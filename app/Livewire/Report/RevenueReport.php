<?php

namespace App\Livewire\Report;

use App\Exports\RevenueExport;
use App\Models\DirectSale;
use App\Models\WorkOrder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class RevenueReport extends Component
{
    public string $period   = 'this_month'; // today|this_week|this_month|last_month|custom
    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->applyPeriod();
    }

    public function updatedPeriod(): void
    {
        $this->applyPeriod();
    }

    private function applyPeriod(): void
    {
        match ($this->period) {
            'today'      => [$this->dateFrom, $this->dateTo] = [today()->toDateString(), today()->toDateString()],
            'this_week'  => [$this->dateFrom, $this->dateTo] = [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'this_month' => [$this->dateFrom, $this->dateTo] = [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'last_month' => [$this->dateFrom, $this->dateTo] = [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
            default      => null, // custom — user input sendiri
        };
    }

    // ─── Computed Properties ──────────────────────────────────

    #[Computed]
    public function summary(): array
    {
        $woTotal = WorkOrder::completed()
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->selectRaw('COUNT(*) as count, SUM(grand_total) as total')
            ->first();

        $dsTotal = DirectSale::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->selectRaw('COUNT(*) as count, SUM(grand_total) as total')
            ->first();

        $woCount = (int)   ($woTotal->count ?? 0);
        $dsCount = (int)   ($dsTotal->count ?? 0);
        $woRev   = (float) ($woTotal->total ?? 0);
        $dsRev   = (float) ($dsTotal->total ?? 0);
        $grand   = $woRev + $dsRev;
        $txCount = $woCount + $dsCount;

        return [
            'grand_total'   => $grand,
            'wo_count'      => $woCount,
            'wo_revenue'    => $woRev,
            'ds_count'      => $dsCount,
            'ds_revenue'    => $dsRev,
            'avg_per_tx'    => $txCount > 0 ? $grand / $txCount : 0,
        ];
    }

    /** Data untuk bar chart (30 hari atau sesuai range) — array [{label, wo, ds}] */
    #[Computed]
    public function chartData(): array
    {
        $from = $this->dateFrom ? \Carbon\Carbon::parse($this->dateFrom) : now()->subDays(29)->startOfDay();
        $to   = $this->dateTo   ? \Carbon\Carbon::parse($this->dateTo)   : now()->endOfDay();

        // Batasi maksimal 60 titik agar chart tidak terlalu penuh
        $diffDays = (int) $from->diffInDays($to);
        $step     = max(1, intdiv($diffDays, 60));

        $points = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $date = $cursor->toDateString();

            $wo = WorkOrder::completed()->whereDate('paid_at', $date)->sum('grand_total');
            $ds = DirectSale::whereDate('paid_at', $date)->sum('grand_total');

            $points[] = [
                'label' => $cursor->format('d/m'),
                'wo'    => (float) $wo,
                'ds'    => (float) $ds,
            ];

            $cursor->addDays($step);
        }

        return $points;
    }

    /** Tabel detail semua transaksi */
    #[Computed]
    public function transactions(): \Illuminate\Support\Collection
    {
        $wos = WorkOrder::with(['customer', 'user'])
            ->completed()
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->get()
            ->map(fn($wo) => [
                'date'    => $wo->paid_at?->format('d/m/Y H:i'),
                'number'  => $wo->wo_number,
                'type'    => 'WO',
                'customer'=> $wo->customer?->name ?? '-',
                'total'   => (float) $wo->grand_total,
                'method'  => ucfirst($wo->payment_method ?? '-'),
                'cashier' => $wo->user?->name ?? '-',
            ]);

        $dss = DirectSale::with(['customer', 'user'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->get()
            ->map(fn($ds) => [
                'date'    => $ds->paid_at?->format('d/m/Y H:i'),
                'number'  => $ds->sale_number,
                'type'    => 'DS',
                'customer'=> $ds->buyer_name,
                'total'   => (float) $ds->grand_total,
                'method'  => ucfirst($ds->payment_method ?? '-'),
                'cashier' => $ds->user?->name ?? '-',
            ]);

        return $wos->merge($dss)->sortByDesc('date')->values();
    }

    // ─── Export Actions ───────────────────────────────────────

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new RevenueExport($this->dateFrom ?: null, $this->dateTo ?: null),
            'laporan-pendapatan-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $transactions = $this->transactions;
        $filename     = 'laporan-pendapatan-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Tanggal', 'No Transaksi', 'Tipe', 'Pelanggan', 'Grand Total', 'Metode Bayar', 'Kasir']);
            foreach ($transactions as $tx) {
                fputcsv($h, [$tx['date'], $tx['number'], $tx['type'], $tx['customer'], $tx['total'], $tx['method'], $tx['cashier']]);
            }
            fclose($h);
        }, $filename);
    }

    public function render()
    {
        return view('livewire.report.revenue-report')->title('Laporan Pendapatan');
    }
}
