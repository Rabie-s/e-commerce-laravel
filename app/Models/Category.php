<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Category extends Model
{

    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            if ($category->mainImage) {
                \Storage::delete($category->mainImage->path);
                $category->mainImage->delete();
            }
        });
    }

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function images(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
    }
}
