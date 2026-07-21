<?php

namespace App\Livewire\WorkOrder;

use App\Models\WorkOrder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WorkOrderList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $status    = '';
    public string $dateFrom  = '';
    public string $dateTo    = '';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingStatus(): void  { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void  { $this->resetPage(); }

    #[Computed]
    public function workOrders()
    {
        return WorkOrder::with(['customer', 'vehicle', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('wo_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($q2) =>
                      $q2->where('name', 'like', "%{$this->search}%")
                  )
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.work-order.work-order-list')
            ->title('Daftar Work Order');
    }
}
