<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftCardResource\Pages;
use App\Models\GiftCard;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Reservations Management';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Gift Cards';

    protected static ?string $modelLabel = 'Gift Card';

    protected static ?string $pluralModelLabel = 'Gift Cards';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title_en')
                    ->label('Card Title in English')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title_ar')
                    ->label('Card Title in Arabic')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image')
                    ->label('Card Image')
                    ->disk('s3')
                    ->directory('uploads/gift_cards')
                    ->image()
                    ->downloadable()
                    ->openable()
                    ->required()
                    ->rules(['mimes:jpeg,png,jpg,webp'])
                    ->hint('Upload an image in JPEG, PNG, JPG, or WEBP format. This field is required.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('s3')
                    ->width(60)
                    ->height(60),

                TextColumn::make('title_en')
                    ->label('Title in English')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title_ar')
                    ->label('Title in Arabic')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
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
                            ->title('Gift Card Deleted')
                            ->body('Gift Card has been deleted successfully.')
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
            'index' => Pages\ListGiftCards::route('/'),
            'create' => Pages\CreateGiftCard::route('/create'),
            'edit' => Pages\EditGiftCard::route('/{record}/edit'),
            'view' => Pages\ViewGiftCard::route('/{record}'),
        ];
    }
}
