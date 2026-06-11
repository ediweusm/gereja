<?php

namespace App\Filament\Widgets;

use App\Models\Family;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DemographicStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Jemaat
        $totalMembers = Member::where('is_deceased', false)->count();

        // 2. Jemaat Laki-Laki
        $maleMembers = Member::where('is_deceased', false)->where('gender', 'L')->count();

        // 3. Jemaat Perempuan
        $femaleMembers = Member::where('is_deceased', false)->where('gender', 'P')->count();

        // 4. Total Kepala Keluarga
        $totalFamilies = Family::count();

        // 5. Keluarga Pra Sejahtera
        $praSejahteraFamilies = Family::needsAssistance()->count();

        // 6. Ulang Tahun Minggu Ini
        $daysOfThisWeek = [];
        $start = Carbon::now()->startOfWeek(); // Senin
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $daysOfThisWeek[] = [
                'month' => $date->month,
                'day' => $date->day,
            ];
        }

        $birthdaysThisWeek = Member::where('is_deceased', false)
            ->whereNotNull('birth_date')
            ->where(function ($q) use ($daysOfThisWeek) {
                foreach ($daysOfThisWeek as $day) {
                    $q->orWhere(function ($sub) use ($day) {
                        $sub->whereMonth('birth_date', $day['month'])
                            ->whereDay('birth_date', $day['day']);
                    });
                }
            })
            ->count();

        return [
            Stat::make('Total Jemaat', $totalMembers)
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('Jemaat Laki-Laki', $maleMembers)
                ->icon('heroicon-o-user')
                ->color('info'),
            Stat::make('Jemaat Perempuan', $femaleMembers)
                ->icon('heroicon-o-user')
                ->color('info'),
            Stat::make('Total Kepala Keluarga', $totalFamilies)
                ->icon('heroicon-o-home')
                ->color('info'),
            Stat::make('Keluarga Pra Sejahtera', $praSejahteraFamilies)
                ->icon('heroicon-o-shield-exclamation')
                ->color('warning'),
            Stat::make('Ulang Tahun Minggu Ini', $birthdaysThisWeek)
                ->icon('heroicon-o-cake')
                ->color('success'),
        ];
    }
}
