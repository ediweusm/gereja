<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $modelLabel = 'Agenda Publik';
    protected static ?string $pluralModelLabel = 'Agenda Publik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Informasi Utama')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kegiatan')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->readOnly(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi Kegiatan')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Section::make('Waktu & Lokasi')->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('Waktu Selesai'),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi (Fisik / Link Online)')
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Status & Visual')->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Dijadwalkan',
                                'ongoing' => 'Sedang Berlangsung',
                                'completed' => 'Selesai',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->default('scheduled')
                            ->required(),
                        Forms\Components\FileUpload::make('banner')
                            ->label('Banner Kegiatan')
                            ->image()
                            ->directory('agendas'),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner')->label('Banner')->circular(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('start_time')->label('Mulai')->dateTime('d M Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'ongoing' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Dijadwalkan',
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}