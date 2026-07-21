<?php

namespace App\Livewire\SparePart;

use App\Models\SparePart;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SparePartList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $category  = '';
    public bool   $lowStockOnly = false;

    public function updatingSearch(): void { $this->resetPage(); }

    #[Computed]
    public function categories(): array
    {
        return SparePart::whereNotNull('category')
            ->distinct()->orderBy('category')
            ->pluck('category')->toArray();
    }

    #[Computed]
    public function spareParts()
    {
        return SparePart::when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('part_code', 'like', "%{$this->search}%")
            )
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->when($this->lowStockOnly, fn($q) => $q->where('quantity_available', '<=', 5))
            ->orderBy('quantity_available')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.spare-part.spare-part-list')
            ->title('Manajemen Sparepart');
    }
}
