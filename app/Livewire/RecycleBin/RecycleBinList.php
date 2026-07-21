<?php

namespace App\Livewire\RecycleBin;

use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Services\AuditTrailService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class RecycleBinList extends Component
{
    public string $activeTab = 'customers';

    // Konfirmasi hapus permanen
    public bool    $showConfirm      = false;
    public ?int    $confirmId        = null;
    public string  $confirmType      = '';
    public string  $confirmName      = '';

    // ─── Computed per tab ─────────────────────────────────────

    #[Computed]
    public function deletedCustomers()
    {
        return Customer::onlyTrashed()->latest('deleted_at')->get();
    }

    #[Computed]
    public function deletedVehicles()
    {
        return CustomerVehicle::onlyTrashed()->with('customer')->latest('deleted_at')->get();
    }

    #[Computed]
    public function deletedSpareParts()
    {
        return SparePart::onlyTrashed()->latest('deleted_at')->get();
    }

    #[Computed]
    public function deletedWorkOrders()
    {
        return WorkOrder::onlyTrashed()->with('customer')->latest('deleted_at')->get();
    }

    // ─── Restore ──────────────────────────────────────────────

    public function restore(int $id, string $type, AuditTrailService $audit): void
    {
        $model = $this->resolveModel($type, $id);
        if (!$model) return;

        $model->restore();

        $audit->logRestore(
            tableName: $type,
            recordId: $id,
            restoredValues: $model->toArray(),
            user: auth()->user(),
        );

        $this->dispatch('notify', type: 'success', message: "Data berhasil dipulihkan.");
        $this->clearComputed();
    }

    // ─── Force Delete ─────────────────────────────────────────

    public function confirmForceDelete(int $id, string $type, string $name): void
    {
        $this->confirmId   = $id;
        $this->confirmType = $type;
        $this->confirmName = $name;
        $this->showConfirm = true;
    }

    public function forceDelete(AuditTrailService $audit): void
    {
        if (!$this->confirmId || !$this->confirmType) return;

        $model = $this->resolveModel($this->confirmType, $this->confirmId);
        if (!$model) {
            $this->showConfirm = false;
            return;
        }

        $audit->logDelete(
            tableName: $this->confirmType,
            recordId: $this->confirmId,
            oldValues: $model->toArray(),
            user: auth()->user(),
        );

        $model->forceDelete();

        $this->dispatch('notify', type: 'success', message: "Data dihapus permanen.");
        $this->showConfirm = false;
        $this->confirmId   = null;
        $this->clearComputed();
    }

    public function cancelConfirm(): void
    {
        $this->showConfirm = false;
        $this->confirmId   = null;
    }

    // ─── Helper ───────────────────────────────────────────────

    private function resolveModel(string $type, int $id): ?object
    {
        return match($type) {
            'customers'        => Customer::withTrashed()->find($id),
            'customer_vehicles'=> CustomerVehicle::withTrashed()->find($id),
            'spare_parts'      => SparePart::withTrashed()->find($id),
            'work_orders'      => WorkOrder::withTrashed()->find($id),
            default            => null,
        };
    }

    private function clearComputed(): void
    {
        unset(
            $this->deletedCustomers,
            $this->deletedVehicles,
            $this->deletedSpareParts,
            $this->deletedWorkOrders,
        );
    }

    public function render()
    {
        return view('livewire.recycle-bin.recycle-bin-list')
            ->title('Recycle Bin');
    }
}
