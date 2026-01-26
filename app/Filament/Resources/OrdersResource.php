<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Filament\Resources\OrdersResource\RelationManagers;
use App\Models\Order;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Reservations Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Reservation Order';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('payment_status')->label('Payment Status')->required()
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation.id')->label('Reservation ID')->sortable()->searchable(),
                TextColumn::make('user_email')->label('User Email')->sortable()->searchable(),
                TextColumn::make('payment_status')->label('Payment Status')->sortable()->searchable(),
                TextColumn::make('price')->label('Price')->money('usd', true)->sortable()->searchable(),
                TextColumn::make('vat')->label('VAT')->money('usd', true)->sortable()->searchable(),
                TextColumn::make('total')->label('Total')->money('usd', true)->sortable()->searchable(),
                TextColumn::make('payment_processor')->label('Payment Processor')->sortable()->searchable(),
                TextColumn::make('payment_reference')->label('Payment Reference')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View Order Details'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
        ];
    }
}
