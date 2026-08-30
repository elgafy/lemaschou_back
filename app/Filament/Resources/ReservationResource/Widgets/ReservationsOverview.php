<?php

namespace App\Filament\Resources\ReservationResource\Widgets;

use App\Models\Reservation;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationsOverview extends BaseWidget
{
    use InteractsWithPageTable;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Reservations', Reservation::count()),
        ];
    }
}
