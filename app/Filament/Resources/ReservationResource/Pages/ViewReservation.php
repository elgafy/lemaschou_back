<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\OrdersResource;
use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected static ?string $label = 'Reservation';

    public static function canAccess(array $arguments = []): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->record->loadMissing(['order.items', 'order.payments']);

        return $infolist
            ->schema([
                Section::make('Reservation Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sevenrooms_reservation_id')
                            ->label('Sevenrooms ID')
                            ->placeholder('—'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'confirmed' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('mobile')
                            ->label('Mobile')
                            ->placeholder('—'),
                        TextEntry::make('date')->label('Date')->date(),
                        TextEntry::make('time')->label('Time'),
                        TextEntry::make('guests_count')->label('Guests Count'),
                        TextEntry::make('special_request')
                            ->label('Special Request')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ]),

                Section::make('Occasion')
                    ->columns(3)
                    ->schema([
                        IconEntry::make('occasion')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark')
                            ->label('Has Occasion'),
                        TextEntry::make('occasion_type')
                            ->label('Occasion Type')
                            ->placeholder('—'),
                        TextEntry::make('deposite')
                            ->label('Deposit Amount')
                            ->money('SAR')
                            ->placeholder('—'),
                        TextEntry::make('occasion_items')
                            ->label('Occasion Items')
                            ->state(function ($record) {
                                $state = $record->occasion_items;
                                if (! $state) {
                                    return '—';
                                }
                                $items = is_array($state) ? $state : json_decode($state, true);
                                if (! is_array($items) || empty($items)) {
                                    return '—';
                                }

                                return collect($items)->map(function ($item) {
                                    $name = $item['itemName'] ?? 'Item';
                                    $qty = $item['quantity'] ?? 1;
                                    $variation = $item['variationValue'] ?? null;
                                    $label = $variation ? $name.' - '.$variation : $name;

                                    return $qty > 1 ? $label.' (x'.$qty.')' : $label;
                                })->implode(', ');
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Allergies')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('allergic')
                            ->boolean()
                            ->trueIcon('heroicon-o-exclamation-triangle')
                            ->falseIcon('heroicon-o-check-circle')
                            ->label('Has Allergies'),
                        TextEntry::make('food_allergies')
                            ->label('Food Allergies')
                            ->state(function ($record) {
                                $state = $record->food_allergies;
                                if (! $state) {
                                    return '—';
                                }
                                $allergies = is_array($state) ? $state : json_decode($state, true);

                                return is_array($allergies) && ! empty($allergies) ? implode(', ', $allergies) : '—';
                            }),
                    ]),

                Section::make('Order')
                    ->description(fn ($record) => $record->order
                        ? 'Order #'.$record->order->id.' — '.ucfirst($record->order->status)
                        : 'No order associated with this reservation')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('order.id')
                            ->label('Order ID')
                            ->placeholder('No order')
                            ->url(fn ($record) => $record->order
                                ? OrdersResource::getUrl('view', ['record' => $record->order_id])
                                : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('order.status')
                            ->label('Order Status')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                default => 'gray',
                            })
                            ->placeholder('—'),
                        TextEntry::make('order.payment_processor')
                            ->label('Payment Gateway')
                            ->placeholder('—'),
                        TextEntry::make('order.subtotal')
                            ->label('Subtotal')
                            ->money('SAR')
                            ->placeholder('—'),
                        TextEntry::make('order.discount')
                            ->label('Discount')
                            ->money('SAR')
                            ->placeholder('—'),
                        TextEntry::make('order.deposit')
                            ->label('Order Deposit')
                            ->money('SAR')
                            ->placeholder('—'),
                        TextEntry::make('order.total')
                            ->label('Order Total')
                            ->money('SAR')
                            ->placeholder('—'),
                        TextEntry::make('order.currency')
                            ->label('Currency')
                            ->placeholder('—'),
                        TextEntry::make('order.created_at')
                            ->label('Order Created')
                            ->dateTime()
                            ->placeholder('—'),
                    ]),

                Section::make('Order Items')
                    ->visible(fn ($record) => $record->order && $record->order->items->count() > 0)
                    ->schema([
                        RepeatableEntry::make('order.items')
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
                    ->visible(fn ($record) => $record->order && $record->order->payments->count() > 0)
                    ->schema([
                        RepeatableEntry::make('order.payments')
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
