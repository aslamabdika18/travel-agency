<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Current month revenue
        $currentMonthRevenue = Booking::whereMonth('booking_date', Carbon::now()->month)
            ->whereYear('booking_date', Carbon::now()->year)
            ->sum('total_price');

        // Last month revenue
        $lastMonthRevenue = Booking::whereMonth('booking_date', Carbon::now()->subMonth()->month)
            ->whereYear('booking_date', Carbon::now()->subMonth()->year)
            ->sum('total_price');

        // Monthly revenue change percentage
        $monthlyChangePercentage = 0;
        if ($lastMonthRevenue > 0) {
            $monthlyChangePercentage = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // Current year revenue
        $currentYearRevenue = Booking::whereYear('booking_date', Carbon::now()->year)
            ->sum('total_price');

        // Last year revenue
        $lastYearRevenue = Booking::whereYear('booking_date', Carbon::now()->subYear()->year)
            ->sum('total_price');

        // Yearly revenue change percentage
        $yearlyChangePercentage = 0;
        if ($lastYearRevenue > 0) {
            $yearlyChangePercentage = (($currentYearRevenue - $lastYearRevenue) / $lastYearRevenue) * 100;
        }

        // Average monthly revenue for this year
        $monthsPassedThisYear = min(Carbon::now()->month, 12);
        $averageMonthlyRevenue = $monthsPassedThisYear > 0 ? $currentYearRevenue / $monthsPassedThisYear : 0;

        return [
            Stat::make('Current Month Revenue', 'Rp ' . number_format((float)$currentMonthRevenue, 0, ',', '.'))
                ->description('Revenue for ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([
                    $currentMonthRevenue * 0.6,
                    $currentMonthRevenue * 0.7,
                    $currentMonthRevenue * 0.8,
                    $currentMonthRevenue * 0.9,
                    $currentMonthRevenue,
                ])
                ->color('success'),

            Stat::make('Current Year Revenue', 'Rp ' . number_format((float)$currentYearRevenue, 0, ',', '.'))
                ->description($yearlyChangePercentage >= 0
                    ? number_format((float)abs($yearlyChangePercentage), 2) . '% increase from last year'
                    : number_format((float)abs($yearlyChangePercentage), 2) . '% decrease from last year')
                ->descriptionIcon($yearlyChangePercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($yearlyChangePercentage >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastYearRevenue * 0.8,
                    $lastYearRevenue * 0.9,
                    $lastYearRevenue,
                    $currentYearRevenue * 0.9,
                    $currentYearRevenue,
                ]),

            Stat::make('Average Monthly Revenue', 'Rp ' . number_format((float)$averageMonthlyRevenue, 0, ',', '.'))
                ->description('Based on ' . $monthsPassedThisYear . ' months in ' . Carbon::now()->year)
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),
        ];
    }
}
