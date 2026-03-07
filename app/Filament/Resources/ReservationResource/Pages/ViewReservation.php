<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;
    protected static ?string $label = 'Order';
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('first_name')->label('First Name'),
            TextEntry::make('last_name')->label('Last Name'),
            TextEntry::make('email')->label('Email'),
            TextEntry::make('mobile')->label('Mobile'),
            TextEntry::make('date')->label('Date'),
            TextEntry::make('time')->label('Time'),
            TextEntry::make('guests_count')->label('Guests Count'),
            IconEntry::make('occasion')
            ->boolean()
            ->trueIcon('heroicon-o-check-badge')
            ->falseIcon('heroicon-o-x-mark')
            ->label('Has Occasion'),
            IconEntry::make('allergic')
            ->boolean()
            ->trueIcon('heroicon-o-check-badge')
            ->falseIcon('heroicon-o-x-mark')
            ->label('Is Allergic'),
            TextEntry::make('order.id')->label('Order ID'),
        ]);
    }

}
