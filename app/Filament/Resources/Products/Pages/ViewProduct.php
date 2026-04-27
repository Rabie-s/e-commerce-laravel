<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load(['variants.images', 'variants.attributeValues.type']);

        return $data;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(3)
                    ->schema([

                        Group::make()
                            ->columnSpan(2)
                            ->schema([

                                Section::make('Product Details')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Name'),

                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Variants')
                                    ->schema([
                                        RepeatableEntry::make('variants')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('sku')
                                                    ->label('SKU')
                                                    ->badge(),

                                                TextEntry::make('price')
                                                    ->label('Price')
                                                    ->money('USD'),

                                                TextEntry::make('stock')
                                                    ->label('Stock')
                                                    ->badge()
                                                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                                                TextEntry::make('attributeValues')
                                                    ->label('Attributes')
                                                    ->badge()
                                                    ->getStateUsing(fn ($record) => $record->attributeValues
                                                        ->map(fn ($av) => "{$av->type->name}: {$av->value}")
                                                        ->toArray()
                                                    ),

                                                ImageEntry::make('images')
                                                    ->label('Images')
                                                    ->getStateUsing(fn ($record) => $record->images->pluck('path')->toArray())
                                                    ->disk('public')
                                                    ->height(60)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(4),
                                    ]),

                            ]),

                        Group::make()
                            ->columnSpan(1)
                            ->schema([

                                Section::make('Organisation')
                                    ->schema([
                                        TextEntry::make('category.name')
                                            ->label('Category')
                                            ->badge(),

                                        TextEntry::make('brand.name')
                                            ->label('Brand'),
                                    ]),

                                Section::make('Images')
                                    ->schema([
                                        ImageEntry::make('images')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => $record->images->pluck('path')->toArray()
                                            )
                                            ->disk('public')
                                            ->height(80),
                                    ]),

                            ]),

                    ])->columnSpanFull(),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
