<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    protected static function booted(): void
    {
        static::deleting(function (Product $product) {

            // ── Delete product images ──────────────────
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            // ── Delete variants and their related data ─
            foreach ($product->variants as $variant) {

                // delete variant images
                foreach ($variant->images as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }

                // detach attribute values from pivot table
                $variant->attributeValues()->detach();

                // delete inventory movements
                $variant->movements()->delete();

                // delete the variant itself
                $variant->delete();
            }
        });
    }

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'brand_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
    }
}
