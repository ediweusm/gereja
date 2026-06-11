<?php

namespace App\Filament\Widgets;

use App\Models\JournalItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $now = Carbon::now();

        // Bantuan
        $bantuanWeek = $this->getExpenseSum(
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
            true
        );
        $bantuanMonth = $this->getExpenseSum(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
            true
        );
        $bantuanYear = $this->getExpenseSum(
            $now->copy()->startOfYear(),
            $now->copy()->endOfYear(),
            true
        );

        // Biaya Operasional (Other Expense accounts)
        $operasionalWeek = $this->getExpenseSum(
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
            false
        );
        $operasionalMonth = $this->getExpenseSum(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
            false
        );
        $operasionalYear = $this->getExpenseSum(
            $now->copy()->startOfYear(),
            $now->copy()->endOfYear(),
            false
        );

        return [
            // Row 1: Bantuan
            Stat::make('Bantuan Minggu Ini', $this->formatRupiah($bantuanWeek))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Bantuan Bulan Ini', $this->formatRupiah($bantuanMonth))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Bantuan Tahun Ini', $this->formatRupiah($bantuanYear))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),

            // Row 2: Biaya Operasional
            Stat::make('Biaya Operasional Minggu Ini', $this->formatRupiah($operasionalWeek))
                ->color('warning')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Biaya Operasional Bulan Ini', $this->formatRupiah($operasionalMonth))
                ->color('warning')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Biaya Operasional Tahun Ini', $this->formatRupiah($operasionalYear))
                ->color('warning')
                ->icon('heroicon-o-arrow-trending-down'),
        ];
    }

    private function getExpenseSum(Carbon $startDate, Carbon $endDate, bool $isBantuan): float
    {
        $query = JournalItem::query()
            ->join('journals', 'journal_items.journal_id', '=', 'journals.id')
            ->join('accounts', 'journal_items.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'Expense')
            ->whereBetween('journals.transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);

        if ($isBantuan) {
            $query->where(function ($q) {
                $q->where('accounts.name', 'like', '%Bantuan%')
                  ->orWhere('accounts.name', 'like', '%Diakonia%')
                  ->orWhere('accounts.name', 'like', '%Diakona%');
            });
        } else {
            $query->where(function ($q) {
                $q->where('accounts.name', 'not like', '%Bantuan%')
                  ->where('accounts.name', 'not like', '%Diakonia%')
                  ->where('accounts.name', 'not like', '%Diakona%');
            });
        }

        return (float) $query->selectRaw('SUM(journal_items.debit) - SUM(journal_items.credit) as balance')
            ->value('balance') ?? 0.0;
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
