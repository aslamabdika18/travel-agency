<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CustomerAnalysisOverview;
use App\Filament\Widgets\CustomerBookingTrendsChart;
use App\Filament\Widgets\CustomerSegmentationChart;
use App\Filament\Widgets\MonthlyBookingsChart;
use App\Filament\Widgets\RevenueStatsOverview;
use App\Filament\Widgets\TravelPackageBookingsChart;
use App\Filament\Widgets\TravelPackageBookingsPieChart;
use App\Filament\Widgets\TravelPackageStatsOverview;
use App\Filament\Widgets\YearlyBookingsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Define widgets to be displayed in the dashboard header
     */
    protected function getHeaderWidgets(): array
    {
        return [
            TravelPackageStatsOverview::class,
            CustomerAnalysisOverview::class,
            //RevenueStatsOverview::class,
        ];
    }

    /**
     * Define widgets to be displayed in the dashboard content area
     * @return array<class-string<\Filament\Widgets\Widget> | \Filament\Widgets\WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            MonthlyBookingsChart::class,
            YearlyBookingsChart::class,
            CustomerSegmentationChart::class,
            CustomerBookingTrendsChart::class,
            TravelPackageBookingsChart::class,
            TravelPackageBookingsPieChart::class,
        ];
    }
}
