<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancelReservation')
                ->label('Cancel Reservation')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Cancel Reservation')
                ->modalDescription('Are you sure you want to cancel this reservation? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, cancel reservation')
                ->visible(fn ($record) => $record->status !== 'cancelled')
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);

                    Notification::make()
                        ->title('Reservation #'.$record->id.' has been cancelled')
                        ->success()
                        ->send();

                    $this->redirect(ReservationResource::getUrl('view', ['record' => $record]));
                }),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Reservation updated';
    }
}
