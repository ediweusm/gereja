<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DataDictionary;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Administrasi Jemaat';

    public static function getModelLabel(): string
    {
        return 'Jadwal Ibadah';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jadwal Ibadah';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Ibadah')
                    ->description('Tentukan detail kegiatan ibadah atau persekutuan.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Ibadah / Kegiatan')
                                    ->required()
                                    ->placeholder('Contoh: Ibadah Minggu')
                                    ->maxLength(255),
                                TextInput::make('theme')
                                    ->label('Tema')
                                    ->nullable()
                                    ->placeholder('Contoh: Hidup dalam Kasih')
                                    ->maxLength(255),
                            ]),
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('event_date')
                                    ->label('Tanggal')
                                    ->required(),
                                TimePicker::make('start_time')
                                    ->label('Waktu Mulai')
                                    ->required(),
                                Select::make('event_type')
                                    ->label('Jenis Kegiatan')
                                    ->options(fn () => DataDictionary::active()->category('worship')->pluck('label', 'code'))
                                    ->live()
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('mode')
                                    ->label('Mode')
                                    ->options(fn () => DataDictionary::active()->category('worship_venue')->pluck('label', 'code'))
                                    ->default('Onsite')
                                    ->required(),
                                Textarea::make('location_notes')
                                    ->label('Catatan Lokasi')
                                    ->placeholder('Contoh: Gedung Gereja Utama / Ruang Aula Lt. 2')
                                    ->rows(2)
                                    ->nullable(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('rayon_id')
                                    ->relationship('rayon', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->label('Rayon'),
                                Select::make('host_family_id')
                                    ->relationship(
                                        name: 'hostFamily',
                                        titleAttribute: 'family_number',
                                        modifyQueryUsing: fn ($query) => $query->with(['members.familyPosition'])
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $head = $record->members->first(fn($m) => $m->familyPosition?->code === 'suami') ?? $record->members->first();
                                        $headName = $head ? $head->fullName : '-';
                                        return "No. KK: {$record->family_number} ({$headName})";
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->label('Keluarga Penerima (Host Family)'),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('event_type') === 'Persekutuan Wilayah'),
                    ]),

                Section::make('Jadwal Petugas')
                    ->description('Tentukan petugas yang melayani dalam ibadah / kegiatan ini.')
                    ->schema([
                        Repeater::make('assignments')
                            ->relationship('assignments')
                            ->schema([
                                Select::make('ministry_role_id')
                                    ->relationship('ministryRole', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Peran Pelayanan'),
                                Select::make('member_id')
                                    ->relationship(
                                        name: 'member',
                                        titleAttribute: 'first_name',
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullName)
                                    ->searchable(['first_name', 'middle_name', 'last_name'])
                                    ->preload()
                                    ->nullable()
                                    ->label('Petugas (Jemaat)')
                                    ->helperText('Pilih jika petugas adalah jemaat internal'),
                                TextInput::make('guest_name')
                                    ->nullable()
                                    ->label('Nama Tamu')
                                    ->helperText('Isi jika petugas adalah tamu/eksternal'),
                            ])
                            ->columns(3)
                            ->label('Daftar Petugas')
                            ->defaultItems(0),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i'),
                TextColumn::make('name')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('theme')
                    ->label('Tema')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('mode')
                    ->label('Mode')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'onsite' => 'success',
                        'online' => 'warning',
                        'hybrid', 'hibryd' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->defaultSort('event_date', 'desc')
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
