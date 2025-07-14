<?php
namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\TravelPackageResource;
use App\Filament\Resources\TravelPackageResource\Api\Transformers\TravelPackageTransformer;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = TravelPackageResource::class;
    public static bool $public = true;


    /**
     * List of TravelPackage - Endpoint publik read-only
     * 
     * Endpoint ini dapat diakses oleh pengguna publik untuk melihat daftar TravelPackage
     * dengan dukungan pagination, filtering, dan sorting.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return TravelPackageTransformer::collection($query);
    }
}
