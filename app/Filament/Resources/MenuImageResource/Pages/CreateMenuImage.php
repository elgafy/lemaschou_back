<?php

namespace App\Filament\Resources\MenuImageResource\Pages;

use App\Filament\Resources\MenuImageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuImage extends CreateRecord
{
    protected static string $resource = MenuImageResource::class;
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
        ->title('Menu image created')
        ->body('Menu image created succsessfully');
    }
}
