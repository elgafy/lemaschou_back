<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccasionSpecialItemsResource\Pages;
use App\Filament\Resources\OccasionSpecialItemsResource\RelationManagers;
use App\Models\OccasionSpecialItems;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OccasionSpecialItemsResource extends Resource
{
    protected static ?string $model = OccasionSpecialItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Reservations Management';
    protected static ?int $navigationSort = 7;

    protected static ?string $label = 'Occasion Items';

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
                Textarea::make('description_en')
                    ->label('Item description in English')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description_ar')
                    ->label('Item description in Arabic')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->relationship('category', 'name_en')
                    ->required()
                    ->options(function () {
                        return \App\Models\OccasionSpecialItemsCategory::all()->pluck('name_en', 'id'); // Using a scope 'active'
                    }),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->minValue('0'),
                TextInput::make('reservation_availability_period')
                    ->label('Reservation Availability Period (days)')
                    ->helperText('Number of days in advance the item is available for reservation')
                    ->required()
                    ->numeric()
                    ->minValue('0')
                    ->default('0'),
                TextInput::make('available_before_time')
                    ->label('Available Before Hour')
                    ->helperText('What hour in 24 hours format, before the booking time; the item is available for reservation, e.g. if the booking time is 20:00 and you set this field to 18:00, the item will not be available for reservation after 18:00')
                    ->numeric()
                    ->minValue('0')
                    ->default('13'),
                FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/specialItems')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('125')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->nullable()
                // ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Required only on create
                ->rules([
                    'mimes:jpeg,png,jpg,webp',
                    // 'dimensions:min_width=50,min_height=50,max_width=100,max_height=100'
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions between 50x50 and 100x100 pixels. Only JPEG, PNG, and WEBP formats are allowed, with a maximum file size of 125 KB. This field is required.')
                // ->imageResizeMode('cover')
                // ->imageCropAspectRatio('1:1')
                // ->imageResizeTargetWidth('100')
                // ->imageResizeTargetHeight('100')
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                ->label('Image')
                ->disk("s3")
                ->url(fn($record) => $record->image)
                ->width(50)
                ->height(50),
                TextColumn::make('name_en')
                    ->label('Name in english')
                    ->searchable(),
                TextColumn::make('name_ar')
                    ->label('Name in arabic')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Occasion Item Deleted')
                            ->body('Occasion item deleted successfully.')
                    ),
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
            'index' => Pages\ListOccasionSpecialItems::route('/'),
            'create' => Pages\CreateOccasionSpecialItems::route('/create'),
            'edit' => Pages\EditOccasionSpecialItems::route('/{record}/edit'),
        ];
    }
}
