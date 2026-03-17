<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Collected = 'collected';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Collected => 'Collected',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'orange',
            self::Collected => 'green',
            self::Failed => 'red',
            self::Refunded => 'purple',
        };
    }
}
