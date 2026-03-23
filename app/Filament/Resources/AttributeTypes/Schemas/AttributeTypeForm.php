<?php

namespace App\Filament\Resources\AttributeTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
