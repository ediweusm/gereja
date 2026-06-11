<?php

namespace App\Filament\Resources\FamilyResource\RelationManagers;

use App\Models\DataDictionary;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'Anggota Keluarga';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Biodata')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label('Nama Depan')
                                            ->required()
                                            ->maxLength(100),
                                        TextInput::make('middle_name')
                                            ->label('Nama Tengah')
                                            ->maxLength(100),
                                        TextInput::make('last_name')
                                            ->label('Nama Belakang')
                                            ->maxLength(100),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nik')
                                            ->label('NIK')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20),
                                        Select::make('gender')
                                            ->label('Jenis Kelamin')
                                            ->options([
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                            ])
                                            ->required(),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('birth_place')
                                            ->label('Tempat Lahir')
                                            ->maxLength(100),
                                        DatePicker::make('birth_date')
                                            ->label('Tanggal Lahir'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('family_position_id')
                                            ->label('Hubungan Keluarga')
                                            ->options(fn () => DataDictionary::active()->category('family_position')->pluck('label', 'id'))
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('marital_status_id')
                                            ->label('Status Pernikahan')
                                            ->options(fn () => DataDictionary::active()->category('marital_status')->pluck('label', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        Select::make('education_id')
                                            ->label('Pendidikan')
                                            ->options(fn () => DataDictionary::active()->category('education')->pluck('label', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        Select::make('occupation_id')
                                            ->label('Pekerjaan')
                                            ->options(fn () => DataDictionary::active()->category('occupation')->pluck('label', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('income')
                                            ->label('Penghasilan')
                                            ->numeric()
                                            ->default(0),
                                    ]),
                            ]),
                        Tab::make('Kegerejaan')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('church_role_id')
                                            ->label('Jabatan Struktural')
                                            ->options(fn () => DataDictionary::active()->category('church_role')->pluck('label', 'id'))
                                            ->searchable()
                                            ->preload(),
                                        Select::make('membership_status_id')
                                            ->label('Status Keanggotaan/Penokohan')
                                            ->options(fn () => DataDictionary::active()->category('membership_status')->pluck('label', 'id'))
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),
                        Tab::make('Sakramen')
                            ->schema([
                                Toggle::make('status_baptis')
                                    ->label('Sudah Baptis')
                                    ->live(),
                                Grid::make(3)
                                    ->schema([
                                        DatePicker::make('baptism_date')
                                            ->label('Tanggal Baptis'),
                                        TextInput::make('baptism_church')
                                            ->label('Gereja Baptis')
                                            ->maxLength(150),
                                        TextInput::make('baptism_pastor')
                                            ->label('Pendeta Baptis')
                                            ->maxLength(100),
                                    ])
                                    ->visible(fn (callable $get) => (bool)$get('status_baptis')),
                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('sidi_date')
                                            ->label('Tanggal Sidi'),
                                        TextInput::make('sidi_church')
                                            ->label('Gereja Sidi')
                                            ->maxLength(150),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('marriage_date')
                                            ->label('Tanggal Pernikahan'),
                                        TextInput::make('marriage_church')
                                            ->label('Gereja Pernikahan')
                                            ->maxLength(150),
                                    ])
                                    ->visible(function (callable $get) {
                                        $statusId = $get('marital_status_id');
                                        if (! $statusId) {
                                            return false;
                                        }
                                        $status = DataDictionary::find($statusId);
                                        return $status && in_array($status->code, ['married', 'married-civil']);
                                    }),
                            ]),
                        Tab::make('Kematian')
                            ->schema([
                                Toggle::make('is_deceased')
                                    ->label('Meninggal Dunia')
                                    ->live(),
                                DatePicker::make('death_date')
                                    ->label('Tanggal Kematian')
                                    ->visible(fn (callable $get) => (bool)$get('is_deceased')),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                TextColumn::make('fullName')
                    ->label('Nama Lengkap')
                    ->state(fn (Member $record) => $record->fullName)
                    ->searchable(['first_name', 'middle_name', 'last_name']),
                TextColumn::make('familyPosition.label')
                    ->label('Hubungan Keluarga')
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('membershipStatus.label')
                    ->label('Status Anggota')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
