<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;
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
        ->title('Video created')
        ->body('Video created succsessfully');
    }
}
