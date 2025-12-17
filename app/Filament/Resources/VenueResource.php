<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Filament\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use App\Rules\ImageVenueDimensions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?int $navigationSort = 5;
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_venue') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_venue') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_venue') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_venue') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_venue') : false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\FileUpload::make('image')
                //     ->disk('s3')
                //     ->columns(1)
                //     ->directory('uploads/venues')
                //     ->reorderable()
                //     ->downloadable()
                //     ->required()
                //     ->openable()
                //     ->maxSize('250')
                //     ->visibility('publico')
                //     ->storeFileNamesIn('original_filename'),
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/venues')
                ->reorderable()
                ->downloadable()
                ->required()
                ->openable()
                ->maxSize('1024')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->rules([
                    'required',
                    'mimes:jpeg,png,jpg',
                    'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions between 100x100 and 2000x2000 pixels. Only JPEG, PNG, and JPG formats are allowed, with a maximum file size of 1 MB. This field is required.'),
                Select::make('is_main')
                    ->label('Is main ?')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
                // Checkbox::make('status')
                // ->label('Status')
                // ->label(fn($state) => $state ? 'Status Active' : 'Status Unactive') // Dynamically change label
                // ->default(false)
                // ->afterStateHydrated(function (Checkbox $component, $state) {
                //     $component->state($state === '1');
                // })
                // ->dehydrateStateUsing(fn($state) => $state ? '1' : '0')
                Radio::make('status')
                ->label('Status')
                ->options([
                    '1' => 'Active',
                    '0' => 'Unactive',
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('No.'),
                ImageColumn::make('image')
                    ->label('Image')
                    ->url(fn($record) => $record->image)
                    ->width(50)
                    ->height(50),

                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(fn(Venue $venue) => $venue->status ? '1' : '0')
                    ->name('status')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Venue Deleted')
                            ->body('Venue deleted successfully.')
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
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
