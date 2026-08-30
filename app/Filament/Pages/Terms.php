<?php

namespace App\Filament\Pages;

use App\Models\Seo;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Dom\Text;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Terms extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.terms';

    protected static ?string $navigationLabel = 'Terms';
    protected static ?string $title = 'Privacy & Terms Page';
    protected static ?int $navigationSort = 10;

    public ?string $privacy_terms_page_title_en = '';
    public ?string $privacy_terms_page_title_ar = '';
    public ?string $privacy_terms_page_content_en = '';
    public ?string $privacy_terms_page_content_ar = '';
    public ?string $desc_terms_en = '';
    public ?string $keywords_terms_en = '';
    public ?string $desc_terms_ar = '';
    public ?string $keywords_terms_ar = '';


    public function mount(): void
    {
        $this->privacy_terms_page_title_en = Setting::where('key', 'privacy_terms_page_title_en')->first()?->value ?? '';
        $this->privacy_terms_page_title_ar = Setting::where('key', 'privacy_terms_page_title_ar')->first()?->value ?? '';
        $this->privacy_terms_page_content_en = Setting::where('key', 'privacy_terms_page_content_en')->first()?->value ?? '';
        $this->privacy_terms_page_content_ar = Setting::where('key', 'privacy_terms_page_content_ar')->first()?->value ?? '';
        $this->desc_terms_en = Seo::where('key', 'desc_terms_en')->first()->value ?? '';
        $this->keywords_terms_en = Seo::where('key', 'keywords_terms_en')->first()->value ?? '';
        $this->desc_terms_ar = Seo::where('key', 'desc_terms_ar')->first()->value ?? '';
        $this->keywords_terms_ar = Seo::where('key', 'keywords_terms_ar')->first()->value ?? '';

    }
    protected function getFormSchema(): array {
        return [
            Fieldset::make('Page Title')
            ->schema([
                TextInput::make('privacy_terms_page_title_en')->label('Privacy & Terms Page Title in English')->required()->maxLength(255),
                TextInput::make('privacy_terms_page_title_ar')->label('Privacy & Terms Page Title in Arabic')->required()->maxLength(255),
            ])->columns([
                'xs' => 1,
                'sm' => 2,
            ]),
            Fieldset::make('Page Content')
            ->schema([
                RichEditor::make('privacy_terms_page_content_en')->label('Privacy & Terms Page Content in English')->required()->maxLength(255),
                RichEditor::make('privacy_terms_page_content_ar')->label('Privacy & Terms Page Content in Arabic')->required()->maxLength(255),
            ])->columns(1),
            Fieldset::make('Page SEO Meta')
            ->schema([
                TextInput::make('keywords_terms_en')->label('SEO Keywords in English')->required()->maxLength(255),
                Textarea::make('desc_terms_en')->label('SEO Description in English')->required()->maxLength(255)->rows(5),
                TextInput::make('keywords_terms_ar')->label('SEO Keywords in Arabic')->required()->maxLength(255),
                Textarea::make('desc_terms_ar')->label('SEO Description in Arabic')->required()->maxLength(255)->rows(5),
            ])->columns(1),
        ];
    }

    public function submit(): void {
        Setting::updateOrCreate(['key' => 'privacy_terms_page_title_en'], ['value' => $this->privacy_terms_page_title_en]);
        Setting::updateOrCreate(['key' => 'privacy_terms_page_title_ar'], ['value' => $this->privacy_terms_page_title_ar]);
        Setting::updateOrCreate(['key' => 'privacy_terms_page_content_en'], ['value' => $this->privacy_terms_page_content_en]);
        Setting::updateOrCreate(['key' => 'privacy_terms_page_content_ar'], ['value' => $this->privacy_terms_page_content_ar]);
        Seo::updateOrCreate(['key' => 'desc_terms_en'], ['value' => $this->desc_terms_en]);
        Seo::updateOrCreate(['key' => 'keywords_terms_en'], ['value' => $this->keywords_terms_en]);
        Seo::updateOrCreate(['key' => 'desc_terms_ar'], ['value' => $this->desc_terms_ar]);
        Seo::updateOrCreate(['key' => 'keywords_terms_ar'], ['value' => $this->keywords_terms_ar]);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

    }
}
