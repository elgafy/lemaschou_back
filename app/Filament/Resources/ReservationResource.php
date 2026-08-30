<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\Setting;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Reservations Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Reservation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
                DatePicker::make('date')->label('Date')->required(),
                TextInput::make('time')->label('Time')->required(),
                TextInput::make('guests_count')->label('Guests Count')->required()->numeric(),
                TextInput::make('first_name')->label('First Name')->required(),
                TextInput::make('last_name')->label('Last Name')->required(),
                TextInput::make('email')->label('Email')->required()->email(),
                TextInput::make('mobile')->label('Mobile'),
                Textarea::make('special_request')->label('Special Request')->rows(2)->columnSpanFull(),
                Toggle::make('occasion')->label('Has Occasion'),
                Select::make('occasion_type')->label('Occasion Type')
                    ->options(self::getOccasionOptions())
                    ->searchable()
                    ->placeholder('Select occasion'),
                Toggle::make('allergic')->label('Has Allergies'),
                CheckboxList::make('food_allergies')->label('Food Allergies')
                    ->options(self::getAllergyOptions())
                    ->columns(3)
                    ->columnSpanFull(),
                TextInput::make('deposite')->label('Deposit Amount')->numeric(),

            ]);
    }

    private static function getOccasionOptions(): array
    {
        $raw = Setting::where('key', 'occasions')->first()?->value;
        if (! $raw) {
            return [];
        }
        $occasions = json_decode($raw, true);
        if (! is_array($occasions)) {
            return [];
        }

        return collect($occasions)->mapWithKeys(fn ($item) => [
            $item['name_en'] => $item['name_en'],
        ])->toArray();
    }

    private static function getAllergyOptions(): array
    {
        $raw = Setting::where('key', 'allergies')->first()?->value;
        if (! $raw) {
            return [];
        }
        $allergies = json_decode($raw, true);
        if (! is_array($allergies)) {
            return [];
        }

        return collect($allergies)->mapWithKeys(fn ($item) => [
            $item['name_en'] => $item['name_en'],
        ])->toArray();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sevenrooms_reservation_id')->label('Sevenrooms Reservation ID')->sortable()->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('order.id')->label('Order ID')->sortable()->searchable(),
                TextColumn::make('first_name')->label('First Name')->searchable(),
                TextColumn::make('last_name')->label('Last Name')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('mobile')->label('Mobile')->searchable(),
                TextColumn::make('date')->label('Date')->date()->sortable(),
                TextColumn::make('time')->label('Time'),
                TextColumn::make('guests_count')->label('Guests Count'),
                IconColumn::make('occasion')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
                IconColumn::make('allergic')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
                // BooleanColumn::make('occasion')->label('Occasion'),
                // BooleanColumn::make('allergic')->label('Allergic'),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReservations::route('/'),
            // 'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
            'view' => Pages\ViewReservation::route('/{record}'),
        ];
    }
}
