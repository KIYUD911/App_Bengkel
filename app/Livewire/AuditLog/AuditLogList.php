<?php

namespace App\Livewire\AuditLog;

use App\Models\AuditLog;
use App\Services\AuditTrailService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AuditLogList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $action    = '';
    public string $tableName = '';
    public string $dateFrom  = '';
    public string $dateTo    = '';

    // Expand detail baris
    public ?int $expandedId = null;

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingAction(): void   { $this->resetPage(); }
    public function updatingTableName(): void { $this->resetPage(); }

    public function mount(AuditTrailService $audit): void
    {
        // Log bahwa owner mengakses halaman audit log
        $audit->logCreate(
            tableName: 'audit_log_access',
            recordId: 0,
            newValues: ['page' => 'AuditLogList', 'accessed_at' => now()->toIso8601String()],
            user: auth()->user(),
        );
    }

    #[Computed]
    public function tableNames(): array
    {
        return AuditLog::distinct()
            ->orderBy('table_name')
            ->pluck('table_name')
            ->toArray();
    }

    #[Computed]
    public function logs()
    {
        return AuditLog::when($this->search, fn($q) =>
                $q->where('user_name', 'like', "%{$this->search}%")
            )
            ->when($this->action,    fn($q) => $q->where('action', $this->action))
            ->when($this->tableName, fn($q) => $q->where('table_name', $this->tableName))
            ->when($this->dateFrom,  fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,    fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->paginate(25);
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = ($this->expandedId === $id) ? null : $id;
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $logs = AuditLog::when($this->search, fn($q) => $q->where('user_name', 'like', "%{$this->search}%"))
            ->when($this->action,    fn($q) => $q->where('action', $this->action))
            ->when($this->tableName, fn($q) => $q->where('table_name', $this->tableName))
            ->when($this->dateFrom,  fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,    fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->get();

        $filename = 'audit-log-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'Pengguna', 'Aksi', 'Tabel', 'Record ID', 'IP Address', 'User Agent']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('d/m/Y H:i:s'),
                    $log->user_name,
                    $log->action,
                    $log->table_name,
                    $log->record_id,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }
            fclose($handle);
        }, $filename);
    }

    public function render()
    {
        return view('livewire.audit-log.audit-log-list')
            ->title('Audit Log');
    }
}
