<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('Event created')
        ->body('Event created succsessfully');
    }
}
