<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuImageResource\Pages;
use App\Filament\Resources\MenuImageResource\RelationManagers;
use App\Models\MenuImage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuImageResource extends Resource
{
    protected static ?string $model = MenuImage::class;


    protected static ?string $navigationIcon = 'heroicon-o-cake';


    protected static ?int $navigationSort = 11;

    protected static ?string $navigationGroup = 'Main Settings';

    protected static ?string $navigationLabel = 'Menu Image In Home';
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_menu::image') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_menu::image') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_menu::image') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_menu::image') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_menu::image') : false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('text_en')
                ->label('Text in english')
                    ->rows(10)
                    ->cols(20)
                    ->required(),

                Textarea::make('text_ar')
                ->label('Text in english')
                    ->rows(10)
                    ->cols(20)
                    ->required(),
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/settings/menu_page')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('1024')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->required()
                // ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Required only on create
                ->rules([
                    'mimes:jpeg,png,jpg,webp',
                    'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000' // Image dimension constraints
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions 2000x2000 pixels. Only JPEG, PNG, JPG, and Webp formats are allowed, with a maximum file size of 1 MB. This field is required.'),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMenuImages::route('/'),
            'create' => Pages\CreateMenuImage::route('/create'),
            'edit' => Pages\EditMenuImage::route('/{record}/edit'),
        ];
    }
}
