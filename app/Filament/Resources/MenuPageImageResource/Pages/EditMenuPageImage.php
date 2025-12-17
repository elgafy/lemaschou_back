<?php

namespace App\Filament\Resources\MenuPageImageResource\Pages;

use App\Filament\Resources\MenuPageImageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMenuPageImage extends EditRecord
{
    protected static string $resource = MenuPageImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Menu page image updated')
            ->body('Menu page image updated succsessfully');
    }
}
