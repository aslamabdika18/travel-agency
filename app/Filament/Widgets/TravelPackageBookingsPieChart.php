<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\TravelPackage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TravelPackageBookingsPieChart extends ChartWidget
{
    protected static ?string $heading = 'Travel Package Booking Distribution';
    
    protected static ?string $description = 'Percentage of bookings for each travel package';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'pie';
    }
    
    protected function getData(): array
    {
        // Optimized query untuk menghindari N+1 query problem
        // Menggunakan join untuk mendapatkan nama travel package sekaligus
        $bookings = Booking::select(
                'travel_packages.name as package_name',
                DB::raw('count(*) as total')
            )
            ->join('travel_packages', 'bookings.travel_package_id', '=', 'travel_packages.id')
            ->groupBy('travel_packages.id', 'travel_packages.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(201, 203, 207, 0.8)',
            'rgba(255, 99, 255, 0.8)',
            'rgba(54, 255, 235, 0.8)',
            'rgba(255, 178, 86, 0.8)',
        ];
        
        foreach ($bookings as $booking) {
            $labels[] = $booking->package_name;
            $data[] = $booking->total;
        }
        
        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'right',
                        'title' => [
                            'display' => true,
                            'text' => 'Paket Travel',
                            'font' => [
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => "function(context) {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' booking (' + percentage + '%)';
                            }",
                        ],
                    ],
                ],
            ],
        ];
    }
}