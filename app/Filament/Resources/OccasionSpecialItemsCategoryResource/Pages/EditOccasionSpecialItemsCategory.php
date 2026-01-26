<?php

namespace App\Filament\Resources\OccasionSpecialItemsCategoryResource\Pages;

use App\Filament\Resources\OccasionSpecialItemsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOccasionSpecialItemsCategory extends EditRecord
{
    protected static string $resource = OccasionSpecialItemsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
