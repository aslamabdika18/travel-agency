<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class YearlyBookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Yearly Booking Report';
    
    protected static ?string $description = 'Number of bookings per year in the last 5 years';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getData(): array
    {
        // Get current year and previous 4 years
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 4;
        
        // Get booking data per year for the last 5 years
        $bookingsByYear = Booking::select(
                DB::raw('YEAR(booking_date) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('booking_date', '>=', $startYear)
            ->groupBy(DB::raw('YEAR(booking_date)'))
            ->orderBy('year')
            ->get()
            ->keyBy('year');
        
        // Prepare data for the last 5 years
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];
        
        // Colors for chart
        $colors = [
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
        ];
        
        $borders = [
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(255, 99, 132)',
        ];
        
        for ($i = 0; $i < 5; $i++) {
            $year = $startYear + $i;
            $labels[] = (string) $year;
            $data[] = $bookingsByYear[$year]->total ?? 0;
            $backgroundColors[] = $colors[$i];
            $borderColors[] = $borders[$i];
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Number of Bookings',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'scales' => [
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Year',
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