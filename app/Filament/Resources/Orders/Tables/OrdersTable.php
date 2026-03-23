<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),

                TextColumn::make('customerInfo.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($state, $record) => $record->customerInfo
                        ? "{$record->customerInfo->first_name} {$record->customerInfo->last_name}"
                        : '—'
                    )
                    ->searchable(),

                TextColumn::make('customerInfo.phone_number')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Order Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->color() : 'gray'
                    )
                    ->sortable(),

                TextColumn::make('payment.status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn ($state) => $state instanceof PaymentStatus ? $state->color() : 'gray'
                    ),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options([
                        OrderStatus::Pending->value => OrderStatus::Pending->label(),
                        OrderStatus::Confirmed->value => OrderStatus::Confirmed->label(),
                        OrderStatus::Shipped->value => OrderStatus::Shipped->label(),
                        OrderStatus::Delivered->value => OrderStatus::Delivered->label(),
                        OrderStatus::Cancelled->value => OrderStatus::Cancelled->label(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
