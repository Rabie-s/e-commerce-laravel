<?php

namespace App\Filament\Resources\ProductInventoryMovements\Pages;

use App\Filament\Resources\ProductInventoryMovements\ProductInventoryMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductInventoryMovements extends ListRecords
{
    protected static string $resource = ProductInventoryMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
