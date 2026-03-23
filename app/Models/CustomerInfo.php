<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerInfo extends Model
{
    protected $table = 'customer_info';
    protected $fillable = [
        'first_name',
        'last_name',
        'city',
        'address',
        'nearby_landmark',
        'phone_number',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
