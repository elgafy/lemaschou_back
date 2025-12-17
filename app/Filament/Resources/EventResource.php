<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    protected static ?int $navigationSort = 6;
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_event') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_event') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_event') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_event') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_event') : false;
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
                // Forms\Components\FileUpload::make('image')
                //     ->disk('s3')
                //     ->columns(1)
                //     ->directory('uploads/events')
                //     ->reorderable()
                //     ->downloadable()
                //     ->required()
                //     ->openable()
                //     ->maxSize('250')
                //     ->visibility('publico')
                //     ->storeFileNamesIn('original_filename'),
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/events')
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
                    'dimensions:min_width=300,min_height=400,max_width=600,max_height=700'
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions between 300x400 and 600x700 pixels. Only JPEG, PNG, and JPG formats are allowed, with a maximum file size of 1 MB.'),
                //     Checkbox::make('status')
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
                ImageColumn::make('image')
                ->label('Image')
                ->url(fn($record) => $record->image)
                ->width(50)
                ->height(50),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name in english')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('Name in arabic')
                    ->searchable(),

                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(fn(Event $event) => $event->status ? '1' : '0')
                    ->name('status')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    }),
            ])->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Event deleted')
                            ->body('Event deleted succsessfully')
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
