<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\AttributeValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([

                        Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(3)->schema([

                                    // ── Left side (wide) ──────────────────────
                                    Group::make()
                                        ->columnSpan(2)
                                        ->schema([

                                            Section::make('Product Details')
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpanFull(),

                                                    RichEditor::make('description')
                                                        ->columnSpanFull(),
                                                ]),
                                            Section::make('Images')
                                                ->schema([
                                                    FileUpload::make('uploaded_images')
                                                        ->label('Product Images')
                                                        ->disk('public')
                                                        ->multiple()
                                                        ->image()
                                                        ->imageEditor()
                                                        ->reorderable()
                                                        ->maxFiles(10)
                                                        ->directory('products')
                                                        ->columnSpanFull(),
                                                ]),

                                        ]),

                                    // ── Right side (narrow) ───────────────────
                                    Group::make()
                                        ->columnSpan(1)
                                        ->schema([

                                            Section::make('Organisation')
                                                ->schema([
                                                    Select::make('category_id')
                                                        ->relationship('category', 'name')
                                                        ->searchable()
                                                        ->preload()
                                                        ->required(),

                                                    Select::make('brand_id')
                                                        ->relationship('brand', 'name')
                                                        ->searchable()
                                                        ->preload()
                                                        ->required(),
                                                ]),

                                        ]),

                                ]),
                            ]),

                        Tab::make('Variants')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Repeater::make('variants')
                                    ->label('')
                                    ->addActionLabel('+ Add Variant')
                                    ->collapsible()
                                    ->cloneable()
                                    ->defaultItems(1)
                                    ->itemLabel(fn (array $state): string =>
                                    filled($state['sku'] ?? null)
                                        ? "SKU: {$state['sku']} — ${$state['price']}"
                                        : 'New Variant'
                                    )
                                    ->schema([
                                        Hidden::make('id'),

                                        // ── Variant Info ───────────────────────────
                                        Section::make('Variant Info')
                                            ->icon('heroicon-o-tag')
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->placeholder('e.g. SHIRT-L-RED')
                                                    ->prefixIcon('heroicon-o-hashtag'),

                                                TextInput::make('price')
                                                    ->label('Price')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('0.00')
                                                    ->prefixIcon('heroicon-o-banknotes'),

                                                TextInput::make('initial_stock')
                                                    ->label('Initial Stock')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->placeholder('0')
                                                    ->suffix('units')
                                                    ->prefixIcon('heroicon-o-archive-box')
                                                    ->hiddenOn('edit'),
                                            ]),

                                        // ── Options ────────────────────────────────
                                        Section::make('Options')
                                            ->icon('heroicon-o-adjustments-horizontal')
                                            ->columns(1)
                                            ->schema([
                                                Toggle::make('is_default')
                                                    ->label('Set as default variant')
                                                    ->helperText('The default variant is shown first to customers.')
                                                    ->default(false),
                                            ]),

                                        // ── Attributes ─────────────────────────────
                                        Section::make('Attributes')
                                            ->icon('heroicon-o-swatch')
                                            ->description('Add attribute combinations for this variant (e.g. Size: L, Color: Red)')
                                            ->schema([
                                                Repeater::make('attribute_values')
                                                    ->label('')
                                                    ->addActionLabel('+ Add Attribute')
                                                    ->defaultItems(0)
                                                    ->grid(3)
                                                    ->schema([
                                                        Select::make('attribute_value_id')
                                                            ->label('Attribute')
                                                            ->options(function () {
                                                                return AttributeValue::with('type')
                                                                    ->get()
                                                                    ->filter(fn ($v) => $v->type !== null)
                                                                    ->mapWithKeys(fn ($v) => [
                                                                        $v->id => "{$v->type->name}: {$v->value}",
                                                                    ]);
                                                            })
                                                            ->searchable()
                                                            ->required()
                                                            ->prefixIcon('heroicon-o-tag'),
                                                    ]),
                                            ]),

                                    ]),
                            ]),

                    ]),

            ]);
    }
}
