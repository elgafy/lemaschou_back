<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAd extends CreateRecord
{
    protected static string $resource = AdResource::class;

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
           // CreateAction::make()->createAnother(false) // Hides "Create & Create Another"
        ];

    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('Ad created')
        ->body('Ad created succsessfully');
    }
}
