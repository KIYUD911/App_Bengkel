<?php

namespace App\Livewire\DirectSale;

use App\Models\DirectSale;
use App\Services\DirectSaleService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DirectSaleList extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $dateFrom = '';
    public string $dateTo   = '';

    public function updatingSearch(): void  { $this->resetPage(); }

    #[Computed]
    public function sales()
    {
        return DirectSale::with(['customer', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('sale_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                  ->orWhere('walk_in_name', 'like', "%{$this->search}%")
            )
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);
    }

    public function downloadReceipt(int $saleId, DirectSaleService $service)
    {
        $sale = DirectSale::with(['items.sparePart', 'customer', 'user'])->findOrFail($saleId);
        return $service->generateInvoicePdf($sale);
    }

    public function render()
    {
        return view('livewire.direct-sale.direct-sale-list')
            ->title('Riwayat Penjualan Langsung');
    }
}
