<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'customer_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating'        => 'integer',
        'work_order_id' => 'integer',
        'customer_id'   => 'integer',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ─── Helper ──────────────────────────────────────────────
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
