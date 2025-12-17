<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Filament\Resources\AdResource\RelationManagers;
use App\Models\Ad;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ViewField;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationGroup = 'Main Settings';

    protected static ?string $navigationLabel = 'Ad';


    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_ad') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_ad') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_ad') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_ad') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_ad') : false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ViewField::make('current_image')
                ->view('components.current-image')
                ->label('Current Desktop Image'),
                Forms\Components\FileUpload::make('image')
                ->label('Desktop Image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/ads')
                ->imagePreviewHeight('500')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('5120')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->rules([
                    'mimes:jpeg,png,jpg,webp',
                    'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000' // Image dimension constraints
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions 2000x2000 pixels. Only JPEG, PNG, JPG, and Webp formats are allowed, with a maximum file size of 5 MB. This field is required.'),

                ViewField::make('current_image_mobile')
                ->view('components.current-image-mobile')
                ->label('Current Mobile Image'),
                Forms\Components\FileUpload::make('image_mobile')
                ->label('Mobile Image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/ads')
                ->imagePreviewHeight('500')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('5120')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->rules([
                    'mimes:jpeg,png,jpg,webp',
                    'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000' // Image dimension constraints
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions 2000x2000 pixels. Only JPEG, PNG, JPG, and Webp formats are allowed, with a maximum file size of 5 MB. This field is required.'),

                TextInput::make('link')
                    ->label('Ad Link')
                    ->url()
                    ->nullable(),
                    // Toggle::make('show_one_time')
                    // ->label('Show One Time')
                    // ->onIcon('heroicon-s-check-circle')
                    // ->offIcon('heroicon-s-x-circle')
                    // ->required(),
                    Forms\Components\Select::make('show_one_time')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->default('0')
                    ->label('Show One Time'),
                    Repeater::make('ad_pages') // Nested form for AdPage
                    ->label('Ad Pages')
                    ->relationship('adPages') // Define the relationship
                    ->schema([
                        Select::make('page')
                            ->label('Page')
                            ->options([
                                'home' => 'Home',
                                'menu' => 'Menu',
                                'venue' => 'Venue',
                            ])
                            ->required(),
                    ])
                    ->columns(1), // Display fields in one column
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                ->label('Desktop Image')
                ->url(fn($record) => $record->image)
                ->width(50)
                ->height(50),
                ImageColumn::make('image_mobile')
                ->label('Mobile Image')
                ->url(fn($record) => $record->image_mobile)
                ->width(50)
                ->height(50),
                BooleanColumn::make('show_one_time')
                    ->label('Show Once'),
                TextColumn::make('link')
                    ->label('Link'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
