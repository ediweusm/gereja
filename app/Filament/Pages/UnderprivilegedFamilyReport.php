<?php

namespace App\Filament\Pages;

use App\Models\Family;
use App\Models\Member;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnderprivilegedFamilyReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Administrasi Jemaat';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Keluarga Pra Sejahtera';

    protected static string $view = 'filament.pages.underprivileged-family-report';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn () => route('report.underprivileged.print'))
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Family::query()
                    ->with(['houseStatus', 'houseCategory', 'rayon', 'members.familyPosition'])
                    ->needsAssistance()
            )
            ->columns([
                TextColumn::make('family_number')
                    ->label('No KK')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('head_of_family')
                    ->label('Kepala Keluarga')
                    ->state(function (Family $record): string {
                        $head = $record->members->first(function ($member) {
                            return $member->familyPosition?->code === 'suami';
                        });
                        return $head ? $head->full_name : '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('members', function (Builder $q) use ($search) {
                            $q->whereHas('familyPosition', function ($posQ) {
                                $posQ->where('code', 'suami');
                            })->where(function ($nameQ) use ($search) {
                                $nameQ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        });
                    }),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('rayon.name')
                    ->label('Rayon')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('houseStatus.label')
                    ->label('Status Rumah')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('houseCategory.label')
                    ->label('Kondisi Rumah')
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('rayon_id')
                    ->label('Rayon')
                    ->relationship('rayon', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('house_status_id')
                    ->label('Status Rumah')
                    ->relationship('houseStatus', 'label', fn (Builder $query) => $query->category('house_status')->active())
                    ->searchable()
                    ->preload(),

                SelectFilter::make('house_category_id')
                    ->label('Kondisi Rumah')
                    ->relationship('houseCategory', 'label', fn (Builder $query) => $query->category('house_category')->active())
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                'houseStatus.label',
                'houseCategory.label',
            ])
            ->striped();
    }
}
