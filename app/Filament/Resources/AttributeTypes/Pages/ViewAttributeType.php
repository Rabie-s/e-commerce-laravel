<?php

namespace App\Filament\Resources\AttributeTypes\Pages;

use App\Filament\Resources\AttributeTypes\AttributeTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttributeType extends ViewRecord
{
    protected static string $resource = AttributeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
