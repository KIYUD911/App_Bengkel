<?php

namespace App\Livewire\Report;

use App\Exports\InventoryExport;
use App\Models\SparePart;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class InventoryReport extends Component
{
    public string $category = '';

    #[Computed]
    public function categories(): array
    {
        return SparePart::distinct()->whereNotNull('category')->orderBy('category')->pluck('category')->toArray();
    }

    /** Top 10 terlaris dari stock_movements OUT */
    #[Computed]
    public function topSelling(): \Illuminate\Support\Collection
    {
        return StockMovement::where('type', 'OUT')
            ->selectRaw('spare_part_id, SUM(quantity) as total_sold')
            ->groupBy('spare_part_id')
            ->orderByDesc('total_sold')
            ->take(10)
            ->with('sparePart')
            ->get()
            ->map(fn($m) => [
                'name'       => $m->sparePart?->name ?? '-',
                'code'       => $m->sparePart?->part_code ?? '-',
                'total_sold' => (int) $m->total_sold,
                'stock'      => $m->sparePart?->quantity_available ?? 0,
            ]);
    }

    /** Semua sparepart dengan nilai stok */
    #[Computed]
    public function allParts(): \Illuminate\Database\Eloquent\Collection
    {
        $sold = StockMovement::where('type', 'OUT')
            ->selectRaw('spare_part_id, SUM(quantity) as total_sold')
            ->groupBy('spare_part_id')
            ->pluck('total_sold', 'spare_part_id');

        return SparePart::when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('quantity_available')
            ->get()
            ->each(function ($p) use ($sold) {
                $p->total_sold  = $sold[$p->id] ?? 0;
                $p->stock_value = $p->purchase_price * $p->quantity_available;
            });
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new InventoryExport($this->category ?: null),
            'laporan-inventori-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.report.inventory-report')->title('Laporan Inventori');
    }
}
