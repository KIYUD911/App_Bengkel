<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wo_number',
        'customer_id',
        'customer_vehicle_id',
        'user_id',
        'complaint',
        'mechanic_notes',
        'status',
        'labour_cost',
        'total_parts_cost',
        'grand_total',
        'payment_method',
        'paid_at',
        'cancelled_at',
        'cancel_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'labour_cost'      => 'decimal:2',
        'total_parts_cost' => 'decimal:2',
        'grand_total'      => 'decimal:2',
        'paid_at'          => 'datetime',
        'cancelled_at'     => 'datetime',
        'status'           => 'string',
        'payment_method'   => 'string',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class, 'customer_vehicle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(CustomerFeedback::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    // ─── Helper ──────────────────────────────────────────────
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }
}
