<?php

namespace App\Filament\Resources\OrdersResource\Pages;

use App\Filament\Resources\OrdersResource;
use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    protected static ?string $label = 'Order';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('Cancel Order')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->modalDescription('Are you sure you want to cancel this order? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, cancel order')
                ->visible(fn ($record) => ! in_array($record->status, ['cancelled', 'refunded']))
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);

                    Notification::make()
                        ->title('Order #'.$record->id.' has been cancelled')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->record->loadMissing(['reservation', 'items', 'payments']);

        return $infolist
            ->schema([
                Section::make('Order Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('payment_processor')
                            ->label('Payment Gateway')
                            ->placeholder('—'),
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->placeholder('—'),
                    ]),

                Section::make('Pricing')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('SAR'),
                        TextEntry::make('discount')
                            ->label('Discount')
                            ->money('SAR'),
                        TextEntry::make('deposit')
                            ->label('Deposit')
                            ->money('SAR'),
                        TextEntry::make('total')
                            ->label('Total')
                            ->money('SAR')
                            ->weight('bold'),
                    ]),

                Section::make('Reservation')
                    ->description(fn ($record) => $record->reservation
                        ? 'Reservation #'.$record->reservation->id.' — '.$record->reservation->first_name.' '.$record->reservation->last_name
                        : 'No reservation associated')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('reservation.id')
                            ->label('Reservation ID')
                            ->placeholder('No reservation')
                            ->url(fn ($record) => $record->reservation
                                ? ReservationResource::getUrl('view', ['record' => $record->reservation_id])
                                : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('reservation.sevenrooms_reservation_id')
                            ->label('Sevenrooms ID')
                            ->placeholder('—'),
                        TextEntry::make('reservation.status')
                            ->label('Reservation Status')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'confirmed' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('—'),
                        TextEntry::make('reservation.first_name')
                            ->label('Guest Name')
                            ->formatStateUsing(fn ($record) => $record->reservation
                                ? $record->reservation->first_name.' '.$record->reservation->last_name
                                : '—'),
                        TextEntry::make('reservation.email')
                            ->label('Email')
                            ->placeholder('—'),
                        TextEntry::make('reservation.mobile')
                            ->label('Mobile')
                            ->placeholder('—'),
                        TextEntry::make('reservation.date')
                            ->label('Date')
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('reservation.time')
                            ->label('Time')
                            ->placeholder('—'),
                        TextEntry::make('reservation.guests_count')
                            ->label('Guests')
                            ->placeholder('—'),
                    ]),

                Section::make('Order Items')
                    ->visible(fn ($record) => $record->items->count() > 0)
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')->label('Item'),
                                TextEntry::make('quantity')->label('Qty'),
                                TextEntry::make('unit_price')->label('Unit Price')->money('SAR'),
                                TextEntry::make('sub_total')->label('Subtotal')->money('SAR'),
                                TextEntry::make('vat')->label('VAT')->money('SAR'),
                                TextEntry::make('total')->label('Total')->money('SAR'),
                            ])
                            ->columns(6),
                    ]),

                Section::make('Payment History')
                    ->visible(fn ($record) => $record->payments->count() > 0)
                    ->schema([
                        RepeatableEntry::make('payments')
                            ->label('')
                            ->schema([
                                TextEntry::make('id')->label('Payment ID'),
                                TextEntry::make('gateway')->label('Gateway'),
                                TextEntry::make('amount')->label('Amount')->money('SAR'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (?string $state) => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'declined' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('gateway_transaction_id')
                                    ->label('Transaction ID')
                                    ->placeholder('—'),
                                TextEntry::make('paid_at')
                                    ->label('Paid At')
                                    ->dateTime()
                                    ->placeholder('—'),
                            ])
                            ->columns(6),
                    ]),
            ]);
    }
}
