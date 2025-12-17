<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageResource\Pages;
use App\Filament\Resources\ImageResource\RelationManagers;
use App\Models\Image;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Main Settings';

    protected static ?string $navigationLabel = 'About Image';
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_image') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_image') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_image') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_image') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_image') : false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                ->disk('s3')
                ->columns(1)
                ->directory('uploads/settings/about')
                ->reorderable()
                ->downloadable()
                ->openable()
                ->maxSize('1024')
                ->visibility('publico')
                ->storeFileNamesIn('original_filename')
                ->nullable()
                // ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // Required only on create
                ->rules([
                    'mimes:jpeg,png,jpg',
                    'dimensions:min_width=100,min_height=100,max_width=1550,max_height=870' // Image dimension constraints
                    // Custom rule for maximum dimensions
                ])
                ->hint('Upload an image with dimensions 1440x844 pixels. Only JPEG, PNG, and JPG formats are allowed, with a maximum file size of 1 MB. This field is optional.')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                ->label('Image')
                ->url(fn($record) => $record->video)
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
