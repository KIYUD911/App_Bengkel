<?php

namespace App\Services;

use App\Exceptions\InvalidWorkOrderStatusException;
use App\Models\Customer;
use App\Models\CustomerFeedback;
use App\Models\CustomerVehicle;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CRMService
{
    public function __construct(
        private readonly AuditTrailService $audit,
    ) {}

    /**
     * Buat pelanggan baru.
     */
    public function createCustomer(array $data, User $user): Customer
    {
        return DB::transaction(function () use ($data, $user) {
            $customer = Customer::create($data);

            $this->audit->logCreate(
                tableName: 'customers',
                recordId: $customer->id,
                newValues: $customer->toArray(),
                user: $user,
            );

            return $customer;
        });
    }

    /**
     * Update data pelanggan.
     */
    public function updateCustomer(Customer $customer, array $data, User $user): Customer
    {
        return DB::transaction(function () use ($customer, $data, $user) {
            $oldValues = $customer->toArray();
            $customer->update($data);

            $this->audit->logUpdate(
                tableName: 'customers',
                recordId: $customer->id,
                oldValues: $oldValues,
                newValues: $customer->fresh()->toArray(),
                user: $user,
            );

            return $customer->fresh();
        });
    }

    /**
     * Tambah kendaraan baru untuk pelanggan.
     */
    public function addVehicle(Customer $customer, array $data, User $user): CustomerVehicle
    {
        return DB::transaction(function () use ($customer, $data, $user) {
            $vehicle = $customer->vehicles()->create($data);

            $this->audit->logCreate(
                tableName: 'customer_vehicles',
                recordId: $vehicle->id,
                newValues: $vehicle->toArray(),
                user: $user,
            );

            return $vehicle;
        });
    }

    /**
     * Update statistik pelanggan setelah transaksi selesai.
     * Cek VIP: total_spent >= 1.000.000 → is_vip = true
     */
    public function updateCustomerStats(Customer $customer, float $amount): void
    {
        $customer->increment('visit_count');
        $customer->increment('total_spent', $amount);
        $customer->refresh();

        if (! $customer->is_vip && $customer->total_spent >= 1_000_000) {
            $customer->update(['is_vip' => true]);
        }
    }

    /**
     * Simpan feedback pelanggan untuk sebuah WO.
     * Validasi: WO harus completed & belum ada feedback.
     */
    public function submitFeedback(WorkOrder $wo, int $rating, ?string $comment, User $user): CustomerFeedback
    {
        if ($wo->status !== 'completed') {
            throw new InvalidWorkOrderStatusException(
                currentStatus: $wo->status,
                message: 'Feedback hanya dapat diberikan untuk WO yang sudah selesai.',
            );
        }

        if ($wo->feedback()->exists()) {
            throw new \RuntimeException('Feedback untuk Work Order ini sudah diberikan sebelumnya.');
        }

        return DB::transaction(function () use ($wo, $rating, $comment, $user) {
            $feedback = CustomerFeedback::create([
                'work_order_id' => $wo->id,
                'customer_id'   => $wo->customer_id,
                'rating'        => $rating,
                'comment'       => $comment,
            ]);

            $this->audit->logCreate(
                tableName: 'customer_feedbacks',
                recordId: $feedback->id,
                newValues: $feedback->toArray(),
                user: $user,
            );

            return $feedback;
        });
    }

    /**
     * Ambil semua garansi aktif milik pelanggan (warranty_end_date >= hari ini).
     */
    public function getActiveWarranties(Customer $customer): Collection
    {
        return \App\Models\WorkOrderItem::query()
            ->whereHas('workOrder', fn($q) => $q->where('customer_id', $customer->id))
            ->where('warranty_end_date', '>=', today())
            ->with(['workOrder', 'sparePart'])
            ->get();
    }
}
