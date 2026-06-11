<?php

namespace App\Filament\Pages;

use App\Models\Member;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BirthdayReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Administrasi Jemaat';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Ulang Tahun Minggu Ini';

    protected static string $view = 'filament.pages.birthday-report';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('report.birthdays.print'))
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        $daysOfThisWeek = [];
        $start = Carbon::now()->startOfWeek(); // Senin
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $daysOfThisWeek[] = [
                'month' => $date->month,
                'day' => $date->day,
            ];
        }

        $query = Member::query()
            ->with(['family.rayon'])
            ->where('is_deceased', false)
            ->whereNotNull('birth_date')
            ->where(function (Builder $q) use ($daysOfThisWeek) {
                foreach ($daysOfThisWeek as $day) {
                    $q->orWhere(function ($sub) use ($day) {
                        $sub->whereMonth('birth_date', $day['month'])
                            ->whereDay('birth_date', $day['day']);
                    });
                }
            });

        $cases = [];
        foreach ($daysOfThisWeek as $index => $day) {
            $cases[] = "WHEN MONTH(birth_date) = {$day['month']} AND DAY(birth_date) = {$day['day']} THEN {$index}";
        }
        $caseSql = "CASE " . implode(' ', $cases) . " ELSE 99 END";
        $query->orderByRaw($caseSql);

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->getStateUsing(fn (Member $record): string => $record->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('birth_date')
                    ->label('Tanggal Ulang Tahun')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $start = Carbon::now()->startOfWeek();
                        for ($i = 0; $i < 7; $i++) {
                            $date = $start->copy()->addDays($i);
                            if ($date->month === $state->month && $date->day === $state->day) {
                                return $date->translatedFormat('l, d F');
                            }
                        }
                        return $state->translatedFormat('l, d F');
                    })
                    ->sortable(),

                TextColumn::make('age_this_year')
                    ->label('Umur Tahun Ini')
                    ->state(fn (Member $record): int => Carbon::now()->year - $record->birth_date?->year),

                TextColumn::make('family.rayon.name')
                    ->label('Rayon')
                    ->placeholder('-'),

                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->default('-'),
            ])
            ->striped();
    }
}
