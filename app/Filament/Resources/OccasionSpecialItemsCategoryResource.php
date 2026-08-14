<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccasionSpecialItemsCategoryResource\Pages;
use App\Filament\Resources\OccasionSpecialItemsCategoryResource\RelationManagers;
use App\Models\OccasionSpecialItemsCategory;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OccasionSpecialItemsCategoryResource extends Resource
{
    protected static ?string $model = OccasionSpecialItemsCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationGroup = 'Reservations Management';
    protected static ?int $navigationSort = 6;

    protected static ?string $label = 'Occasion Item Categories';



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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label('Name in english')
                    ->searchable(),
                TextColumn::make('name_ar')
                    ->label('Name in arabic')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View Category'),
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
            'index' => Pages\ListOccasionSpecialItemsCategories::route('/'),
            'create' => Pages\CreateOccasionSpecialItemsCategory::route('/create'),
            'edit' => Pages\EditOccasionSpecialItemsCategory::route('/{record}/edit'),
        ];
    }
}
