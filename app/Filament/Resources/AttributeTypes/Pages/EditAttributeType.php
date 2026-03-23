<?php

namespace App\Filament\Resources\AttributeTypes\Pages;

use App\Filament\Resources\AttributeTypes\AttributeTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAttributeType extends EditRecord
{
    protected static string $resource = AttributeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
