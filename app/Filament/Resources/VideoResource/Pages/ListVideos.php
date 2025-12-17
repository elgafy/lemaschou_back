<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Models\Video;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideos extends ListRecords
{
    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        // Check if there are any records in the gallery table
        $hasRecords = Video::query()->exists();

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
