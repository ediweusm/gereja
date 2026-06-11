<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberAssistanceResource\Pages;
use App\Models\MemberAssistance;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MemberAssistanceResource extends Resource
{
    protected static ?string $model = MemberAssistance::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Penyaluran Diakonia';

    public static function getModelLabel(): string
    {
        return 'Penyaluran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penyaluran Diakonia';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Penyaluran Diakonia')
                    ->description('Masukkan tanggal penyaluran, penerima bantuan, dan jumlah nominal bantuan diakonia.')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),

                        Select::make('member_id')
                            ->label('Jemaat Penerima Bantuan')
                            ->relationship('member', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullName)
                            ->searchable(['first_name', 'middle_name', 'last_name'])
                            ->preload()
                            ->required(),

                        TextInput::make('amount')
                            ->label('Nominal Bantuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ]),

                Section::make('Akun & Keterangan Jurnal (Double-Entry)')
                    ->description('Tentukan pos beban bantuan diakonia (debit) dan pos sumber dana kas/bank (kredit).')
                    ->columns(2)
                    ->schema([
                        Select::make('expense_account_id')
                            ->label('Beban Diakonia (Debit)')
                            ->options(fn () => Account::where('code', 'like', '412%')->whereDoesntHave('children')->where('is_active', true)->get()->pluck('fullName', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('cash_account_id')
                            ->label('Sumber Dana Kas/Bank (Kredit)')
                            ->options(fn () => Account::where('code', 'like', '1%')->whereDoesntHave('children')->where('is_active', true)->get()->pluck('fullName', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('description')
                            ->label('Keterangan / Tujuan Bantuan')
                            ->placeholder('Contoh: Penyaluran bantuan kesehatan diakonia kepada Sdr. David Lado')
                            ->required()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member')
                    ->label('Penerima Bantuan')
                    ->getStateUsing(fn ($record) => $record->member?->fullName ?? '-')
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->whereHas('member', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah Uang')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.'))
                    ->sortable(),

                TextColumn::make('journal.transaction_number')
                    ->label('No. Jurnal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->url(fn (MemberAssistance $record) => route('diakonia.receipt', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMemberAssistances::route('/'),
            'create' => Pages\CreateMemberAssistance::route('/create'),
            'edit' => Pages\EditMemberAssistance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['member', 'journal']);
    }
}
