<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SparePart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_code',
        'name',
        'category',
        'purchase_price',
        'selling_price',
        'quantity_available',
        'unit',
    ];

    protected $casts = [
        'purchase_price'     => 'decimal:2',
        'selling_price'      => 'decimal:2',
        'quantity_available' => 'integer',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function workOrderItems(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function directSaleItems(): HasMany
    {
        return $this->hasMany(DirectSaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────
    public function scopeLowStock(Builder $query, int $threshold = 5): Builder
    {
        return $query->where('quantity_available', '<=', $threshold);
    }
}
