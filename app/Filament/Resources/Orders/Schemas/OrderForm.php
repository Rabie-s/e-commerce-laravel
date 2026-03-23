<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                Grid::make(3)
                    ->schema([

                        // ── Left (wide) ────────────────────────
                        Group::make()
                            ->columnSpan(2)
                            ->schema([

                                Section::make('Order Status')
                                    ->icon('heroicon-o-arrow-path')
                                    ->description('Update the current status of this order.')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Order Status')
                                            ->options([
                                                OrderStatus::Pending->value => OrderStatus::Pending->label(),
                                                OrderStatus::Confirmed->value => OrderStatus::Confirmed->label(),
                                                OrderStatus::Shipped->value => OrderStatus::Shipped->label(),
                                                OrderStatus::Delivered->value => OrderStatus::Delivered->label(),
                                                OrderStatus::Cancelled->value => OrderStatus::Cancelled->label(),
                                            ])
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Payment Status')
                                    ->icon('heroicon-o-banknotes')
                                    ->description('Update payment collection status for this order.')
                                    ->schema([
                                        Select::make('payment_status')
                                            ->label('Payment Status')
                                            ->options([
                                                PaymentStatus::Pending->value => PaymentStatus::Pending->label(),
                                                PaymentStatus::Collected->value => PaymentStatus::Collected->label(),
                                                PaymentStatus::Failed->value => PaymentStatus::Failed->label(),
                                                PaymentStatus::Refunded->value => PaymentStatus::Refunded->label(),
                                            ])
                                            ->required()

                                            ->columnSpanFull(),
                                    ]),

                            ]),

                        // ── Right (narrow) ─────────────────────
                        Group::make()
                            ->columnSpan(1)
                            ->schema([

                                Section::make('Order Info')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Placeholder::make('order_id')
                                            ->label('Order #')
                                            ->content(fn ($record) => '#'.$record?->id),

                                        Placeholder::make('customer')
                                            ->label('Customer')
                                            ->content(fn ($record) => $record?->customerInfo
                                                ? "{$record->customerInfo->first_name} {$record->customerInfo->last_name}"
                                                : '—'
                                            ),

                                        Placeholder::make('total_price')
                                            ->label('Total Price')
                                            ->content(fn ($record) => $record ? '$'.number_format($record->total_price, 2) : '—'
                                            ),

                                        Placeholder::make('created_at')
                                            ->label('Order Date')
                                            ->content(fn ($record) => $record?->created_at?->format('M d, Y H:i')
                                            ),
                                    ]),

                            ]),

                    ]),

            ]);
    }
}
