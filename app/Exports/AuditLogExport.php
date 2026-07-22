<?php

namespace App\Exports;

use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class AuditLogExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly ?string $action    = null,
        private readonly ?string $tableName = null,
        private readonly ?string $dateFrom  = null,
        private readonly ?string $dateTo    = null,
    ) {}

    public function collection(): Collection
    {
        return AuditLog::when($this->action,    fn($q) => $q->where('action', $this->action))
            ->when($this->tableName, fn($q) => $q->where('table_name', $this->tableName))
            ->when($this->dateFrom,  fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,    fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->get()
            ->map(fn($log) => [
                'Waktu'       => $log->created_at?->format('d/m/Y H:i:s'),
                'Pengguna'    => $log->user_name ?? 'System',
                'Aksi'        => $log->action,
                'Tabel'       => $log->table_name,
                'Record ID'   => $log->record_id,
                'IP Address'  => $log->ip_address ?? '-',
                'Old Values'  => $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : '-',
                'New Values'  => $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : '-',
            ]);
    }

    public function headings(): array
    {
        return ['Waktu', 'Pengguna', 'Aksi', 'Tabel', 'Record ID', 'IP Address', 'Old Values (JSON)', 'New Values (JSON)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Audit Log';
    }
}
