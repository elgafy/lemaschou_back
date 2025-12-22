<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ReservationsSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Reservation Settings';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Reservation';

    protected static string $view = 'filament.pages.reservations-settings';

    public ?string $use_reservation_external_link = '';
    public ?string $reservation_link = '';


    public function mount(): void {
        $this->use_reservation_external_link = Setting::where('key', 'use_reservation_external_link')->first()?->value ?? '';
        $this->reservation_link = Setting::where('key', 'reservation_link')->first()?->value ?? '';
    }
    protected function getFormSchema(): array {
        return [
            Toggle::make('use_reservation_external_link')->label('Use external link for reservation')->activeUrl(),
            TextInput::make('reservation_link')->label('Reservation Link')->required()->maxLength(255)
        ];
    }
    public function submit(): void {
        Setting::updateOrCreate(['key' => 'use_reservation_external_link'], ['value' => $this->use_reservation_external_link]);
        Setting::updateOrCreate(['key' => 'reservation_link'], ['value' => $this->reservation_link]);
    }

}
