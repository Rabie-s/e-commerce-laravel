<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'uuid',
        'status',
        'total_price',
        'customer_info_id',
        'tracking_number',
    ];

    /**
     * The route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Boot method to auto-generate UUID.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
                $model->tracking_number = self::generateTrackingNumber();
            }
        });
    }

    private static function generateTrackingNumber(): string
    {
        $year = now()->year;

        // get last order id + 1 padded to 5 digits
        $lastId = self::max('id') ?? 0;
        $number = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        return "ORD-{$year}-{$number}";
        // → ORD-2025-00001
        // → ORD-2025-00042
    }

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

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
