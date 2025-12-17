<?php

namespace App\Filament\Resources\MenuImageResource\Pages;

use App\Filament\Resources\MenuImageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMenuImage extends EditRecord
{
    protected static string $resource = MenuImageResource::class;

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
            ->title('Menu image updated')
            ->body('Menu image updated succsessfully');
    }
}
