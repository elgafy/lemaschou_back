<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Asset updated')
            ->body('Asset updated successfully');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete the old image from S3 (optional, depending on your use case)
            if ($record->image) {
                Storage::disk('s3')->delete($record->image);
            }

            // Upload the new image to S3
            $imagePath = $data['image']->store('uploads/meals', 's3');

            // Store the image path in the data array
            $data['image'] = $imagePath;
        } elseif (empty($data['image'])) {
            // If no image is provided, you can either leave it as it is or delete the image from the record (optional)
            // For example, we might want to unset the image to not overwrite it with null
            unset($data['image']);
        }

        // Update the model with the provided data (including the image path)
        $record->update($data);

        // Return the updated record
        return $record;
    }
}
