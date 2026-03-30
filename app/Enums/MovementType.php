<?php

namespace App\Enums;

enum MovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Return = 'return';
    case Damaged = 'damaged';
    //case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Purchase',
            self::Sale => 'Sale',
            self::Return => 'Return',
            self::Damaged => 'Damaged',
            //self::Adjustment => 'Adjustment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Purchase => 'success',
            self::Sale => 'info',
            self::Return => 'warning',
            self::Damaged => 'danger',
           // self::Adjustment => 'warning',
        };
    }
}
