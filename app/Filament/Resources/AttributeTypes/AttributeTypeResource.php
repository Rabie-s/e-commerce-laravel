<?php

namespace App\Filament\Resources\AttributeTypes;

use App\Filament\Resources\AttributeTypes\Pages\CreateAttributeType;
use App\Filament\Resources\AttributeTypes\Pages\EditAttributeType;
use App\Filament\Resources\AttributeTypes\Pages\ListAttributeTypes;
use App\Filament\Resources\AttributeTypes\Pages\ViewAttributeType;
use App\Filament\Resources\AttributeTypes\Schemas\AttributeTypeForm;
use App\Filament\Resources\AttributeTypes\Schemas\AttributeTypeInfolist;
use App\Filament\Resources\AttributeTypes\Tables\AttributeTypesTable;
use App\Models\AttributeType;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttributeTypeResource extends Resource
{
    protected static ?string $model = AttributeType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static UnitEnum|string|null $navigationGroup = 'Attributes';

    public static function form(Schema $schema): Schema
    {
        return AttributeTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttributeTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributeTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ValuesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttributeTypes::route('/'),
            'create' => CreateAttributeType::route('/create'),
            'view' => ViewAttributeType::route('/{record}'),
            'edit' => EditAttributeType::route('/{record}/edit'),
        ];
    }
}
