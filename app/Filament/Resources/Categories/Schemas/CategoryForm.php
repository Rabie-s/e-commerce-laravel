<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->description('Create and organize your product categories')
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->placeholder('e.g. Electronics, Clothing, Accessories')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autocomplete(false)
                            ->helperText('Enter a descriptive name for this category'),

                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->placeholder('Select a parent category (optional)')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Choose a parent category to create a hierarchy. Leave empty for top-level categories')
                            ->allowHtml(false)
                            ->native(false),

                        FileUpload::make('image_upload')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('categories')
                            ->dehydrated(false)   // ← v5: don't pass to model save
                            ->nullable(),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
