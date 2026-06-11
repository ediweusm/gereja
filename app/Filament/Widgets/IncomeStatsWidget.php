<?php

namespace App\Filament\Widgets;

use App\Models\JournalItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class IncomeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $now = Carbon::now();

        $incomeWeek = $this->getIncomeSum(
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
        );

        $incomeMonth = $this->getIncomeSum(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        );

        $incomeYear = $this->getIncomeSum(
            $now->copy()->startOfYear(),
            $now->copy()->endOfYear()
        );

        return [
            Stat::make('Persembahan Minggu Ini', $this->formatRupiah($incomeWeek))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),
            Stat::make('Persembahan Bulan Ini', $this->formatRupiah($incomeMonth))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),
            Stat::make('Persembahan Tahun Ini', $this->formatRupiah($incomeYear))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),
        ];
    }

    private function getIncomeSum(Carbon $startDate, Carbon $endDate): float
    {
        return (float) JournalItem::query()
            ->join('journals', 'journal_items.journal_id', '=', 'journals.id')
            ->join('accounts', 'journal_items.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'Revenue')
            ->whereBetween('journals.transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
            ->selectRaw('SUM(journal_items.credit) - SUM(journal_items.debit) as balance')
            ->value('balance') ?? 0.0;
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
