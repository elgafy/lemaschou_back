<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SettingsPage extends Page
{
    use WithFileUploads;

    protected static ?string $navigationLabel = 'Settings';
    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Main Settings';


    public ?string $about_en = '';
    public ?string $about_ar = '';
    public ?string $email = '';
    public ?string $phone = '';
    public ?string $address_en = '';
    public ?string $address_ar = '';
    // public ?string $facebook = '';
    public ?string $instagram = '';
    // public ?string $linkedin = '';
    // public ?string $video = '';
    // public ?string $twitter = '';
    // public ?string $youtube = '';
    public ?string $from = '';
    public ?string $to = '';
    public ?string $reservation_link = '';
    public ?string $active_ramadan_menu='';
    // public $video_file; // Define the video_file property


    public function mount(): void
    {
        // Load settings from the database
        $this->about_en = Setting::where('key', 'about_en')->first()?->value ?? '';
        $this->about_ar = Setting::where('key', 'about_ar')->first()?->value ?? '';
        $this->email = Setting::where('key', 'email')->first()?->value ?? '';
        $this->phone = Setting::where('key', 'phone')->first()?->value ?? '';
        $this->address_en = Setting::where('key', 'address_en')->first()?->value ?? '';
        $this->address_ar = Setting::where('key', 'address_ar')->first()?->value ?? '';
        // $this->facebook = Setting::where('key', 'facebook')->first()?->value ?? '';
        $this->instagram = Setting::where('key', 'instagram')->first()?->value ?? '';
        // $this->twitter = Setting::where('key', 'twitter')->first()?->value ?? '';
        // $this->youtube = Setting::where('key', 'youtube')->first()?->value ?? '';
        // $this->video = Setting::where('key', 'video')->first()?->value ?? '';
        $this->from = Setting::where('key', 'from')->first()?->value ?? '';
        $this->to = Setting::where('key', 'to')->first()?->value ?? '';
        $this->reservation_link = Setting::where('key', 'reservation_link')->first()?->value ?? '';
        $this->active_ramadan_menu = Setting::where('key', 'active_ramadan_menu')->first()?->value ?? '';
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')->label('Email')->email()->required()->maxLength(255),
            TextInput::make('phone')->label('Phone')->tel()->required(),
            // TextInput::make('facebook')->label('Facebook')->required()->maxLength(255),
            TextInput::make('instagram')->label('Instagram')->required()->maxLength(255),
            // TextInput::make('twitter')->label('Twitter')->required()->maxLength(255),
            // TextInput::make('youtube')->label('Youtube')->required()->maxLength(255),
            Textarea::make('about_en')->label('About in English')->required()->rows(10),
            Textarea::make('about_ar')->label('About in Arabic')->required()->rows(10),
            TextInput::make('address_en')->label('Address in English')->required()->maxLength(255),
            TextInput::make('address_ar')->label('Address in Arabic')->required()->maxLength(255),
            // TextInput::make('video')->label('Video Link')->required()->maxLength(255),
            TimePicker::make('from')->label('Working from')->required()->format('H:i A'),
            TimePicker::make('to')->label('Working to')->required()->format('H:i A'),
            TextInput::make('reservation_link')->label('Reservation Link')->required()->maxLength(255),
            Forms\Components\Select::make('active_ramadan_menu')
                ->options([
                    '1' => 'Yes',
                    '0' => 'No',
                ])
                ->default('0')
                ->label('Active ramadan menu'),
        ];
    }

    public function submit(): void
    {

        // Update or create settings
        Setting::updateOrCreate(['key' => 'about_en'], ['value' => $this->about_en]);
        Setting::updateOrCreate(['key' => 'about_ar'], ['value' => $this->about_ar]);
        Setting::updateOrCreate(['key' => 'email'], ['value' => $this->email]);
        Setting::updateOrCreate(['key' => 'phone'], ['value' => $this->phone]);
        Setting::updateOrCreate(['key' => 'address_en'], ['value' => $this->address_en]);
        Setting::updateOrCreate(['key' => 'address_ar'], ['value' => $this->address_ar]);
        Setting::updateOrCreate(['key' => 'instagram'], ['value' => $this->instagram]);
        Setting::updateOrCreate(['key' => 'from'], ['value' => $this->from]);
        Setting::updateOrCreate(['key' => 'to'], ['value' => $this->to]);
        Setting::updateOrCreate(['key' => 'reservation_link'], ['value' => $this->reservation_link]);
        Setting::updateOrCreate(['key' => 'active_ramadan_menu'], ['value' => $this->active_ramadan_menu]);
        Notification::make()
            ->title('Settings updated successfully!')
            ->success()
            ->send();
    }

    public static function canPage(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('page_SettingsPage') : false;
    }
}
