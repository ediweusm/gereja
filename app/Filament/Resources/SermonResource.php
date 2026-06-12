<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SermonResource\Pages;
use App\Models\Sermon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SermonResource extends Resource
{
    protected static ?string $model = Sermon::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $modelLabel = 'Arsip Khotbah';
    protected static ?string $pluralModelLabel = 'Arsip Khotbah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Khotbah')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Tema / Judul Khotbah')
                        ->required(),
                    Forms\Components\DatePicker::make('sermon_date')
                        ->label('Tanggal Ibadah')
                        ->required(),
                    Forms\Components\TextInput::make('preacher')
                        ->label('Nama Pembicara / Pendeta'),
                    Forms\Components\TextInput::make('passage')
                        ->label('Nats / Ayat Alkitab (Misal: Yohanes 3:16)'),
                    Forms\Components\Textarea::make('content_summary')
                        ->label('Ringkasan Khotbah')
                        ->rows(5)
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Multimedia (Opsional)')->schema([
                    Forms\Components\TextInput::make('video_url')
                        ->label('Link Video (YouTube)')
                        ->url(),
                    Forms\Components\TextInput::make('audio_url')
                        ->label('Link Audio (Spotify/Drive)')
                        ->url(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Tema Khotbah')->searchable(),
                Tables\Columns\TextColumn::make('preacher')->label('Pembicara')->searchable(),
                Tables\Columns\TextColumn::make('passage')->label('Ayat'),
                Tables\Columns\TextColumn::make('sermon_date')->label('Tanggal')->date('d M Y')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSermons::route('/'),
            'create' => Pages\CreateSermon::route('/create'),
            'edit' => Pages\EditSermon::route('/{record}/edit'),
        ];
    }
}