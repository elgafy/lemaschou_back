<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;

class ReservationsSettings extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Reservation Settings';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'Reservations Management';

    protected static string $view = 'filament.pages.reservations-settings';

    public ?string $use_reservation_external_link = '';
    public ?string $reservation_link = '';
    public ?string $force_reservation_downpayment = '';
    public ?string $downpayment_amount = '';
    public ?string $enable_sevenrooms_reservation = '';
    public ?string $booking_time_window = '';
    public ?int $booking_min_guests = 2;
    public ?int $booking_max_guests = 12;
    public ?string $sevenrooms_venue_id = '';
    public ?string $enable_occasions = '';
    public ?string $enable_occasion_items = '';
    public ?string $enable_occasion_items_payment = '';
    public ?string $add_calculated_vat = '';
    public ?string $vat_value = '';
    public ?string $enable_booking_notice = '';
    public ?string $booking_intro_en = '';
    public ?string $booking_intro_ar = '';
    public ?string $booking_notice_en = '';
    public ?string $booking_notice_ar = '';
    public ?string $occasion_items_title_en = '';
    public ?string $occasion_items_title_ar = '';
    public ?string $occasion_items_notice_en = '';
    public ?string $occasion_items_notice_ar = '';
    public ?array $occasions = [];
    public ?array $allergies = [];
    public ?string $enable_personnel_booking_email_notification = '';
    public ?string $enable_guest_booking_email_notification = '';
    public ?array $reservation_notice_emails = [];


    public function mount(): void {
        $this->use_reservation_external_link = Setting::where('key', 'use_reservation_external_link')->first()?->value ?? '';
        $this->reservation_link = Setting::where('key', 'reservation_link')->first()?->value ?? '';
        $this->enable_sevenrooms_reservation = Setting::where('key', 'enable_sevenrooms_reservation')->first()?->value ?? '';
        $this->force_reservation_downpayment = Setting::where('key', 'force_reservation_downpayment')->first()?->value ?? '';
        $this->downpayment_amount = Setting::where('key', 'downpayment_amount')->first()?->value ?? '';
        $this->booking_time_window = Setting::where('key', 'booking_time_window')->first()?->value ?? '';
        $this->booking_min_guests = Setting::where('key', 'booking_min_guests')->first()?->value ?? 2;
        $this->booking_max_guests = Setting::where('key', 'booking_max_guests')->first()?->value ?? 12;
        $this->sevenrooms_venue_id = Setting::where('key', 'sevenrooms_venue_id')->first()?->value ?? '';
        $this->enable_occasions = Setting::where('key', 'enable_occasions')->first()?->value ?? '';
        $this->enable_occasion_items = Setting::where('key', 'enable_occasion_items')->first()?->value ?? '';
        $this->enable_occasion_items_payment = Setting::where('key', 'enable_occasion_items_payment')->first()?->value ?? '';
        $this->add_calculated_vat = Setting::where('key', 'add_calculated_vat')->first()?->value ?? false;
        $this->vat_value = Setting::where('key', 'vat_value')->first()?->value ?? false;
        $this->enable_booking_notice = Setting::where('key', 'enable_booking_notice')->first()?->value ?? true;
        $this->booking_intro_en = Setting::where('key', 'booking_intro_en')->first()?->value ?? '';
        $this->booking_intro_ar = Setting::where('key', 'booking_intro_ar')->first()?->value ?? '';
        $this->booking_notice_en = Setting::where('key', 'booking_notice_en')->first()?->value ?? '';
        $this->booking_notice_ar = Setting::where('key', 'booking_notice_ar')->first()?->value ?? '';
        $this->occasion_items_title_en = Setting::where('key', 'occasion_items_title_en')->first()?->value ?? '';
        $this->occasion_items_title_ar = Setting::where('key', 'occasion_items_title_ar')->first()?->value ?? '';
        $this->occasion_items_notice_en = Setting::where('key', 'occasion_items_notice_en')->first()?->value ?? '';
        $this->occasion_items_notice_ar = Setting::where('key', 'occasion_items_notice_ar')->first()?->value ?? '';
        $this->occasions = json_decode(Setting::where('key', 'occasions')->first()?->value, true) ?? [];
        $this->allergies = json_decode(Setting::where('key', 'allergies')->first()?->value, true) ?? [];
        $this->enable_personnel_booking_email_notification = Setting::where('key', 'enable_personnel_booking_email_notification')->first()?->value ?? '';
        $this->enable_guest_booking_email_notification = Setting::where('key', 'enable_guest_booking_email_notification')->first()?->value ?? '';
        $this->reservation_notice_emails = json_decode(Setting::where('key', 'reservation_notice_emails')->first()?->value, true) ?? [];
    }
    protected function getFormSchema(): array {
        return [
            Toggle::make('use_reservation_external_link')
            ->label('Use external link for reservation')
            ->helperText('Use an external link for reservation instead of the website booking system.')
            ->live(),
            TextInput::make('reservation_link')->label('Reservation Link')->activeUrl()->required()->maxLength(255)->hidden(fn (Get $get): bool => ! $get('use_reservation_external_link')),
            Toggle::make('enable_sevenrooms_reservation')->label('Enable sevenrooms booking (disable for testing)'),
            TextInput::make('sevenrooms_venue_id')
            ->label('Sevenrooms venue ID')
            ->helperText('Sevenrooms venue ID which will be used for reservations, this field is mandatory for reservations to be accessible in Sevenrooms.')
            ->required()
            ->maxLength(255),
            TextInput::make('booking_time_window')->label('Booking Time Window')->numeric()->integer()->minValue(1)->required()->helperText('Initial booking reservation will be holded for this period (in minutes) before being automatically released if not confirmed.'),
            Fieldset::make('Guests Count Limits')
                ->schema([
                    TextInput::make('booking_min_guests')->label('Minimum Guests')->numeric()->integer()->minValue(1)->required(),
                    TextInput::make('booking_max_guests')->label('Maximum Guests')->numeric()->integer()->minValue(1)->required(),
                ])->columns([
                    'xs' => 1,
                    'sm' => 2,
                ]),
            Toggle::make('enable_booking_notice')->label('Enable Booking Notice Popup')
            ->helperText('Enable popup to inform customers with drinks and desserts policy.'),
            Toggle::make('enable_occasions')->label('Enable Special Occassions')
            ->helperText('Enable occasions selection, like Wedding, Business, Date Night etc.'),
            Toggle::make('enable_occasion_items')->label('Enable Special Occassion Items')
            ->helperText('Enable occasion special items to be purchased, like Flowers, Cakes etc.'),
            Toggle::make('enable_occasion_items_payment')->label('Enable Payment for Special Occassion Items')
            ->helperText('Enable payment for occasion special items reservation.'),
            Toggle::make('add_calculated_vat')->label('Add Calculated VAT')
            ->helperText('Add calculated VAT to the total price.')->live(),
            TextInput::make('vat_value')->label('VAT Value')->required()->numeric()->hidden(fn (Get $get): bool => ! $get('add_calculated_vat')),
            Section::make('Occasions')
            ->schema([
                Repeater::make('occasions')
                ->schema([
                    TextInput::make('key')->required(),
                    TextInput::make('name_en')->required(),
                    TextInput::make('name_ar')->required(),
                ])
                ->columns(3)
                ->collapsible()
            ])
            ->collapsed(),
            Section::make('Food Allergies')
            ->schema([
                Repeater::make('allergies')
                ->schema([
                    TextInput::make('key')->required(),
                    TextInput::make('name_en')->required(),
                    TextInput::make('name_ar')->required(),
                ])
                ->columns(3)
                ->collapsible()
            ])
            ->collapsed(),
            Section::make('Copywrite')
            ->columns([
                'xs' => 1,
                'sm' => 2,
                'xl' => 3,
            ])
            ->schema([
                Fieldset::make('Booking Widget Introduction Text - what displayed to customers before the booking form')
                ->schema([
                    Textarea::make('booking_intro_en')->label('Booking widget intro in English'),
                    Textarea::make('booking_intro_ar')->label('Booking widget intro in Arabic'),
                ])->columns([
                    'xs' => 1,
                    'sm' => 2,
                ]),
                Fieldset::make('Booking Popup Notice - Drinks and desserts policy ')
                ->schema([
                    Textarea::make('booking_notice_en')->label('Notice in English')->required(),
                    Textarea::make('booking_notice_ar')->label('Notice in Arabic')->required(),
                ])->columns([
                    'xs' => 1,
                    'sm' => 2,
                ]),
                Fieldset::make('Special Occaasion Items Title')
                ->schema([
                    TextInput::make('occasion_items_title_en')->label('Title in English')->required(),
                    TextInput::make('occasion_items_title_ar')->label('Title in Arabic')->required(),
                ])->columns([
                    'xs' => 1,
                    'sm' => 2,
                ]),
                Fieldset::make('Special Occaasion Items Payment Notice')
                ->schema([
                    Textarea::make('occasion_items_notice_en')->label('Notice in English')->required()->rows(5),
                    Textarea::make('occasion_items_notice_ar')->label('Notice in Arabic')->required()->rows(5),
                ])->columns([
                    'xs' => 1,
                    'sm' => 2,
                ]),
            ])
            ->collapsed(),
            Section::make('Email Notifications Settings')
            ->schema([
                Toggle::make('enable_guest_booking_email_notification')->label('Enable Booking Email Notification for Guests')
                ->helperText('Send email notification to guests email after booking is made or updated.'),
                Toggle::make('enable_personnel_booking_email_notification')->label('Enable Reservation\'s Special Items Email Notification for Personnel')
                ->helperText('Send email notification to specified emails when a new reservation is made with special occasion items.')
                ->live(),
                Repeater::make('reservation_notice_emails')
                ->label('Personnel to receive reservation notice emails')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->required(),
                ])
                ->columns(2)
                ->collapsible()
                ->hidden(fn (Get $get): bool => ! $get('enable_personnel_booking_email_notification'))
            ])
            ->collapsed(),
        ];
    }
    public function submit(): void {
        Setting::updateOrCreate(['key' => 'use_reservation_external_link'], ['value' => $this->use_reservation_external_link]);
        Setting::updateOrCreate(['key' => 'reservation_link'], ['value' => $this->reservation_link]);
        Setting::updateOrCreate(['key' => 'force_reservation_downpayment'], ['value' => $this->force_reservation_downpayment]);
        Setting::updateOrCreate(['key' => 'enable_sevenrooms_reservation'], ['value' => $this->enable_sevenrooms_reservation]);
        Setting::updateOrCreate(['key' => 'downpayment_amount'], ['value' => $this->downpayment_amount]);
        Setting::updateOrCreate(['key' => 'booking_time_window'], ['value' => $this->booking_time_window]);
        Setting::updateOrCreate(['key' => 'booking_min_guests'], ['value' => $this->booking_min_guests]);
        Setting::updateOrCreate(['key' => 'booking_max_guests'], ['value' => $this->booking_max_guests]);
        Setting::updateOrCreate(['key' => 'sevenrooms_venue_id'], ['value' => $this->sevenrooms_venue_id]);
        Setting::updateOrCreate(['key' => 'enable_booking_notice'], ['value' => $this->enable_booking_notice]);
        Setting::updateOrCreate(['key' => 'enable_occasions'], ['value' => $this->enable_occasions]);
        Setting::updateOrCreate(['key' => 'enable_occasion_items'], ['value' => $this->enable_occasion_items]);
        Setting::updateOrCreate(['key' => 'enable_occasion_items_payment'], ['value' => $this->enable_occasion_items_payment]);
        Setting::updateOrCreate(['key' => 'add_calculated_vat'], ['value' => $this->add_calculated_vat]);
        Setting::updateOrCreate(['key' => 'vat_value'], ['value' => $this->vat_value]);
        Setting::updateOrCreate(['key' => 'booking_intro_en'], ['value' => $this->booking_intro_en]);
        Setting::updateOrCreate(['key' => 'booking_intro_ar'], ['value' => $this->booking_intro_ar]);
        Setting::updateOrCreate(['key' => 'booking_notice_en'], ['value' => $this->booking_notice_en]);
        Setting::updateOrCreate(['key' => 'booking_notice_ar'], ['value' => $this->booking_notice_ar]);
        Setting::updateOrCreate(['key' => 'occasion_items_title_en'], ['value' => $this->occasion_items_title_en]);
        Setting::updateOrCreate(['key' => 'occasion_items_title_ar'], ['value' => $this->occasion_items_title_ar]);
        Setting::updateOrCreate(['key' => 'occasion_items_notice_en'], ['value' => $this->occasion_items_notice_en]);
        Setting::updateOrCreate(['key' => 'occasion_items_notice_ar'], ['value' => $this->occasion_items_notice_ar]);
        Setting::updateOrCreate(['key' => 'occasions'], ['value' => json_encode($this->occasions)]);
        Setting::updateOrCreate(['key' => 'allergies'], ['value' => json_encode($this->allergies)]);
        Setting::updateOrCreate(['key' => 'enable_personnel_booking_email_notification'], ['value' => $this->enable_personnel_booking_email_notification]);
        Setting::updateOrCreate(['key' => 'enable_guest_booking_email_notification'], ['value' => $this->enable_guest_booking_email_notification]);
        Setting::updateOrCreate(['key' => 'reservation_notice_emails'], ['value' => json_encode($this->reservation_notice_emails)]);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

}
