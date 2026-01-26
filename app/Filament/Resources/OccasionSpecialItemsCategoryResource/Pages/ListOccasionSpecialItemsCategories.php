<?php

namespace App\Filament\Resources\OccasionSpecialItemsCategoryResource\Pages;

use App\Filament\Resources\OccasionSpecialItemsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOccasionSpecialItemsCategories extends ListRecords
{
    protected static string $resource = OccasionSpecialItemsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
