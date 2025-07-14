<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\TravelPackage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TravelPackageStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total bookings
        $totalBookings = Booking::count();

        // Average booking price
        $averagePrice = Booking::avg('total_price') ?? 0;

        // Number of available travel packages
        $totalPackages = TravelPackage::count();

        // Most popular travel package
        $mostPopularPackage = Booking::select('travel_package_id', DB::raw('count(*) as total'))
            ->groupBy('travel_package_id')
            ->orderByDesc('total')
            ->first();

        $popularPackageName = 'No data available';
        $popularPackageCount = 0;

        if ($mostPopularPackage) {
            $travelPackage = TravelPackage::find($mostPopularPackage->travel_package_id);
            if ($travelPackage) {
                $popularPackageName = $travelPackage->name;
                $popularPackageCount = $mostPopularPackage->total;
            }
        }

        return [
            Stat::make('Total Bookings', $totalBookings)
                ->description('Number of bookings created')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([
                    $totalBookings * 0.6,
                    $totalBookings * 0.7,
                    $totalBookings * 0.8,
                    $totalBookings * 0.9,
                    $totalBookings,
                ])
                ->color('success'),

            Stat::make('Average Price', 'Rp ' . number_format((float)$averagePrice, 0, ',', '.'))
                ->description('Average booking price')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([
                    $averagePrice * 0.6,
                    $averagePrice * 0.7,
                    $averagePrice * 0.8,
                    $averagePrice * 0.9,
                    $averagePrice,
                ])
                ->color('warning'),

            Stat::make('Available Travel Packages', $totalPackages)
                ->description('Number of available travel packages')
                ->descriptionIcon('heroicon-m-map')
                ->chart([
                    $totalPackages * 0.6,
                    $totalPackages * 0.7,
                    $totalPackages * 0.8,
                    $totalPackages * 0.9,
                    $totalPackages,
                ])
                ->color('primary'),

            Stat::make('Most Popular Package', $popularPackageName)
                ->description($popularPackageCount . ' bookings')
                ->descriptionIcon('heroicon-m-star')
                ->color('danger'),
        ];
    }
}
