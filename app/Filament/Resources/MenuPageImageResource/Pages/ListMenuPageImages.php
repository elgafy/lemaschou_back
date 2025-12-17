<?php

namespace App\Filament\Resources\MenuPageImageResource\Pages;

use App\Filament\Resources\MenuPageImageResource;
use App\Models\MenuPageImage;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuPageImages extends ListRecords
{
    protected static string $resource = MenuPageImageResource::class;

    protected function getHeaderActions(): array
    {
        // Check if there are any records in the gallery table
        $hasRecords = MenuPageImage::query()->exists();

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
