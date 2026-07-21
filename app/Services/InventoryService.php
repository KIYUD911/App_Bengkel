<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        private readonly AuditTrailService $audit,
    ) {}

    /**
     * Tambah stok dan catat mutasi IN.
     */
    public function addStock(
        SparePart $part,
        int $qty,
        string $reason,
        User $user,
        ?int $workOrderId = null,
        ?int $directSaleId = null,
    ): StockMovement {
        return DB::transaction(function () use ($part, $qty, $reason, $user, $workOrderId, $directSaleId) {
            $oldQty = $part->quantity_available;

            $part->increment('quantity_available', $qty);
            $part->refresh();

            $movement = StockMovement::create([
                'spare_part_id'  => $part->id,
                'work_order_id'  => $workOrderId,
                'direct_sale_id' => $directSaleId,
                'type'           => 'IN',
                'quantity'       => $qty,
                'reason'         => $reason,
                'user_id'        => $user->id,
            ]);

            $this->audit->logUpdate(
                tableName: 'spare_parts',
                recordId: $part->id,
                oldValues: ['quantity_available' => $oldQty],
                newValues: ['quantity_available' => $part->quantity_available],
                user: $user,
            );

            return $movement;
        });
    }

    /**
     * Kurangi stok dan catat mutasi OUT.
     * Throws InsufficientStockException jika stok tidak mencukupi.
     */
    public function deductStock(
        SparePart $part,
        int $qty,
        string $reason,
        User $user,
        ?int $workOrderId = null,
        ?int $directSaleId = null,
    ): StockMovement {
        return DB::transaction(function () use ($part, $qty, $reason, $user, $workOrderId, $directSaleId) {
            // Lock row untuk cegah race condition
            $part = SparePart::lockForUpdate()->findOrFail($part->id);

            if ($part->quantity_available < $qty) {
                throw new InsufficientStockException(
                    partName: $part->name,
                    availableQty: $part->quantity_available,
                    requestedQty: $qty,
                );
            }

            $oldQty = $part->quantity_available;

            $part->decrement('quantity_available', $qty);
            $part->refresh();

            $movement = StockMovement::create([
                'spare_part_id'  => $part->id,
                'work_order_id'  => $workOrderId,
                'direct_sale_id' => $directSaleId,
                'type'           => 'OUT',
                'quantity'       => $qty,
                'reason'         => $reason,
                'user_id'        => $user->id,
            ]);

            $this->audit->logUpdate(
                tableName: 'spare_parts',
                recordId: $part->id,
                oldValues: ['quantity_available' => $oldQty],
                newValues: ['quantity_available' => $part->quantity_available],
                user: $user,
            );

            return $movement;
        });
    }

    /**
     * Rollback stok semua item saat WO di-cancel.
     * WAJIB dipanggil dalam DB::transaction() yang sama dengan proses cancel.
     */
    public function rollbackWorkOrderStock(WorkOrder $wo, User $user): void
    {
        foreach ($wo->items as $item) {
            $this->addStock(
                part: $item->sparePart,
                qty: $item->quantity,
                reason: 'wo_cancel_rollback',
                user: $user,
                workOrderId: $wo->id,
            );
        }
    }

    /**
     * Ambil daftar item dengan stok kritis.
     */
    public function getLowStockItems(int $threshold = 5): Collection
    {
        return SparePart::lowStock($threshold)
            ->orderBy('quantity_available')
            ->get();
    }
}
