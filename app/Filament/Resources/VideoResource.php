<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource\RelationManagers;
use App\Models\Video;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationGroup = 'Main Settings';

    protected static ?string $navigationLabel = 'Website Video';
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_video') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_video') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_video') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_video') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_video') : false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // FileUpload::make('video') // New file upload for video
                // ->label('Upload Video')
                // ->disk('s3') // Adjust according to your storage
                // ->directory('uploads/settings/videos') // Directory for videos
                // ->acceptedFileTypes(['video/*']) // Only allow video types
                // ->required()
                // ->maxSize(102400) // Max size in kilobytes (adjust as needed)
                // ->visibility('publico'), // Set visibility to public if needed

                FileUpload::make('video') // New file upload for video
                ->label('Upload Video')
                ->disk('s3') // Adjust according to your storage
                ->directory('uploads/settings/videos') // Directory for videos
                ->acceptedFileTypes(['video/*']) // Only allow video types
                ->required()
                ->maxSize(102400) // Max size in kilobytes (adjust as needed)
                ->visibility('publico') // Set visibility to public if needed
                ->hint('Upload a video file up to 100 MB in size. Only video formats (e.g., MP4, AVI, MOV) are allowed. This field is required.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('video')
                ->label('Video')
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
