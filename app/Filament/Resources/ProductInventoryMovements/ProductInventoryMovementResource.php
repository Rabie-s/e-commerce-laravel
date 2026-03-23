<?php

namespace App\Filament\Resources\ProductInventoryMovements;

use App\Filament\Resources\ProductInventoryMovements\Pages\CreateProductInventoryMovement;
use App\Filament\Resources\ProductInventoryMovements\Pages\EditProductInventoryMovement;
use App\Filament\Resources\ProductInventoryMovements\Pages\ListProductInventoryMovements;
use App\Filament\Resources\ProductInventoryMovements\Pages\ViewProductInventoryMovement;
use App\Filament\Resources\ProductInventoryMovements\Schemas\ProductInventoryMovementForm;
use App\Filament\Resources\ProductInventoryMovements\Schemas\ProductInventoryMovementInfolist;
use App\Filament\Resources\ProductInventoryMovements\Tables\ProductInventoryMovementsTable;
use App\Models\ProductInventoryMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductInventoryMovementResource extends Resource
{
    protected static ?string $model = ProductInventoryMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'yes';

    public static function form(Schema $schema): Schema
    {
        return ProductInventoryMovementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInventoryMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductInventoryMovementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductInventoryMovements::route('/'),
            'create' => CreateProductInventoryMovement::route('/create'),
            'view' => ViewProductInventoryMovement::route('/{record}'),
            'edit' => EditProductInventoryMovement::route('/{record}/edit'),
        ];
    }
}
