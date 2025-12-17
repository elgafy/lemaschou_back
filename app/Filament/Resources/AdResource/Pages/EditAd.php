<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditAd extends EditRecord
{
    protected static string $resource = AdResource::class;

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
            ->title('Ad updated')
            ->body('Ad updated succsessfully');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        foreach (['image', 'image_mobile'] as $field) {
            if (isset($data[$field]) && $data[$field] instanceof \Illuminate\Http\UploadedFile) {
                if ($record->{$field}) {
                    Storage::disk('s3')->delete($record->{$field});
                }
                $data[$field] = $data[$field]->store('uploads/ads', 's3');
            } elseif (empty($data[$field])) {
                unset($data[$field]);
            }
        }

        $record->update($data);
        return $record;
    }
}
