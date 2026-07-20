<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'spare_part_id',
        'work_order_id',
        'direct_sale_id',
        'type',
        'quantity',
        'reason',
        'user_id',
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'type'            => 'string',
        'spare_part_id'   => 'integer',
        'work_order_id'   => 'integer',
        'direct_sale_id'  => 'integer',
        'user_id'         => 'integer',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function directSale(): BelongsTo
    {
        return $this->belongsTo(DirectSale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
