<?php

namespace App\Filament\Resources\MenuImageResource\Pages;

use App\Filament\Resources\MenuImageResource;
use App\Models\MenuImage;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuImages extends ListRecords
{
    protected static string $resource = MenuImageResource::class;

    protected function getHeaderActions(): array
    {
        // Check if there are any records in the gallery table
        $hasRecords = MenuImage::query()->exists();

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
