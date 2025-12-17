<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('Asset created')
        ->body('Asset created succsessfully');
    }
}
