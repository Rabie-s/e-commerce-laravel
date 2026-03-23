<?php

namespace App\Filament\Resources\ProductInventoryMovements\Pages;

use App\Filament\Resources\ProductInventoryMovements\ProductInventoryMovementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductInventoryMovement extends ViewRecord
{
    protected static string $resource = ProductInventoryMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
