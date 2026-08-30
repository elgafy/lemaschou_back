<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Filament\Resources\ReservationResource\Widgets\ReservationsOverview;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
{
    return [
        ReservationsOverview::class,
    ];
}
}
