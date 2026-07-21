<?php

namespace App\Services;

use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\SparePart;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DirectSaleService
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly InventoryService $inventory,
        private readonly CRMService $crm,
    ) {}

    /**
     * Buat penjualan langsung (POS).
     * Semua operasi dalam 1 DB::transaction.
     *
     * @param array $data  { customer_id, walk_in_name, payment_method, notes }
     * @param array $items [ { spare_part_id, quantity }, ... ]
     */
    public function createDirectSale(array $data, array $items, User $user): DirectSale
    {
        return DB::transaction(function () use ($data, $items, $user) {
            $grandTotal = 0;
            $itemsData  = [];

            // Validasi & kalkulasi harga tiap item
            foreach ($items as $item) {
                $part     = SparePart::findOrFail($item['spare_part_id']);
                $qty      = (int) $item['quantity'];
                $subtotal = $part->selling_price * $qty;

                $grandTotal += $subtotal;

                $itemsData[] = [
                    'spare_part' => $part,
                    'qty'        => $qty,
                    'unit_price' => $part->selling_price,
                    'subtotal'   => $subtotal,
                ];
            }

            // Buat header penjualan
            $sale = DirectSale::create([
                'sale_number'    => $this->generateSaleNumber(),
                'customer_id'    => $data['customer_id'] ?? null,
                'walk_in_name'   => $data['walk_in_name'] ?? null,
                'user_id'        => $user->id,
                'grand_total'    => $grandTotal,
                'payment_method' => $data['payment_method'],
                'paid_at'        => now(),
                'notes'          => $data['notes'] ?? null,
            ]);

            // Buat item + deduct stok
            foreach ($itemsData as $itemData) {
                DirectSaleItem::create([
                    'direct_sale_id' => $sale->id,
                    'spare_part_id'  => $itemData['spare_part']->id,
                    'quantity'       => $itemData['qty'],
                    'unit_price'     => $itemData['unit_price'],
                    'subtotal'       => $itemData['subtotal'],
                ]);

                $this->inventory->deductStock(
                    part: $itemData['spare_part'],
                    qty: $itemData['qty'],
                    reason: 'direct_sale',
                    user: $user,
                    directSaleId: $sale->id,
                );
            }

            // Update statistik pelanggan terdaftar
            if ($sale->customer_id && $sale->customer) {
                $this->crm->updateCustomerStats($sale->customer, $grandTotal);
            }

            $this->audit->logCreate(
                tableName: 'direct_sales',
                recordId: $sale->id,
                newValues: $sale->toArray(),
                user: $user,
            );

            return $sale->load('items.sparePart');
        });
    }

    /**
     * Generate PDF struk penjualan langsung.
     */
    public function generateInvoicePdf(DirectSale $sale): Response
    {
        $sale->load(['items.sparePart', 'customer', 'user']);

        $pdf = Pdf::loadView('pdf.direct-sale-receipt', compact('sale'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // ~80mm thermal paper

        return $pdf->download("struk-{$sale->sale_number}.pdf");
    }

    // ─── Private ─────────────────────────────────────────────────

    private function generateSaleNumber(): string
    {
        $maxId = DB::table('direct_sales')->lockForUpdate()->max('id') ?? 0;
        return 'DS-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
    }
}
