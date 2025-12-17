<?php

namespace App\Filament\Resources\MenuPageImageResource\Pages;

use App\Filament\Resources\MenuPageImageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuPageImage extends CreateRecord
{
    protected static string $resource = MenuPageImageResource::class;
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
        ->title('Menu page image created')
        ->body('Menu page image created succsessfully');
    }
}
