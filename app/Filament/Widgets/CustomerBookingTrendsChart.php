<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerBookingTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Booking Trends';
    
    protected static ?string $description = 'Number of new vs repeat customers per month';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getData(): array
    {
        // Get data for the last 12 months
        $months = collect();
        $labels = [];
        
        // Create array for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $months->push([
                'date' => $date,
                'month' => $date->format('M Y'),
                'start' => $date->format('Y-m-d'),
                'end' => $date->copy()->endOfMonth()->format('Y-m-d'),
            ]);
            $labels[] = $date->format('M Y');
        }
        
        // Data for new customers (first-time bookers)
        $newCustomerData = [];
        
        // Data for repeat customers (repeat bookers)
        $repeatCustomerData = [];
        
        foreach ($months as $month) {
            // Get all bookings in this month
            $bookingsThisMonth = Booking::whereBetween('booking_date', [$month['start'], $month['end']])
                ->get();
            
            // Get user_ids who made bookings this month
            $userIdsThisMonth = $bookingsThisMonth->pluck('user_id')->unique();
            
            // Count new customers (who have never booked before this month)
            $newCustomers = 0;
            $repeatCustomers = 0;
            
            foreach ($userIdsThisMonth as $userId) {
                // Check if this user has booked before this month
                $earlierBookings = Booking::where('user_id', $userId)
                    ->where('booking_date', '<', $month['start'])
                    ->exists();
                
                if ($earlierBookings) {
                    // This is a repeat customer
                    $repeatCustomers++;
                } else {
                    // This is a new customer
                    $newCustomers++;
                }
            }
            
            $newCustomerData[] = $newCustomers;
            $repeatCustomerData[] = $repeatCustomers;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => $newCustomerData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Repeat Customers',
                    'data' => $repeatCustomerData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                ],
            ],
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Customers',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'footer' => "function(tooltipItems) {\n"
                            . "    var sum = 0;\n"
                            . "    tooltipItems.forEach(function(tooltipItem) {\n"
                            . "        sum += tooltipItem.parsed.y;\n"
                            . "    });\n"
                            . "    return 'Total: ' + sum + ' customers';\n"
                            . "}",
                    ],
                ],
            ],
        ];
    }
}