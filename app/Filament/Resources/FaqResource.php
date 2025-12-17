<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_any_faq') : false;
    }
    public static function canView($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('view_faq') : false;
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('create_faq') : false;
    }

    public static function canEdit($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('update_faq') : false;
    }

    public static function canDelete($record): bool
    {
        $user = Filament::auth()->user();
        return $user ? $user->can('delete_faq') : false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('q_en')
                ->label('Question (English)')
                ->rows(10)
                ->required(),
            Forms\Components\Textarea::make('q_ar')
                ->label('Question (Arabic)')
                ->rows(10)
                ->required(),
                Forms\Components\Textarea::make('a_en')
                ->label('Answer (English)')
                ->rows(10)
                ->required(),
            Forms\Components\Textarea::make('a_ar')
                ->label('Answer (Arabic)')
                ->rows(10)
                ->required(),
                Forms\Components\Select::make('status')
                ->options([
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->default('1')
                ->label('Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                ->label('No.')
                ->sortable(),
                Tables\Columns\TextColumn::make('q_en')
                ->label('Question (English)')
                ->searchable(),
                Tables\Columns\TextColumn::make('a_en')
                ->label('Answer (English)')
                ->searchable(),
                IconColumn::make('status')
                ->getStateUsing(fn(Faq $faq) => $faq->status ? '1' : '0')
                ->name('status')
                ->icon(fn(string $state): string => match ($state) {
                    '0' => 'heroicon-o-x-circle',
                    '1' => 'heroicon-o-check-circle',
                })
                ->color(fn(string $state): string => match ($state) {
                    '0' => 'danger',
                    '1' => 'success',
                }),
            ])->defaultSort('order') // Set the default sorting by 'order'
            ->reorderable('order')
            ->filters([
                // SelectFilter::make('status')
                //     ->options(['0' => 'Inactive', '1' => 'Active']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Faq deleted')
                        ->body('Faq deleted succsessfully')
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
