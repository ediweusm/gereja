<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 7;

    public static function getModelLabel(): string
    {
        return 'Kode Akun';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kode Akun';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Kode Akun')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Akun')
                    ->required()
                    ->maxLength(150),
                Select::make('type')
                    ->label('Tipe Akun')
                    ->required()
                    ->options([
                        'Asset' => 'Aset',
                        'Liability' => 'Kewajiban',
                        'Net Asset' => 'Aset Bersih',
                        'Revenue' => 'Pendapatan',
                        'Expense' => 'Beban',
                    ]),
                Select::make('restriction_type')
                    ->label('Pembatasan Dana')
                    ->required()
                    ->options([
                        'Tidak Terikat' => 'Tidak Terikat',
                        'Terikat Temporer' => 'Terikat Temporer',
                        'Terikat Permanen' => 'Terikat Permanen',
                    ]),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Induk Akun'),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Akun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Akun')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe Akun')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'Asset' => 'Aset',
                        'Liability' => 'Kewajiban',
                        'Net Asset' => 'Aset Bersih',
                        'Revenue' => 'Pendapatan',
                        'Expense' => 'Beban',
                    ][$state] ?? $state)
                    ->sortable(),
                TextColumn::make('restriction_type')
                    ->label('Pembatasan Dana')
                    ->badge(),
                TextColumn::make('parent.name')
                    ->label('Induk Akun'),
                ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Akun')
                    ->options([
                        'Asset' => 'Aset',
                        'Liability' => 'Kewajiban',
                        'Net Asset' => 'Aset Bersih',
                        'Revenue' => 'Pendapatan',
                        'Expense' => 'Beban',
                    ]),
                SelectFilter::make('restriction_type')
                    ->label('Pembatasan Dana')
                    ->options([
                        'Tidak Terikat' => 'Tidak Terikat',
                        'Terikat Temporer' => 'Terikat Temporer',
                        'Terikat Permanen' => 'Terikat Permanen',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Account $record, Tables\Actions\DeleteAction $action) {
                        if ($record->journalItems()->exists()) {
                            Notification::make()
                                ->title('Gagal Hapus')
                                ->body('Akun ini tidak dapat dihapus karena telah digunakan dalam transaksi jurnal.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action) {
                            $hasTransactions = $action->getRecords()->contains(fn (Account $account) => $account->journalItems()->exists());
                            if ($hasTransactions) {
                                Notification::make()
                                    ->title('Gagal Hapus Massal')
                                    ->body('Beberapa akun yang dipilih tidak dapat dihapus karena telah digunakan dalam transaksi jurnal.')
                                    ->danger()
                                    ->send();

                                $action->halt();
                            }
                        }),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
