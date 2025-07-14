<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Filament\Resources\TravelPackageResource\Api\Transformers\TravelPackageTransformer;

/**
 * PublicTravelPackageController - Controller untuk akses publik travel packages
 *
 * Controller ini menyediakan endpoint publik untuk mengakses data travel packages
 * tanpa memerlukan autentikasi. Hanya operasi read-only yang diizinkan.
 */
class PublicTravelPackageController extends Controller
{
    /**
     * Mendapatkan semua travel packages
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $packages = TravelPackage::with([
                'media',
                'itineraries',
                'travelIncludes',
                'travelExcludes',
                'reviews.user' // Eager load user untuk menghindari N+1 query di resource
            ])->get();

            $transformedPackages = $packages->map(function ($package) {
                return (new TravelPackageTransformer($package))->toArray(request());
            });

            return response()->json($transformedPackages);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch travel packages',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan travel package berdasarkan ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $package = TravelPackage::with([
                'media',
                'itineraries',
                'travelIncludes',
                'travelExcludes',
                'reviews.user' // Eager load user untuk menghindari N+1 query di resource
            ])->findOrFail($id);

            $transformedPackage = (new TravelPackageTransformer($package))->toArray(request());

            return response()->json($transformedPackage);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Travel package not found',
                'message' => "Travel package with ID {$id} not found"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch travel package',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan travel package berdasarkan slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $package = TravelPackage::with([
                'media',
                'itineraries',
                'travelIncludes',
                'travelExcludes',
                'reviews.user' // Eager load user untuk menghindari N+1 query di resource
            ])->where('slug', $slug)->firstOrFail();

            $transformedPackage = (new TravelPackageTransformer($package))->toArray(request());

            return response()->json($transformedPackage);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Travel package not found',
                'message' => "Travel package with slug '{$slug}' not found"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch travel package',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung harga travel package berdasarkan jumlah orang
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function calculatePrice(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'person_count' => 'required|integer|min:1'
            ]);

            $package = TravelPackage::findOrFail($id);
            $personCount = $request->input('person_count');

            // Hitung harga berdasarkan base person count dan additional person price
            $basePrice = (float) $package->price;
            $basePerson = $package->base_person_count;
            $additionalPersonPrice = (float) $package->additional_person_price;

            if ($personCount <= $basePerson) {
                $totalPrice = $basePrice;
            } else {
                $additionalPersons = $personCount - $basePerson;
                $totalPrice = $basePrice + ($additionalPersons * $additionalPersonPrice);
            }

            // Apply discount if available
            if ($package->discount_amount && $package->discount_amount > 0) {
                $totalPrice = max(0, $totalPrice - $package->discount_amount);
            }

            return response()->json([
                'package_id' => $package->id,
                'package_name' => $package->name,
                'person_count' => $personCount,
                'base_price' => $basePrice,
                'base_person_count' => $basePerson,
                'additional_person_price' => $additionalPersonPrice,
                'discount_amount' => $package->discount_amount ?? 0,
                'total_price' => $totalPrice,
                'price_breakdown' => [
                    'base_price' => $basePrice,
                    'additional_persons' => $personCount > $basePerson ? $personCount - $basePerson : 0,
                    'additional_cost' => $personCount > $basePerson ? ($personCount - $basePerson) * $additionalPersonPrice : 0,
                    'discount' => $package->discount_amount ?? 0,
                    'final_total' => $totalPrice
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Travel package not found',
                'message' => "Travel package with ID {$id} not found"
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to calculate price',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menghitung harga travel package berdasarkan jumlah orang menggunakan slug
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function calculatePriceBySlug(Request $request, string $slug): JsonResponse
    {
        try {
            // Validasi input
            $request->validate([
                'person_count' => 'required|integer|min:1'
            ]);

            // Cari travel package berdasarkan slug
            $package = TravelPackage::where('slug', $slug)->firstOrFail();
            
            // Gunakan calculatePrice method dari model TravelPackage
            $personCount = $request->input('person_count');
            $priceData = $package->calculatePrice($personCount);
            
            // Format response untuk frontend
            return response()->json([
                'success' => true,
                'data' => [
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
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Travel package with slug '{$slug}' not found"
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the price',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
