<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => 'string',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function cancelledWorkOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'cancelled_by');
    }

    public function directSales(): HasMany
    {
        return $this->hasMany(DirectSale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── Helper ──────────────────────────────────────────────
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isStafGudang(): bool
    {
        return $this->role === 'staf_gudang';
    }
}
