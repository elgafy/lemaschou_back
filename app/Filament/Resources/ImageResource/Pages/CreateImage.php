<?php

namespace App\Filament\Resources\ImageResource\Pages;

use App\Filament\Resources\ImageResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;
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
        ->title('About image created')
        ->body('About image created succsessfully');
    }
}
