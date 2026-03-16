<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'status',
        'total_price',
        'customer_info_id',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address',
        'shipping_phone',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_price' => 'decimal:2',
        ];
    }

    public function customerInfo(): BelongsTo
    {
        return $this->belongsTo(CustomerInfo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
