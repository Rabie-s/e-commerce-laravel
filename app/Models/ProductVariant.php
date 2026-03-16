<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductVariant extends Model
{
    protected $fillable = [
        'sku',
        'price',
        'is_default',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_default' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attributes')
            ->with('type');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProductInventoryMovement::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
    }

    protected function stock(): Attribute
    {
        return Attribute::make(
            get: fn() => (int) $this->movements()->sum('quantity')
        );
    }

    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->price ?? $this->product->base_price
        );
    }
}
