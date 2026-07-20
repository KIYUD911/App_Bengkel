<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'spare_part_id',
        'quantity',
        'unit_price',
        'subtotal',
        'warranty_days',
        'warranty_end_date',
    ];

    protected $casts = [
        'quantity'          => 'integer',
        'unit_price'        => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'warranty_days'     => 'integer',
        'warranty_end_date' => 'date',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    // ─── Helper ──────────────────────────────────────────────
    public function isWarrantyActive(): bool
    {
        return $this->warranty_end_date !== null
            && $this->warranty_end_date->isFuture();
    }

    public function warrantyDaysRemaining(): int
    {
        if ($this->warranty_end_date === null) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->warranty_end_date, false));
    }
}
