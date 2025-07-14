<?php
namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TravelPackageResource;
use App\Filament\Resources\TravelPackageResource\Api\Requests\CreateTravelPackageRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = TravelPackageResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create TravelPackage - Endpoint private (tidak dapat diakses publik)
     * 
     * Endpoint ini hanya dapat diakses oleh pengguna yang terautentikasi
     * dan memiliki izin yang sesuai. Endpoint ini digunakan untuk membuat
     * TravelPackage baru.
     *
     * @param CreateTravelPackageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateTravelPackageRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}