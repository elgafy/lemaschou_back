<?php

namespace App\Filament\Pages;

use App\Models\Seo;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;


class SeoPage extends Page
{
    protected static ?string $navigationLabel = 'Seo';
    protected static string $view = 'filament.pages.seo';
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?int $navigationSort = 9;

    protected static ?string $navigationGroup = 'Main Settings';

    public ?string $desc_home_en = '';
    public ?string $desc_home_ar = '';
    public ?string $keywords_home_en = '';
    public ?string $keywords_home_ar = '';

    public ?string $desc_menu_en = '';
    public ?string $desc_menu_ar = '';
    public ?string $keywords_menu_en = '';
    public ?string $keywords_menu_ar = '';

    public ?string $desc_venue_en = '';
    public ?string $desc_venue_ar = '';
    public ?string $keywords_venue_en = '';
    public ?string $keywords_venue_ar = '';

    public ?string $desc_faq_en = '';
    public ?string $desc_faq_ar = '';
    public ?string $keywords_faq_en = '';
    public ?string $keywords_faq_ar = '';

    public function mount(): void
    {
        // Load settings from the database
        $this->desc_home_en = Seo::where('key', 'desc_home_en')->first()?->value ?? '';
        $this->desc_home_ar = Seo::where('key', 'desc_home_ar')->first()?->value ?? '';
        $this->keywords_home_en = Seo::where('key', 'keywords_home_en')->first()?->value ?? '';
        $this->keywords_home_ar = Seo::where('key', 'keywords_home_ar')->first()?->value ?? '';

        $this->desc_menu_en = Seo::where('key', 'desc_menu_en')->first()?->value ?? '';
        $this->desc_menu_ar = Seo::where('key', 'desc_menu_ar')->first()?->value ?? '';
        $this->keywords_menu_en = Seo::where('key', 'keywords_menu_en')->first()?->value ?? '';
        $this->keywords_menu_ar = Seo::where('key', 'keywords_menu_ar')->first()?->value ?? '';

        $this->desc_venue_en = Seo::where('key', 'desc_venue_en')->first()?->value ?? '';
        $this->desc_venue_ar = Seo::where('key', 'desc_venue_ar')->first()?->value ?? '';
        $this->keywords_venue_en = Seo::where('key', 'keywords_venue_en')->first()?->value ?? '';
        $this->keywords_venue_ar = Seo::where('key', 'keywords_venue_ar')->first()?->value ?? '';

        $this->desc_faq_en = Seo::where('key', 'desc_faq_en')->first()?->value ?? '';
        $this->desc_faq_ar = Seo::where('key', 'desc_faq_ar')->first()?->value ?? '';
        $this->keywords_faq_en = Seo::where('key', 'keywords_faq_en')->first()?->value ?? '';
        $this->keywords_faq_ar = Seo::where('key', 'keywords_faq_ar')->first()?->value ?? '';
    }

    protected function getFormSchema(): array
    {
        return [
            Textarea::make('desc_home_en')->label('Description home in English')->required()->rows(10),
            Textarea::make('desc_home_ar')->label('Description home in Arabic')->required()->rows(10),
            TextInput::make('keywords_home_en')
                ->label('Keywords home in English')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),
                TextInput::make('keywords_home_ar')
                ->label('Keywords home in Arabic')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),



            Textarea::make('desc_menu_en')->label('Description menu in English')->required()->rows(10),
            Textarea::make('desc_menu_ar')->label('Description menu in Arabic')->required()->rows(10),
            TextInput::make('keywords_menu_en')
                ->label('Keywords menu in English')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),
                TextInput::make('keywords_menu_ar')
                ->label('Keywords menu in Arabic')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),


            Textarea::make('desc_venue_en')->label('Description venue in English')->required()->rows(10),
            Textarea::make('desc_venue_ar')->label('Description venue in Arabic')->required()->rows(10),
            TextInput::make('keywords_venue_en')
                ->label('Keywords venue in English')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),
                TextInput::make('keywords_venue_ar')
                ->label('Keywords venue in Arabic')
                ->placeholder('Add tags like sssss,sssss')
                ->required(),

                Textarea::make('desc_faq_en')->label('Description faq in English')->required()->rows(10),
                Textarea::make('desc_faq_ar')->label('Description faq in Arabic')->required()->rows(10),
                TextInput::make('keywords_faq_en')
                    ->label('Keywords faq in English')
                    ->placeholder('Add tags like sssss,sssss')
                    ->required(),
                    TextInput::make('keywords_faq_ar')
                    ->label('Keywords faq in Arabic')
                    ->placeholder('Add tags like sssss,sssss')
                    ->required(),
        ];
    }

    public function submit(): void
    {
        // Update other settings
        Seo::updateOrCreate(['key' => 'desc_home_en'], ['value' => $this->desc_home_en]);
        Seo::updateOrCreate(['key' => 'desc_home_ar'], ['value' => $this->desc_home_ar]);
        Seo::updateOrCreate(['key' => 'keywords_home_en'], ['value' => $this->keywords_home_en]);
        Seo::updateOrCreate(['key' => 'keywords_home_ar'], ['value' => $this->keywords_home_ar]);

        Seo::updateOrCreate(['key' => 'desc_menu_en'], ['value' => $this->desc_menu_en]);
        Seo::updateOrCreate(['key' => 'desc_menu_ar'], ['value' => $this->desc_menu_ar]);
        Seo::updateOrCreate(['key' => 'keywords_menu_en'], ['value' => $this->keywords_menu_en]);
        Seo::updateOrCreate(['key' => 'keywords_menu_ar'], ['value' => $this->keywords_menu_ar]);

        Seo::updateOrCreate(['key' => 'desc_venue_en'], ['value' => $this->desc_venue_en]);
        Seo::updateOrCreate(['key' => 'desc_venue_ar'], ['value' => $this->desc_venue_ar]);
        Seo::updateOrCreate(['key' => 'keywords_venue_en'], ['value' => $this->keywords_venue_en]);
        Seo::updateOrCreate(['key' => 'keywords_venue_ar'], ['value' => $this->keywords_venue_ar]);

        Seo::updateOrCreate(['key' => 'desc_faq_en'], ['value' => $this->desc_faq_en]);
        Seo::updateOrCreate(['key' => 'desc_faq_ar'], ['value' => $this->desc_faq_ar]);
        Seo::updateOrCreate(['key' => 'keywords_faq_en'], ['value' => $this->keywords_faq_en]);
        Seo::updateOrCreate(['key' => 'keywords_faq_ar'], ['value' => $this->keywords_faq_ar]);

        Notification::make()
            ->title('Seo settings updated successfully!')
            ->success()
            ->send();
    }

    public static function canPage(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('page_SeoPage') : false;
    }
    
}
