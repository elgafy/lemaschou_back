<?php

namespace App\Filament\Resources;

use App\Exports\MealsExport;
use App\Filament\Resources\MealResource\Pages;
use App\Filament\Resources\MealResource\RelationManagers;
use App\Models\Meal;
use Filament\Actions\Modal\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Maatwebsite\Excel\Facades\Excel;

class MealResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?int $navigationSort = 3;
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_meal') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_meal') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_meal') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_meal') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_meal') : false;
    }

    public static function form(Form $form): Form
    {
        $isCreate = $form->getOperation() === "create";

        return $form
            ->schema([


                TextInput::make('name_en')
                    ->label('Name in english')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_ar')
                    ->label('Name in arabic')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description_en')
                    ->rows(10)
                    ->cols(20)
                    ->nullable(),

                Textarea::make('description_ar')
                    ->rows(10)
                    ->cols(20)
                    ->nullable(),

                TextInput::make('calories')
                    ->label('Calories')
                    ->required()
                    ->numeric()
                    ->minValue('0'),


                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->minValue('0'),

                TextInput::make('grams')
                    ->label('grams')
                    ->required()
                    ->numeric()
                    ->minValue('0'),
                    
                Select::make('category_id')
                    ->relationship('category', 'name_en')
                    ->required()
                    ->options(function () {
                        return \App\Models\Category::where('status','1')->pluck('name_en', 'id'); // Using a scope 'active'
                    }),
            
                //}),
                // Select::make('category_id')
                //     ->relationship(name: 'category', titleAttribute: 'name_en')
                //     ->required(),
                //     Checkbox::make('status')
                //     ->label('Status')
                //     ->trueLabel('Active')    // Label when checkbox is checked
                //     ->falseLabel('Unactive') // Label when checkbox is unchecked
                //     ->default(false)         // Default to 'Unactive' (unchecked)
                //     ->required()             // Optional: make it required
                //     ->afterStateHydrated(function ($component, $state) {
                //         // Set the checkbox state based on the stored value ('1' for checked, '0' for unchecked)
                //         $component->state($state === '1');
                //     })
                //     ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0')

                // // Select::make('status')
                // //     ->label('Status')
                // //     ->options([
                // //         '1' => 'Active',
                // //         '0' => 'Unactive',
                // //     ]),
                // // // Checkbox::make('status')
                // // //     ->label('Status')
                // // //     ->accepted()
                // // //     ->required(),
                // Forms\Components\FileUpload::make('image')
                // ->columns(1)
                // ->directory('uploads/meals')
                // ->reorderable()
                // ->downloadable()
                // ->openable()
                // ->maxSize('250')
                // ->required()
                // ->storeFileNamesIn('original_filename'),
                // Forms\Components\FileUpload::make('image')
                // ->disk('s3')
                // ->columns(1)
                // ->directory('uploads/meals')
                // ->reorderable()
                // ->downloadable()
                // ->required()
                // ->openable()
                // ->maxSize('250')
                // ->visibility('publico')
                // ->storeFileNamesIn('original_filename'),
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/meals')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('1024')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->nullable()
                // ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Required only on create
                ->rules([
                    'mimes:jpeg,png,jpg',
                    'dimensions:min_width=100,min_height=100,max_width=500,max_height=500'
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions between 100x100 and 500x500 pixels. Only JPEG, PNG, and JPG formats are allowed, with a maximum file size of 1 MB. This field is optional.'),
                Select::make('featured_type')
                ->label('Featured Type')
                ->options([
                    'new' => 'New',
                    'best seller' => 'Best Seller',
                    '' => 'No Feature'
                ])
                ->nullable(),
                // Checkbox::make('status')
                //     ->label('Status')
                //     ->label(fn($state) => $state ? 'Status Active' : 'Status Unactive') // Dynamically change label
                //     ->default(false)
                //     ->afterStateHydrated(function (Checkbox $component, $state) {
                //         $component->state($state === '1');
                //     })
                //     ->dehydrateStateUsing(fn($state) => $state ? '1' : '0')
                Radio::make('status')
                ->label('Status')
                ->options([
                    '1' => 'Active',
                    '0' => 'Unactive',
                ])
                ,Radio::make('is_ramadan')
                    ->label('Is in ramadan ?')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->default('0'),
                    Radio::make('is_menu')
                    ->label('Is in main menu ?')
                    ->default('1')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')->label('No.'),
                TextColumn::make('order')
                ->label('No.')
                ->sortable(),
                ImageColumn::make('image')
                ->label('Image')
                ->url(fn($record) => $record->image)
                ->width(50)
                ->height(50),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name in English')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label('Name in Arabic')
                    ->searchable(),

                Tables\Columns\TextColumn::make('calories')
                    ->label('Calories')
                    ->searchable(),

                Tables\Columns\TextColumn::make('grams')
                    ->label('Grams')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable(),

                




                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(fn(Meal $meal) => $meal->status ? '1' : '0')
                    ->name('status')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),

                    Tables\Columns\IconColumn::make('is_ramadan')
                    ->getStateUsing(fn(Meal $meal) => $meal->is_ramadan ? '1' : '0')
                    ->name('is_ramadan')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
            ])
            ->defaultSort('order') // Set the default sorting by 'order'
            ->reorderable('order') // Enable manual ordering by 'order' column
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Meal Deleted')
                            ->body('Meal deleted successfully.')
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeals::route('/'),
            'create' => Pages\CreateMeal::route('/create'),
            'edit' => Pages\EditMeal::route('/{record}/edit'),
        ];
    }
}
