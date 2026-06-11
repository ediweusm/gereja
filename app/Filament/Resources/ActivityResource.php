<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Akses dan Keamanan';

    public static function getModelLabel(): string
    {
        return 'Log Aktivitas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Log Aktivitas';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only resource, form is not used.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Nama Log')
                    ->badge()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi Tindakan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state) => $state ? str_replace('App\\Models\\', '', $state) : '')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Operator / Pengguna')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Read-only, no actions
            ])
            ->bulkActions([
                // Read-only, no bulk actions
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
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
