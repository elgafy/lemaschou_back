<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 7;
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_testimonial') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_testimonial') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_testimonial') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_testimonial') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_testimonial') : false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name_en')
                    ->label('Name in english')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_ar')
                    ->label('Name in arabic')
                    ->required()
                    ->maxLength(255),

                Textarea::make('desc_en')
                    ->label('Description in english')
                    ->rows(4)
                    ->cols(10)
                    ->required(),

                Textarea::make('desc_ar')
                    ->label('Description in arabic')
                    ->rows(4)
                    ->cols(10)
                    ->required(),

                TextInput::make('job_en')
                    ->label('Job in english')
                    ->required()
                    ->maxLength(255),

                TextInput::make('job_ar')
                    ->label('Job in arabic')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\FileUpload::make('image')
                //     ->disk('s3')
                //     ->columns(1)
                //     ->directory('uploads/testimonials')
                //     ->reorderable()
                //     ->downloadable()
                //     ->required()
                //     ->openable()
                //     ->maxSize('250')
                //     ->visibility('publico')
                //     ->storeFileNamesIn('original_filename'),
                // Forms\Components\FileUpload::make('image')
                // ->disk('s3')
                // ->columns(1)
                // ->directory('uploads/testimonials')
                // ->reorderable()
                // ->downloadable()
                // ->required()
                // ->openable()
                // ->maxSize('250')
                // ->visibility('publico')
                // ->storeFileNamesIn('original_filename'),
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/testimonials')
                ->reorderable()
                ->downloadable()
                ->required()
                ->openable()
                ->maxSize('1024')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->rules([
                    'required',
                    'mimes:jpeg,png,jpg',
                    'dimensions:min_width=10,min_height=10,max_width=900,max_height=900'
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions between 10x10 and 900x900 pixels. Only JPEG, PNG, and JPG formats are allowed, with a maximum file size of 1 MB. This field is required.'),
                // Select::make('status')
                //     ->label('Status')
                //     ->options([
                //         '1' => 'Active',
                //         '0' => 'Unactive',
                //     ]),
                // Checkbox::make('status')
                // ->label('Status')
                // ->label(fn($state) => $state ? 'Status Active' : 'Status Unactive') // Dynamically change label
                // ->default(false)
                // ->afterStateHydrated(function (Checkbox $component, $state) {
                //     $component->state($state === '1');
                // })
                // ->dehydrateStateUsing(fn($state) => $state ? '1' : '0')
                Radio::make('status')
                ->label('Status')
                ->options([
                    '1' => 'Active',
                    '0' => 'Unactive',
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id'),
                // Tables\Columns\TextColumn::make('id'),
                ImageColumn::make('image')
                ->label('Image')
                ->url(fn($record) => $record->image)
                ->width(50)
                ->height(50),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name in English')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label('Name in Arabic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_en')
                    ->label('Job in English')
                    ->searchable(),

                Tables\Columns\TextColumn::make('job_ar')
                    ->label('Job in Arabic')
                    ->searchable(),


                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(fn(Testimonial $testimonial) => $testimonial->status ? '1' : '0')
                    ->name('status')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Testimonial Deleted')
                            ->body('Testimonial deleted successfully.')
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
