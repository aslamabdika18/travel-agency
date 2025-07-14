<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyBookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Booking Report';
    
    protected static ?string $description = 'Number of bookings per month in the current year';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getData(): array
    {
        // Get booking data per month for the current year
        $bookingsByMonth = Booking::select(
                DB::raw('MONTH(booking_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('booking_date', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(booking_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        // Prepare data for all months (1-12)
        $labels = [];
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create(null, $month)->format('F');
            $labels[] = $monthName;
            $data[] = $bookingsByMonth[$month]->total ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Number of Bookings',
                    'data' => $data,
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'scales' => [
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Month',
                            'font' => [
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                    'y' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Number of Bookings',
                            'font' => [
                                'weight' => 'bold',
                            ],
                        ],
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1,
                            'precision' => 0,
                            'callback' => 'function(value) { return Math.round(value); }',
                        ],
                    ],
                ],
            ],
        ];
    }
}