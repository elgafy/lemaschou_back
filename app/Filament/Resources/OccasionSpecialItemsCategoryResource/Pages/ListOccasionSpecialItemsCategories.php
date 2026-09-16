<?php

namespace App\Filament\Resources\OccasionSpecialItemsCategoryResource\Pages;

use App\Filament\Resources\OccasionSpecialItemsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;

class ListOccasionSpecialItemsCategories extends ListRecords
{
    protected static string $resource = OccasionSpecialItemsCategoryResource::class;

    /**
     * Filament reorders with a query builder update, which bypasses Eloquent
     * events, so the cached occasion items must be cleared manually here.
     */
    public function reorderTable(array $order): void
    {
        parent::reorderTable($order);

        Cache::forget('occasion_items');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
