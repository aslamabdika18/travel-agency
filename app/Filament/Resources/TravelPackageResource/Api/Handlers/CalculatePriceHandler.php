<?php

namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use App\Filament\Resources\TravelPackageResource;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Rupadana\ApiService\Http\Handlers;

class CalculatePriceHandler extends Handlers
{
    public static string | null $uri = '/calculate-price/{slug}';
    public static string | null $resource = TravelPackageResource::class;
    public static bool $public = true;

    /**
     * Calculate price for a travel package based on person count
     * 
     * Endpoint ini dapat diakses oleh pengguna publik untuk menghitung harga
     * dari sebuah TravelPackage berdasarkan jumlah orang.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
    {
        // Cari travel package berdasarkan slug
        $slug = $request->route('slug');
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'person_count' => 'required|integer|min:1',
            'travel_date' => 'nullable|date|date_format:Y-m-d',
        ]);
        
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info('Calculate Price Request', [
            'slug' => $slug,
            'request_data' => $request->all(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cari travel package berdasarkan slug yang sudah didefinisikan
        $travelPackage = TravelPackage::where('slug', $slug)->first();

        if (!$travelPackage) {
            return static::sendNotFoundResponse();
        }

        try {
            // Hitung harga berdasarkan jumlah orang
            $personCount = $request->input('person_count');
            
            if (!$personCount) {
                throw new \InvalidArgumentException('Person count is required');
            }
            
            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info('Calculate Price Processing', [
                'person_count' => $personCount,
            ]);
            
            $priceData = $travelPackage->calculatePrice($personCount);

            // Format response sesuai dengan ApiPriceCalculationResponse
            $response = [
                'base_price' => (string) $priceData['base_price'],
                'additional_price' => (string) $priceData['additional_price'],
                'total_price' => (string) $priceData['total_price'],
                'formatted_base_price' => 'Rp ' . number_format((float)$priceData['base_price'], 0, ',', '.'),
                'formatted_additional_price' => 'Rp ' . number_format((float)$priceData['additional_price'], 0, ',', '.'),
                'formatted_total_price' => 'Rp ' . number_format((float)$priceData['total_price'], 0, ',', '.'),
                'breakdown' => [
                    'base_explanation' => "Harga dasar untuk {$priceData['base_person_count']} orang",
                    'additional_explanation' => $priceData['extra_persons'] > 0 
                        ? "Tambahan untuk {$priceData['extra_persons']} orang (Rp " . number_format((float)$priceData['additional_person_price'], 0, ',', '.') . " per orang)"
                        : "Tidak ada tambahan orang",
                    'total_explanation' => "Total harga untuk {$priceData['person_count']} orang",
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the price',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menentukan metode HTTP yang digunakan
     */
    public static function getMethod()
    {
        return Handlers::POST;
    }
}