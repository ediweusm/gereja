<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberMutationResource\Pages;
use App\Models\MemberMutation;
use App\Models\Member;
use App\Models\Rayon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MemberMutationResource extends Resource
{
    protected static ?string $model = MemberMutation::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Administrasi Jemaat';
    protected static ?string $modelLabel = 'Perpindahan Jemaat';
    protected static ?string $pluralModelLabel = 'Perpindahan Jemaat';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Perpindahan Jemaat';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Perpindahan Jemaat';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Perpindahan Jemaat')
                    ->description('Masukkan rincian data perpindahan jemaat.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('member_id')
                                    ->relationship('member', 'first_name')
                                    ->getOptionLabelFromRecordUsing(fn (Member $record) => $record->fullName)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Anggota Jemaat'),
                                Select::make('mutation_type')
                                    ->options([
                                        'Atestasi Masuk' => 'Atestasi Masuk',
                                        'Atestasi Keluar' => 'Atestasi Keluar',
                                        'Pindah Rayon' => 'Pindah Rayon',
                                        'Titipan' => 'Titipan',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->required()
                                    ->live()
                                    ->label('Jenis Mutasi'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('mutation_date')
                                    ->required()
                                    ->default(now())
                                    ->label('Tanggal Mutasi'),
                                TextInput::make('document_number')
                                    ->maxLength(100)
                                    ->label('Nomor Surat / Dokumen'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('old_rayon_id')
                                    ->relationship('oldRayon', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Rayon Lama')
                                    ->visible(fn (callable $get) => $get('mutation_type') === 'Pindah Rayon'),
                                Select::make('new_rayon_id')
                                    ->relationship('newRayon', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Rayon Baru')
                                    ->visible(fn (callable $get) => $get('mutation_type') === 'Pindah Rayon'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('origin_church')
                                    ->maxLength(150)
                                    ->label('Gereja Asal')
                                    ->visible(fn (callable $get) => in_array($get('mutation_type'), ['Atestasi Masuk', 'Titipan'])),
                                TextInput::make('destination_church')
                                    ->maxLength(150)
                                    ->label('Gereja Tujuan')
                                    ->visible(fn (callable $get) => in_array($get('mutation_type'), ['Atestasi Keluar', 'Titipan'])),
                            ]),
                        Textarea::make('reason')
                            ->label('Alasan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.first_name')
                    ->label('Nama Anggota')
                    ->state(fn ($record) => $record->member?->fullName)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('mutation_type')
                    ->label('Jenis Mutasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Atestasi Masuk' => 'success',
                        'Atestasi Keluar' => 'danger',
                        'Pindah Rayon' => 'info',
                        'Titipan' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mutation_date')
                    ->label('Tanggal Mutasi')
                    ->date()
                    ->sortable(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberMutations::route('/'),
            'create' => Pages\CreateMemberMutation::route('/create'),
            'edit' => Pages\EditMemberMutation::route('/{record}/edit'),
        ];
    }
}
