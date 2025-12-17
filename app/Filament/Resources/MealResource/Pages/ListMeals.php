<?php

namespace App\Filament\Resources\MealResource\Pages;

use App\Exports\MealsExport;
use App\Filament\Resources\MealResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tabs\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class ListMeals extends ListRecords
{
    protected static string $resource = MealResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //         Action::make('export')
    //             ->label('Export to Excel')
    //             ->action(fn() => Excel::download(new MealsExport, 'meals.xlsx'))
    //             ->requiresConfirmation()
    //             ->color('success')
    //             ->successNotificationTitle('Export Successful'),
    //     ];
    // }

    // public function getTabs(): array
    // {
    //     $tabs = [];

    //     // Add a tab for 'All' categories (no filtering)
    //     $tabs['all'] = Tab::make()
    //         ->label('All') // Label the tab "All"
    //         ->modifyQueryUsing(fn(Builder $query) => $query); // No query modification for 'All'

    //     // Fetch categories dynamically
    //     $categories = Category::where('status', '1')->get();

    //     // Loop through each category and create a corresponding tab
    //     foreach ($categories as $category) {
    //         $tabs[$category->id] = Tab::make()
    //             ->label($category->name_en) // Set the category name as the tab label
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('category_id', $category->id)); // Filter meals by category
    //     }

    //     return $tabs;
    // }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('New Meal')
                ->url($this->getResource()::getUrl('create'))
                ->color('primary'),

            Actions\Action::make('export')
                ->label('Export to Excel')
                ->action(fn() => Excel::download(new MealsExport, 'meals.xlsx'))
                ->requiresConfirmation()
                ->color('success')
                ->successNotificationTitle('Export Successful'),
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
        $categories = Category::where('status', '1')->orderBy('order')->get();

        // Loop through each category and create a corresponding tab
        foreach ($categories as $category) {
            $tabs[$category->id] = Tab::make()
                ->label($category->name_en) // Set the category name as the tab label
                ->modifyQueryUsing(fn(Builder $query) => $query->where('category_id', $category->id)); // Filter meals by category
        }

        return $tabs;
    }
}
