<?php

namespace App\Filament\Resources\SpecialDaysResource\Pages;

use App\Filament\Resources\SpecialDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialDays extends EditRecord
{
    protected static string $resource = SpecialDaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
