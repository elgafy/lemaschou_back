<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('Gallery created')
        ->body('Gallery created succsessfully');
    }
}
