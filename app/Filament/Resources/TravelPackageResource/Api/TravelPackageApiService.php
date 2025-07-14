<?php

namespace App\Filament\Resources\TravelPackageResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\TravelPackageResource;
use Illuminate\Routing\Router;

/**
 * TravelPackageApiService - Implementasi API untuk TravelPackage
 * 
 * API ini hanya menyediakan akses read-only untuk pengguna publik.
 * Operasi CRUD (Create, Update, Delete) tidak tersedia untuk akses publik.
 */
class TravelPackageApiService extends ApiService
{
    protected static string | null $resource = TravelPackageResource::class;
    
    // Menandai bahwa API ini dapat diakses secara publik
    protected static bool $public = true;

    /**
     * Mendefinisikan handler yang tersedia untuk API TravelPackage
     * 
     * Hanya handler read-only yang diizinkan untuk akses publik:
     * - PaginationHandler: Untuk mendapatkan daftar TravelPackage dengan pagination
     * - DetailHandler: Untuk mendapatkan detail TravelPackage berdasarkan ID
     * - SlugHandler: Untuk mendapatkan detail TravelPackage berdasarkan slug
     * 
     * Handler untuk operasi CRUD (Create, Update, Delete) tidak disertakan
     * untuk mencegah akses publik ke operasi tersebut.
     * 
     * @return array
     */
    public static function handlers() : array
    {
        return [
            // Hanya mengizinkan operasi read-only untuk pengguna publik
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\SlugHandler::class,
            Handlers\CalculatePriceHandler::class
        ];
    }
}