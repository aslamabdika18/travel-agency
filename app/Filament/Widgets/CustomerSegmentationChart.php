<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CustomerSegmentationChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation';
    
    protected static ?string $description = 'Distribution of customers based on booking frequency';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getType(): string
    {
        return 'pie';
    }
    
    protected function getData(): array
    {
        // Get customer segmentation data based on booking count
        $customerSegments = DB::table('bookings')
            ->select('user_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('user_id')
            ->get()
            ->groupBy(function ($item) {
                // Segmentation based on booking count
                if ($item->booking_count == 1) {
                    return 'One-time';
                } elseif ($item->booking_count >= 2 && $item->booking_count <= 3) {
                    return 'Occasional';
                } elseif ($item->booking_count >= 4 && $item->booking_count <= 6) {
                    return 'Regular';
                } else {
                    return 'Loyal';
                }
            })
            ->map(function ($group) {
                return $group->count();
            });
        
        // Add customers who have never made a booking
        $totalCustomers = User::role('customer')->count();
        $customersWithBookings = Booking::select('user_id')->distinct()->count('user_id');
        $customersWithoutBookings = $totalCustomers - $customersWithBookings;
        
        if ($customersWithoutBookings > 0) {
            $customerSegments->put('Inactive', $customersWithoutBookings);
        }
        
        // Ensure all segments exist, even if their value is 0
        $segments = ['Inactive', 'One-time', 'Occasional', 'Regular', 'Loyal'];
        foreach ($segments as $segment) {
            if (!$customerSegments->has($segment)) {
                $customerSegments->put($segment, 0);
            }
        }
        
        // Colors for each segment
        $backgroundColors = [
            'Inactive' => 'rgba(201, 203, 207, 0.6)',
            'One-time' => 'rgba(255, 99, 132, 0.6)',
            'Occasional' => 'rgba(255, 159, 64, 0.6)',
            'Regular' => 'rgba(75, 192, 192, 0.6)',
            'Loyal' => 'rgba(54, 162, 235, 0.6)',
        ];
        
        $borderColors = [
            'Inactive' => 'rgb(201, 203, 207)',
            'One-time' => 'rgb(255, 99, 132)',
            'Occasional' => 'rgb(255, 159, 64)',
            'Regular' => 'rgb(75, 192, 192)',
            'Loyal' => 'rgb(54, 162, 235)',
        ];
        
        // Prepare data for the chart
        $labels = $customerSegments->keys()->toArray();
        $data = $customerSegments->values()->toArray();
        $colors = [];
        $borders = [];
        
        foreach ($labels as $label) {
            $colors[] = $backgroundColors[$label] ?? 'rgba(0, 0, 0, 0.1)';
            $borders[] = $borderColors[$label] ?? 'rgb(0, 0, 0)';
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Number of Customers',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $borders,
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {\n"
                            . "    var label = context.label || '';\n"
                            . "    var value = context.raw || 0;\n"
                            . "    var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);\n"
                            . "    var percentage = Math.round((value / total) * 100);\n"
                            . "    return label + ': ' + value + ' customers (' + percentage + '%)';"
                            . "}",
                    ],
                ],
            ],
        ];
    }
}