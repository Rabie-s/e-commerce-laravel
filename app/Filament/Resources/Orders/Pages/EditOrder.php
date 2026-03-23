<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\OrderResource;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['payment_status'] = $this->record->payment?->status?->value;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['payment_status']);

        return $data;
    }

    protected function afterSave(): void
    {
        $paymentStatus = $this->data['payment_status'] ?? null;

        if ($paymentStatus && $this->record->payment) {
            $data = ['status' => $paymentStatus];

            if ($paymentStatus === PaymentStatus::Collected->value) {
                $data['paid_at'] = Carbon::now();
            }

            $this->record->payment->update($data);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
