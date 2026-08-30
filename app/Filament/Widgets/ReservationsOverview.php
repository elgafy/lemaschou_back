<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Reservation;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationsOverview extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Reservations', Reservation::count())
            ->url(route('filament.admin.resources.reservations.index')), // Make it a direct link
            Stat::make('Total Orders', Order::count()),
        ];
    }
}
