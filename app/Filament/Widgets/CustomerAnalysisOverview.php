<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerAnalysisOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total number of customers (users with customer role)
        $totalCustomers = User::role('customer')->count();

        // New customers (joined in the last 30 days)
        $newCustomers = User::role('customer')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Percentage of new customers
        $newCustomerPercentage = $totalCustomers > 0 ? ($newCustomers / $totalCustomers) * 100 : 0;

        // Number of customers who have made bookings
        $customersWithBookings = Booking::select('user_id')
            ->distinct()
            ->count('user_id');

        // Percentage of customers who have made bookings
        $bookingCustomerPercentage = $totalCustomers > 0 ? ($customersWithBookings / $totalCustomers) * 100 : 0;

        // Average number of bookings per customer
        $totalBookings = Booking::count();
        $avgBookingsPerCustomer = $customersWithBookings > 0 ? $totalBookings / $customersWithBookings : 0;

        // Customer with highest transaction value
        $topCustomer = Booking::select('user_id', DB::raw('SUM(total_price) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->with('user:id,name')
            ->first();

        $topCustomerName = $topCustomer ? $topCustomer->user->name : 'No data available';
        $topCustomerSpent = $topCustomer ? $topCustomer->total_spent : 0;

        // Repeat customers (more than 1 booking)
        $repeatCustomers = DB::table('bookings')
            ->select('user_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('user_id')
            ->having('booking_count', '>', 1)
            ->count();

        // Percentage of repeat customers from total customers who have booked
        $repeatCustomerPercentage = $customersWithBookings > 0 ? ($repeatCustomers / $customersWithBookings) * 100 : 0;

        return [
            Stat::make('Total Customers', number_format((int)$totalCustomers, 0, ',', '.'))
                ->description($newCustomers . ' new customers in the last 30 days (' . number_format((float)$newCustomerPercentage, 1) . '%)')
                ->descriptionIcon('heroicon-m-user-plus')
                ->chart([
                    $totalCustomers - $newCustomers,
                    $totalCustomers - ($newCustomers * 0.75),
                    $totalCustomers - ($newCustomers * 0.5),
                    $totalCustomers - ($newCustomers * 0.25),
                    $totalCustomers,
                ])
                ->color('success'),

            Stat::make('Average Bookings per Customer', number_format((float)$avgBookingsPerCustomer, 1, ',', '.'))
                ->description($customersWithBookings . ' customers have made bookings (' . number_format((float)$bookingCustomerPercentage, 1) . '%)')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([
                    $avgBookingsPerCustomer * 0.6,
                    $avgBookingsPerCustomer * 0.7,
                    $avgBookingsPerCustomer * 0.8,
                    $avgBookingsPerCustomer * 0.9,
                    $avgBookingsPerCustomer,
                ])
                ->color('warning'),

            Stat::make('Repeat Customers', $repeatCustomers)
                ->description(number_format((float)$repeatCustomerPercentage, 1) . '% of customers make repeat purchases')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([
                    $repeatCustomers * 0.6,
                    $repeatCustomers * 0.7,
                    $repeatCustomers * 0.8,
                    $repeatCustomers * 0.9,
                    $repeatCustomers,
                ])
                ->color('primary'),

            Stat::make('Top Customer', $topCustomerName)
                ->description('Total transactions: Rp ' . number_format((float)$topCustomerSpent, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('danger'),
        ];
    }
}