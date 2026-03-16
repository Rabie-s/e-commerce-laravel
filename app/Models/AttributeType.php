<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeType extends Model
{
    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
