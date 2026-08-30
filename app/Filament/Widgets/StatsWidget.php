<?php
namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Meal;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsWidget extends BaseWidget
{
    use HasWidgetShield;
    protected function getCards(): array
    {
        return [
            Card::make('Total Categories', Category::count()),
            // Card::make('Total Contacts', Contact::count()),
            Card::make('Total Meals', Meal::count()),
            Card::make('Total Users', User::count()),
        ];
    }
}
