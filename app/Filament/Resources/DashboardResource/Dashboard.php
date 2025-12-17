<?php
namespace App\Filament\Resources\DashboardResource;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsWidget::class,
        ];
    }
}
