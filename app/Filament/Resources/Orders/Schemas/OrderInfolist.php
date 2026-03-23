<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([

                        Group::make()
                            ->columnSpan(2)
                            ->schema([

                                Section::make('Order Items')
                                    ->icon('heroicon-o-shopping-cart')
                                    ->schema([
                                        RepeatableEntry::make('items')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('variant.product.name')
                                                    ->label('Product'),

                                                TextEntry::make('variant.sku')
                                                    ->label('SKU')
                                                    ->badge()
                                                    ->color('gray'),

                                                TextEntry::make('quantity')
                                                    ->label('Qty'),

                                                TextEntry::make('unit_price')
                                                    ->label('Unit Price')
                                                    ->money('USD'),

                                                TextEntry::make('total')
                                                    ->label('Total')
                                                    ->money('USD')
                                                    ->getStateUsing(fn ($record) =>
                                                        $record->quantity * $record->unit_price
                                                    ),
                                            ])
                                            ->columns(5),
                                    ]),

                                Section::make('Summary')
                                    ->icon('heroicon-o-calculator')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('total_price')
                                            ->label('Total Price')
                                            ->money('USD'),

                                        TextEntry::make('status')
                                            ->label('Order Status')
                                            ->badge()
                                            ->color(fn ($state) =>
                                            $state instanceof OrderStatus
                                                ? $state->color() : 'gray'
                                            ),

                                        TextEntry::make('created_at')
                                            ->label('Order Date')
                                            ->dateTime(),
                                    ]),

                            ]),

                        Group::make()
                            ->columnSpan(1)
                            ->schema([

                                Section::make('Customer')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('customerInfo.first_name')
                                            ->label('First Name'),

                                        TextEntry::make('customerInfo.last_name')
                                            ->label('Last Name'),

                                        TextEntry::make('customerInfo.phone_number')
                                            ->label('Phone'),

                                        TextEntry::make('customerInfo.city')
                                            ->label('City'),

                                        TextEntry::make('customerInfo.address')
                                            ->label('Address'),

                                        TextEntry::make('customerInfo.nearby_landmark')
                                            ->label('Nearby Landmark')
                                            ->placeholder('—'),
                                    ]),

                                Section::make('Payment')
                                    ->icon('heroicon-o-banknotes')
                                    ->schema([
                                        TextEntry::make('payment.method')
                                            ->label('Method')
                                            ->badge()
                                            ->color('gray')
                                            ->formatStateUsing(fn ($state) =>
                                            $state instanceof PaymentMethod
                                                ? $state->label() : '—'
                                            ),

                                        TextEntry::make('payment.status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn ($state) =>
                                            $state instanceof PaymentStatus
                                                ? $state->color() : 'gray'
                                            ),

                                        TextEntry::make('payment.amount')
                                            ->label('Amount')
                                            ->money('USD'),

                                        TextEntry::make('payment.paid_at')
                                            ->label('Paid At')
                                            ->dateTime()
                                            ->placeholder('Not paid yet'),
                                    ]),

                            ]),

                    ]),

            ]);
    }
}
