<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialDaysResource\Pages;
use App\Filament\Resources\SpecialDaysResource\RelationManagers;
use App\Models\SpecialDays;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpecialDaysResource extends Resource
{
    protected static ?string $model = SpecialDays::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Special Days';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Reservations Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Date')
                    ->required()->unique(ignoreRecord: true)->columnspan(2),
                TextInput::make('name_en')
                    ->label('Name in english')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_ar')
                    ->label('Name in arabic')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description_en')
                    ->label('Description in english')
                    ->maxLength(255),
                Textarea::make('description_ar')
                    ->label('Description in arabic')
                    ->maxLength(255),
                TextInput::make('lunch_shift_payment_amount')
                    ->label('Lunch Shift Payment Amount')
                    ->required()
                    ->numeric(),
                TextInput::make('dinner_shift_payment_amount')
                    ->label('Dinner Shift Payment Amount')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Date')->date()->sortable()->searchable(),
                TextColumn::make('name_en')->label('Name')->sortable()->searchable(),
                TextColumn::make('lunch_shift_payment_amount')->label('Lunch Payment Amount'),
                TextColumn::make('dinner_shift_payment_amount')->label('Dinner Payment Amount'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSpecialDays::route('/'),
            'create' => Pages\CreateSpecialDays::route('/create'),
            'edit' => Pages\EditSpecialDays::route('/{record}/edit'),
        ];
    }
}
