<?php

namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use App\Filament\Resources\TravelPackageResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\TravelPackageResource\Api\Transformers\TravelPackageTransformer;

class SlugHandler extends Handlers
{
    public static string | null $uri = '/slug/{slug}';
    public static string | null $resource = TravelPackageResource::class;
    public static bool $public = true;

    /**
     * Show TravelPackage by slug - Endpoint publik read-only
     * 
     * Endpoint ini dapat diakses oleh pengguna publik untuk melihat detail
     * dari sebuah TravelPackage berdasarkan slug-nya. Endpoint ini sangat berguna
     * untuk URL yang SEO-friendly di frontend.
     *
     * @param Request $request
     * @return TravelPackageTransformer
     */
    public function handler(Request $request)
    {
        $slug = $request->route('slug');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where('slug', $slug)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new TravelPackageTransformer($query);
    }
}
