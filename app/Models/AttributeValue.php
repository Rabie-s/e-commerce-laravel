<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    protected $fillable = [
        'value',
        'attribute_type_id',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AttributeType::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attributes');
    }
}
