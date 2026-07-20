<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'direct_sale_id',
        'spare_part_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    // ─── Relasi ──────────────────────────────────────────────
    public function directSale(): BelongsTo
    {
        return $this->belongsTo(DirectSale::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
