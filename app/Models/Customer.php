<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'visit_count',
        'total_spent',
        'is_vip',
    ];

    protected $casts = [
        'visit_count' => 'integer',
        'total_spent' => 'decimal:2',
        'is_vip'      => 'boolean',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function vehicles(): HasMany
    {
        return $this->hasMany(CustomerVehicle::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function directSales(): HasMany
    {
        return $this->hasMany(DirectSale::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────
    public function scopeVip(Builder $query): Builder
    {
        return $query->where('is_vip', true);
    }
}
