<?php

namespace App\Filament\Resources\OccasionSpecialItemsResource\Pages;

use App\Filament\Resources\OccasionSpecialItemsResource;
use App\Models\OccasionSpecialItemsCategory;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOccasionSpecialItems extends ListRecords
{
    protected static string $resource = OccasionSpecialItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        // Add a tab for 'All' categories (no filtering)
        $tabs['all'] = Tab::make()
            ->label('All') // Label the tab "All"
            ->modifyQueryUsing(fn(Builder $query) => $query); // No query modification for 'All'

        // Fetch categories dynamically
        $categories = OccasionSpecialItemsCategory::get();

        // Loop through each category and create a corresponding tab
        foreach ($categories as $category) {
            $tabs[$category->id] = Tab::make()
                ->label($category->name_en) // Set the category name as the tab label
                ->modifyQueryUsing(fn(Builder $query) => $query->where('category', $category->id)); // Filter meals by category
        }

        return $tabs;
    }
}
