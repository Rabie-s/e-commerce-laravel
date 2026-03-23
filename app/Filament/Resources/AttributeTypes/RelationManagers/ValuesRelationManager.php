<?php

namespace App\Filament\Resources\AttributeTypes\RelationManagers;

use App\Filament\Resources\AttributeTypes\AttributeTypeResource;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    protected static ?string $relatedResource = AttributeTypeResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('value')->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('value')
                    ->label('Value'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('value'),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
