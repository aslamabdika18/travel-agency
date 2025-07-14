<?php

namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TravelPackageResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\TravelPackageResource\Api\Transformers\TravelPackageTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = TravelPackageResource::class;
    public static bool $public = true;


    /**
     * Show TravelPackage - Endpoint publik read-only
     * 
     * Endpoint ini dapat diakses oleh pengguna publik untuk melihat detail
     * dari sebuah TravelPackage berdasarkan ID.
     *
     * @param Request $request
     * @return TravelPackageTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new TravelPackageTransformer($query);
    }
}
