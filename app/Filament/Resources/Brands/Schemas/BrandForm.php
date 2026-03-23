<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_upload')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('brands')
                    ->dehydrated(false)   // ← v5: don't pass to model save
                    ->nullable(),
            ]);
    }
}
