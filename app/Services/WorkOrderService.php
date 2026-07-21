<?php

namespace App\Services;

use App\Exceptions\InvalidWorkOrderStatusException;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly InventoryService $inventory,
        private readonly CRMService $crm,
    ) {}

    /**
     * Buat Work Order baru.
     * WO number di-generate dengan DB lock untuk cegah race condition.
     */
    public function createWorkOrder(array $data, User $user): WorkOrder
    {
        return DB::transaction(function () use ($data, $user) {
            $wo = WorkOrder::create([
                'wo_number'           => $this->generateWoNumber(),
                'customer_id'         => $data['customer_id'],
                'customer_vehicle_id' => $data['customer_vehicle_id'],
                'user_id'             => $user->id,
                'complaint'           => $data['complaint'],
                'mechanic_notes'      => $data['mechanic_notes'] ?? null,
                'status'              => 'pending',
                'labour_cost'         => $data['labour_cost'] ?? 0,
                'total_parts_cost'    => 0,
                'grand_total'         => $data['labour_cost'] ?? 0,
            ]);

            $this->audit->logCreate(
                tableName: 'work_orders',
                recordId: $wo->id,
                newValues: $wo->toArray(),
                user: $user,
            );

            return $wo;
        });
    }

    /**
     * Tambah item sparepart ke WO.
     * Deduct stok, snapshot harga, hitung ulang grand_total.
     */
    public function addItem(WorkOrder $wo, SparePart $part, int $qty, User $user): WorkOrderItem
    {
        if (! in_array($wo->status, ['pending', 'in_progress'])) {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'Item hanya dapat ditambahkan pada WO dengan status pending atau in_progress.',
            );
        }

        return DB::transaction(function () use ($wo, $part, $qty, $user) {
            // Deduct stok
            $this->inventory->deductStock(
                part: $part,
                qty: $qty,
                reason: 'service_usage',
                user: $user,
                workOrderId: $wo->id,
            );

            $unitPrice = $part->selling_price;
            $subtotal  = $unitPrice * $qty;

            // Hitung warranty_end_date
            $warrantyDays    = $part->warranty_days ?? 0;
            $warrantyEndDate = $warrantyDays > 0
                ? now()->addDays($warrantyDays)->toDateString()
                : null;

            $item = WorkOrderItem::create([
                'work_order_id'    => $wo->id,
                'spare_part_id'    => $part->id,
                'quantity'         => $qty,
                'unit_price'       => $unitPrice,
                'subtotal'         => $subtotal,
                'warranty_days'    => $warrantyDays,
                'warranty_end_date' => $warrantyEndDate,
            ]);

            $this->recalculateTotal($wo);

            $this->audit->logCreate(
                tableName: 'work_order_items',
                recordId: $item->id,
                newValues: $item->toArray(),
                user: $user,
            );

            return $item;
        });
    }

    /**
     * Hapus item dari WO dan rollback stok.
     */
    public function removeItem(WorkOrderItem $item, User $user): void
    {
        $wo = $item->workOrder;

        if ($wo->status === 'completed') {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'Item tidak dapat dihapus dari WO yang sudah selesai.',
            );
        }

        DB::transaction(function () use ($item, $wo, $user) {
            // Rollback stok
            $this->inventory->addStock(
                part: $item->sparePart,
                qty: $item->quantity,
                reason: 'item_removed',
                user: $user,
                workOrderId: $wo->id,
            );

            $oldValues = $item->toArray();
            $item->delete();

            $this->recalculateTotal($wo);

            $this->audit->logDelete(
                tableName: 'work_order_items',
                recordId: $item->id,
                oldValues: $oldValues,
                user: $user,
            );
        });
    }

    /**
     * Update status WO dengan validasi transisi.
     * Transisi valid: pending→in_progress, in_progress→completed
     * Cancel harus lewat cancelWorkOrder()
     */
    public function updateStatus(WorkOrder $wo, string $newStatus, User $user): WorkOrder
    {
        $validTransitions = [
            'pending'     => ['in_progress'],
            'in_progress' => ['completed'],
        ];

        if (! isset($validTransitions[$wo->status]) ||
            ! in_array($newStatus, $validTransitions[$wo->status])
        ) {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                attemptedStatus: $newStatus,
            );
        }

        $oldValues = $wo->only(['status']);
        $wo->update(['status' => $newStatus]);

        $this->audit->logUpdate(
            tableName: 'work_orders',
            recordId: $wo->id,
            oldValues: $oldValues,
            newValues: $wo->only(['status']),
            user: $user,
        );

        return $wo->fresh();
    }

    /**
     * Cancel Work Order — METHOD TERPENTING.
     * Rollback stok semua item, update WO, semua dalam 1 transaction.
     */
    public function cancelWorkOrder(WorkOrder $wo, string $reason, User $cancelledBy): WorkOrder
    {
        if ($wo->status === 'completed') {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'WO yang sudah selesai tidak dapat dibatalkan.',
            );
        }

        if (! in_array($wo->status, ['pending', 'in_progress'])) {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'Status WO tidak valid untuk proses cancel.',
            );
        }

        return DB::transaction(function () use ($wo, $reason, $cancelledBy) {
            $oldValues = $wo->only(['status', 'cancelled_at', 'cancel_reason', 'cancelled_by']);

            // a. Rollback stok semua item
            $wo->load('items.sparePart');
            $this->inventory->rollbackWorkOrderStock($wo, $cancelledBy);

            // b. Update WO menjadi cancelled
            $wo->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'cancelled_by' => $cancelledBy->id,
            ]);

            // c. Audit trail
            $this->audit->logUpdate(
                tableName: 'work_orders',
                recordId: $wo->id,
                oldValues: $oldValues,
                newValues: $wo->fresh()->only(['status', 'cancelled_at', 'cancel_reason', 'cancelled_by']),
                user: $cancelledBy,
            );

            return $wo->fresh();
        });
    }

    /**
     * Proses pembayaran WO (harus sudah completed).
     */
    public function processPayment(WorkOrder $wo, string $method, User $user): WorkOrder
    {
        if ($wo->status !== 'completed') {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'Hanya WO dengan status completed yang dapat diproses pembayarannya.',
            );
        }

        return DB::transaction(function () use ($wo, $method, $user) {
            $oldValues = $wo->only(['payment_method', 'paid_at']);

            $wo->update([
                'payment_method' => $method,
                'paid_at'        => now(),
            ]);

            // Update statistik pelanggan
            if ($wo->customer_id) {
                $this->crm->updateCustomerStats($wo->customer, $wo->grand_total);
            }

            $this->audit->logUpdate(
                tableName: 'work_orders',
                recordId: $wo->id,
                oldValues: $oldValues,
                newValues: $wo->fresh()->only(['payment_method', 'paid_at']),
                user: $user,
            );

            return $wo->fresh();
        });
    }

    // ─── Private Helpers ─────────────────────────────────────────

    private function generateWoNumber(): string
    {
        $maxId = DB::table('work_orders')->lockForUpdate()->max('id') ?? 0;
        return 'WO-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
    }

    private function recalculateTotal(WorkOrder $wo): void
    {
        $totalParts = $wo->items()->sum('subtotal');
        $wo->update([
            'total_parts_cost' => $totalParts,
            'grand_total'      => $wo->labour_cost + $totalParts,
        ]);
    }
}
