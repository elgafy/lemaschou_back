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

class PaymentPolicy extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.payment-policy';

    protected static ?string $navigationLabel = 'Payment Policy';
    protected static ?string $title = 'Payment Policy Page';
    protected static ?int $navigationSort = 11;

    public ?string $payment_terms_page_title_en = '';
    public ?string $payment_terms_page_title_ar = '';
    public ?string $payment_terms_page_content_en = '';
    public ?string $payment_terms_page_content_ar = '';
    public ?string $desc_payment_policy_en = '';
    public ?string $keywords_payment_policy_en = '';
    public ?string $desc_payment_policy_ar = '';
    public ?string $keywords_payment_policy_ar = '';


    public function mount(): void
    {
        $this->payment_terms_page_title_en = Setting::where('key', 'payment_terms_page_title_en')->first()?->value ?? '';
        $this->payment_terms_page_title_ar = Setting::where('key', 'payment_terms_page_title_ar')->first()?->value ?? '';
        $this->payment_terms_page_content_en = Setting::where('key', 'payment_terms_page_content_en')->first()?->value ?? '';
        $this->payment_terms_page_content_ar = Setting::where('key', 'payment_terms_page_content_ar')->first()?->value ?? '';
        $this->desc_payment_policy_en = Seo::where('key', 'desc_payment_policy_en')->first()->value ?? '';
        $this->keywords_payment_policy_en = Seo::where('key', 'keywords_payment_policy_en')->first()->value ?? '';
        $this->desc_payment_policy_ar = Seo::where('key', 'desc_payment_policy_ar')->first()->value ?? '';
        $this->keywords_payment_policy_ar = Seo::where('key', 'keywords_payment_policy_ar')->first()->value ?? '';

    }
    protected function getFormSchema(): array {
        return [
            Fieldset::make('Page Title')
            ->schema([
                TextInput::make('payment_terms_page_title_en')->label('Payment Terms Page Title in English')->required()->maxLength(255),
                TextInput::make('payment_terms_page_title_ar')->label('Payment Terms Page Title in Arabic')->required()->maxLength(255),
            ])->columns([
                'xs' => 1,
                'sm' => 2,
            ]),
            Fieldset::make('Page Content')
            ->schema([
                RichEditor::make('payment_terms_page_content_en')->label('Payment Terms Page Content in English')->required()->maxLength(255),
                RichEditor::make('payment_terms_page_content_ar')->label('Payment Terms Page Content in Arabic')->required()->maxLength(255),
            ])->columns(1),
            Fieldset::make('Page SEO Meta')
            ->schema([
                TextInput::make('keywords_payment_policy_en')->label('SEO Keywords in English')->required()->maxLength(255),
                Textarea::make('desc_payment_policy_en')->label('SEO Description in English')->required()->maxLength(255)->rows(5),
                TextInput::make('keywords_payment_policy_ar')->label('SEO Keywords in Arabic')->required()->maxLength(255),
                Textarea::make('desc_payment_policy_ar')->label('SEO Description in Arabic')->required()->maxLength(255)->rows(5),
            ])->columns(1),
        ];
    }

    public function submit(): void {
        Setting::updateOrCreate(['key' => 'payment_terms_page_title_en'], ['value' => $this->payment_terms_page_title_en]);
        Setting::updateOrCreate(['key' => 'payment_terms_page_title_ar'], ['value' => $this->payment_terms_page_title_ar]);
        Setting::updateOrCreate(['key' => 'payment_terms_page_content_en'], ['value' => $this->payment_terms_page_content_en]);
        Setting::updateOrCreate(['key' => 'payment_terms_page_content_ar'], ['value' => $this->payment_terms_page_content_ar]);
        Seo::updateOrCreate(['key' => 'desc_payment_policy_en'], ['value' => $this->desc_payment_policy_en]);
        Seo::updateOrCreate(['key' => 'keywords_payment_policy_en'], ['value' => $this->keywords_payment_policy_en]);
        Seo::updateOrCreate(['key' => 'desc_payment_policy_ar'], ['value' => $this->desc_payment_policy_ar]);
        Seo::updateOrCreate(['key' => 'keywords_payment_policy_ar'], ['value' => $this->keywords_payment_policy_ar]);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

    }
}
