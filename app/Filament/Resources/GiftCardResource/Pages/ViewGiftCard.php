<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Resources\GiftCardResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewGiftCard extends ViewRecord
{
    protected static string $resource = GiftCardResource::class;

    protected static ?string $label = 'Gift Card';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Gift Card Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('created_at')->label('Created At')->dateTime(),
                        TextEntry::make('title_en')->label('Title (English)'),
                        TextEntry::make('title_ar')->label('Title (Arabic)'),
                        ImageEntry::make('image')
                            ->label('Card Image')
                            ->disk('s3')
                            ->height(200)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
