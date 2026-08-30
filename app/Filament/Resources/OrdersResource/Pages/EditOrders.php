<?php

namespace App\Filament\Resources\OrdersResource\Pages;

use App\Filament\Resources\OrdersResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrders extends EditRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('Cancel Order')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->modalDescription('Are you sure you want to cancel this order? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, cancel order')
                ->visible(fn ($record) => ! in_array($record->status, ['cancelled', 'refunded']))
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);

                    Notification::make()
                        ->title('Order #'.$record->id.' has been cancelled')
                        ->success()
                        ->send();

                    $this->redirect(OrdersResource::getUrl('view', ['record' => $record]));
                }),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Order updated';
    }
}
