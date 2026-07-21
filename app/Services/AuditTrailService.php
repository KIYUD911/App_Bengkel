<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditTrailService
{
    /**
     * Catat satu audit log entry.
     * TIDAK boleh throw exception — semua error ditelan agar tidak mengganggu flow utama.
     */
    public function log(
        string $action,           // 'CREATE'|'UPDATE'|'DELETE'|'RESTORE'
        string $tableName,
        int $recordId,
        array|null $oldValues,    // null untuk CREATE
        array|null $newValues,    // null untuk DELETE
        ?User $user = null,
    ): void {
        try {
            $actor = $user ?? Auth::user();

            AuditLog::create([
                'user_id'    => $actor?->id,
                'user_name'  => $actor?->name,
                'action'     => $action,
                'table_name' => $tableName,
                'record_id'  => $recordId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Sengaja ditelan — audit log gagal tidak boleh abort transaksi utama
        }
    }

    // ─── Helper Methods ─────────────────────────────────────────

    public function logCreate(
        string $tableName,
        int $recordId,
        array $newValues,
        ?User $user = null,
    ): void {
        $this->log('CREATE', $tableName, $recordId, null, $newValues, $user);
    }

    public function logUpdate(
        string $tableName,
        int $recordId,
        array $oldValues,
        array $newValues,
        ?User $user = null,
    ): void {
        $this->log('UPDATE', $tableName, $recordId, $oldValues, $newValues, $user);
    }

    public function logDelete(
        string $tableName,
        int $recordId,
        array $oldValues,
        ?User $user = null,
    ): void {
        $this->log('DELETE', $tableName, $recordId, $oldValues, null, $user);
    }

    public function logRestore(
        string $tableName,
        int $recordId,
        array $restoredValues,
        ?User $user = null,
    ): void {
        $this->log('RESTORE', $tableName, $recordId, null, $restoredValues, $user);
    }
}
