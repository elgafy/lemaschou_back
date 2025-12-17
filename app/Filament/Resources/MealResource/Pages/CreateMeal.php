<?php

namespace App\Filament\Resources\MealResource\Pages;

use App\Filament\Resources\MealResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMeal extends CreateRecord
{
    protected static string $resource = MealResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('Meal created')
        ->body('Meal created succsessfully');
    }
}
