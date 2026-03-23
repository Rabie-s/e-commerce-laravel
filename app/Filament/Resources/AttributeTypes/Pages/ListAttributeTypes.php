<?php

namespace App\Filament\Resources\AttributeTypes\Pages;

use App\Filament\Resources\AttributeTypes\AttributeTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttributeTypes extends ListRecords
{
    protected static string $resource = AttributeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
