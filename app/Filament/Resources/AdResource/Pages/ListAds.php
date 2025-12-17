<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\Ad;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAds extends ListRecords
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        // Check if there are any records in the gallery table
        $hasRecords = Ad::query()->exists();

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
