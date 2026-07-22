<?php

namespace App\Exports;

use App\Models\DirectSale;
use App\Models\WorkOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class RevenueExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo   = null,
    ) {}

    public function collection(): Collection
    {
        $woRows = WorkOrder::with(['customer', 'user'])
            ->completed()
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->get()
            ->map(fn($wo) => [
                'Tanggal'      => $wo->paid_at?->format('d/m/Y H:i') ?? '-',
                'No Transaksi' => $wo->wo_number,
                'Tipe'         => 'Work Order',
                'Pelanggan'    => $wo->customer?->name ?? '-',
                'Grand Total'  => (float) $wo->grand_total,
                'Metode Bayar' => ucfirst($wo->payment_method ?? '-'),
                'Kasir'        => $wo->user?->name ?? '-',
            ]);

        $dsRows = DirectSale::with(['customer', 'user'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->get()
            ->map(fn($ds) => [
                'Tanggal'      => $ds->paid_at?->format('d/m/Y H:i') ?? '-',
                'No Transaksi' => $ds->sale_number,
                'Tipe'         => 'Penjualan Langsung',
                'Pelanggan'    => $ds->buyer_name,
                'Grand Total'  => (float) $ds->grand_total,
                'Metode Bayar' => ucfirst($ds->payment_method ?? '-'),
                'Kasir'        => $ds->user?->name ?? '-',
            ]);

        return $woRows->merge($dsRows)->sortByDesc('Tanggal')->values();
    }

    public function headings(): array
    {
        return ['Tanggal', 'No Transaksi', 'Tipe', 'Pelanggan', 'Grand Total (Rp)', 'Metode Bayar', 'Kasir'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }
}
