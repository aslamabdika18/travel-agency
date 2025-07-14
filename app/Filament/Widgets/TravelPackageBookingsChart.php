<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\TravelPackage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TravelPackageBookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Most Popular Travel Packages';
    
    protected static ?string $description = 'Number of bookings for each travel package';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getData(): array
    {
        $bookings = Booking::select('travel_package_id', DB::raw('count(*) as total'))
            ->groupBy('travel_package_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];
        
        // Colors for chart
        $colors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
        ];
        
        $borderColors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 206, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 206, 86)',
            'rgb(75, 192, 192)',
        ];
        
        foreach ($bookings as $index => $booking) {
            $travelPackage = TravelPackage::find($booking->travel_package_id);
            if ($travelPackage) {
                $labels[] = $travelPackage->name;
                $data[] = $booking->total;
                $backgroundColors[] = $colors[$index % count($colors)];
                $borderColors[] = $borderColors[$index % count($borderColors)];
            }
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
                            'text' => 'Paket Travel',
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