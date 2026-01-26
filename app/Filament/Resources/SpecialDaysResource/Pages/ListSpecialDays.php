<?php

namespace App\Filament\Resources\SpecialDaysResource\Pages;

use App\Filament\Resources\SpecialDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialDays extends ListRecords
{
    protected static string $resource = SpecialDaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
