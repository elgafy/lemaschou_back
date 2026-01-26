<?php

namespace App\Filament\Resources\OccasionSpecialItemsResource\Pages;

use App\Filament\Resources\OccasionSpecialItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOccasionSpecialItems extends EditRecord
{
    protected static string $resource = OccasionSpecialItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
