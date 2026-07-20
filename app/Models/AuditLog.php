<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Tabel ini APPEND-ONLY — tidak ada update, tidak ada delete
    public $timestamps = false; // Hanya punya created_at, tidak ada updated_at

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'user_id'    => 'integer',
        'record_id'  => 'integer',
    ];

    // Override boot agar tidak ada operasi update/delete dari model
    protected static function boot(): void
    {
        parent::boot();

        // Cegah update via model
        static::updating(function () {
            return false;
        });

        // Cegah delete via model (audit logs tidak bisa dihapus)
        static::deleting(function () {
            return false;
        });
    }
}
