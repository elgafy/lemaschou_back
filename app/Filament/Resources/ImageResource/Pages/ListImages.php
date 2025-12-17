<?php

namespace App\Filament\Resources\ImageResource\Pages;

use App\Filament\Resources\ImageResource;
use App\Models\Image;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImages extends ListRecords
{
    protected static string $resource = ImageResource::class;

    protected function getHeaderActions(): array
    {
        // Check if there are any records in the gallery table
        $hasRecords = Image::query()->exists();

        // If there are no records, show the create button
        if (!$hasRecords) {
            return [
                Actions\CreateAction::make(),
            ];
        }

        // If there are records, hide the create button
        return [];
    }
}
