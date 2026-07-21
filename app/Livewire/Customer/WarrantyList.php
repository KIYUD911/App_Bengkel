<?php

namespace App\Livewire\Customer;

use App\Models\WorkOrderItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WarrantyList extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterStatus = 'active'; // 'active' | 'all' | 'expired'

    public function updatingSearch(): void { $this->resetPage(); }

    #[Computed]
    public function warranties()
    {
        return WorkOrderItem::query()
            ->with(['workOrder.customer', 'sparePart'])
            ->whereNotNull('warranty_end_date')
            ->when($this->filterStatus === 'active',  fn($q) => $q->where('warranty_end_date', '>=', today()))
            ->when($this->filterStatus === 'expired', fn($q) => $q->where('warranty_end_date', '<',  today()))
            ->when($this->search, fn($q) =>
                $q->whereHas('sparePart', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('workOrder.customer', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
            )
            ->orderBy('warranty_end_date')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.customer.warranty-list')
            ->title('Manajemen Garansi');
    }
}
