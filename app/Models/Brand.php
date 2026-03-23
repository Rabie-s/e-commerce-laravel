<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Brand extends Model
{

    protected static function booted(): void
    {
        static::deleting(function (Brand $brand) {
            if ($brand->mainImage) {
                \Storage::delete($brand->mainImage->path);
                $brand->mainImage->delete();
            }
        });
    }

    protected $fillable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function images(): MorphOne
    {
        return $this->MorphOne(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
    }
}
