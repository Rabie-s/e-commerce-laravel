<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventoryMovement extends Model
{
    public const array POSITIVE_TYPES = [
        MovementType::Purchase,
        MovementType::Return,
    ];

    public const array NEGATIVE_TYPES = [
        MovementType::Sale,
        MovementType::Damaged,
    ];

    protected $fillable = [
        'type',
        'quantity',
        'product_variant_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => MovementType::class,
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
