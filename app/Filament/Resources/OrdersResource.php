<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                Select::make('status')->label('Status')->required()
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('reservation.id')
                    ->label('Reservation ID')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => ReservationResource::getUrl('view', ['record' => $record->reservation_id]))
                    ->openUrlInNewTab(),
                TextColumn::make('reservation.first_name')
                    ->label('Guest')
                    ->formatStateUsing(fn ($record) => $record->reservation
                        ? $record->reservation->first_name.' '.$record->reservation->last_name
                        : '—')
                    ->sortable()
                    ->searchable(['reservations.first_name', 'reservations.last_name']),
                TextColumn::make('reservation.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subtotal')->label('Subtotal')->money('SAR', true)->sortable(),
                TextColumn::make('discount')->label('Discount')->money('SAR', true)->sortable(),
                TextColumn::make('deposit')->label('Deposit')->money('SAR', true)->sortable(),
                TextColumn::make('total')->label('Total')->money('SAR', true)->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('payment_processor')->label('Gateway')->sortable(),
                TextColumn::make('currency')->label('Currency')->sortable(),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
        ];
    }
}
