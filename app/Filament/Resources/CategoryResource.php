<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
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
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Tables\Columns\TextColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_category') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_category') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_category') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_category') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_category') : false;
    }
    public static function form(Form $form): Form
    {
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

                TextInput::make('title_en')
                    ->label('Title in english')
                    ->nullable()
                    ->maxLength(255),
                TextInput::make('title_ar')
                    ->label('Title in arabic')
                    ->nullable()
                    ->maxLength(255),
                // Select::make('status')
                //     ->label('Status')
                //     ->options([
                //         '1' => 'Active',
                //         '0' => 'Unactive',
                //     ]),
                // Checkbox::make('status')
                // ->label('Status')
                // ->label(fn($state) => $state ? 'Status Active' : 'Status Unactive') // Dynamically change label
                // ->default(false)
                // ->afterStateHydrated(function (Checkbox $component, $state) {
                //     $component->state($state === '1');
                // })
                // ->dehydrateStateUsing(fn($state) => $state ? '1' : '0')

                Select::make('grouped')
                    ->label('This category have grouped meals ?')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->default('0')
                    ->hidden(),

                Radio::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Unactive',
                    ]),
                Radio::make('is_ramadan')
                    ->label('Is in ramadan ?')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
                    Radio::make('is_menu')
                    ->default('1')
                    ->label('Is in main menu ?')
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
                // Tables\Columns\TextColumn::make('id'),
                TextColumn::make('order')
                    ->label('No.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name in english')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('Name in arabic')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(fn(Category $category) => $category->status ? '1' : '0')
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
                    ->getStateUsing(fn(Category $category) => $category->is_ramadan ? '1' : '0')
                    ->name('is_ramadan')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
            ])->defaultSort('order') // Set the default sorting by 'order'
            ->reorderable('order')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Category deleted')
                            ->body('Category deleted succsessfully')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            // 'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
