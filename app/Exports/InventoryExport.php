<?php

namespace App\Exports;

use App\Models\SparePart;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly ?string $category = null) {}

    public function collection(): Collection
    {
        $parts = SparePart::when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('quantity_available')
            ->get();

        // Hitung total terjual per part dari stock_movements
        $sold = StockMovement::where('type', 'OUT')
            ->selectRaw('spare_part_id, SUM(quantity) as total_sold')
            ->groupBy('spare_part_id')
            ->pluck('total_sold', 'spare_part_id');

        return $parts->map(fn($p) => [
            'Kode'         => $p->part_code,
            'Nama'         => $p->name,
            'Kategori'     => $p->category ?? '-',
            'Stok'         => $p->quantity_available,
            'Total Terjual'=> $sold[$p->id] ?? 0,
            'Harga Beli'   => (float) $p->purchase_price,
            'Nilai Stok'   => (float) ($p->purchase_price * $p->quantity_available),
        ]);
    }

    public function headings(): array
    {
        return ['Kode Part', 'Nama Sparepart', 'Kategori', 'Stok Saat Ini', 'Total Terjual', 'Harga Beli (Rp)', 'Nilai Stok (Rp)'];
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            ],
        ];

        // Highlight baris stok kritis (qty < 5) dengan merah muda
        $parts = SparePart::when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('quantity_available')
            ->get();

        foreach ($parts as $i => $part) {
            if ($part->quantity_available < 5) {
                $row = $i + 2; // +2 karena baris 1 adalah header
                $styles[$row] = [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFECACA']],
                ];
            }
        }

        return $styles;
    }

    public function title(): string
    {
        return 'Laporan Inventori';
    }
}
