<?php

namespace App\Filament\Resources\ProductInventoryMovements\Pages;

use App\Filament\Resources\ProductInventoryMovements\ProductInventoryMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProductInventoryMovement extends EditRecord
{
    protected static string $resource = ProductInventoryMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
