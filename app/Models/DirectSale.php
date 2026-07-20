<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class DirectSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'walk_in_name',
        'user_id',
        'grand_total',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'grand_total'    => 'decimal:2',
        'paid_at'        => 'datetime',
        'payment_method' => 'string',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DirectSaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    // ─── Helper ──────────────────────────────────────────────
    public function getBuyerNameAttribute(): string
    {
        return $this->customer?->name ?? $this->walk_in_name ?? 'Walk-in';
    }
}
