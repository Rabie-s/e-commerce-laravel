<?php

namespace App\Enums;

enum MovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Return = 'return';
    case Damaged = 'damaged';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Purchase',
            self::Sale => 'Sale',
            self::Return => 'Return',
            self::Damaged => 'Damaged',
            self::Adjustment => 'Adjustment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Purchase => 'green',
            self::Sale => 'blue',
            self::Return => 'orange',
            self::Damaged => 'red',
            self::Adjustment => 'yellow',
        };
    }
}
