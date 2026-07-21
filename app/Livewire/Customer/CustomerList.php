<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool   $vipOnly = false;

    public function updatingSearch(): void { $this->resetPage(); }

    #[Computed]
    public function customers()
    {
        return Customer::when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
            )
            ->when($this->vipOnly, fn($q) => $q->where('is_vip', true))
            ->withCount('workOrders')
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.customer.customer-list')
            ->title('Data Pelanggan');
    }
}
