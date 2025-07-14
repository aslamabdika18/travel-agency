<?php
namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TravelPackageResource;
use App\Filament\Resources\TravelPackageResource\Api\Requests\UpdateTravelPackageRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = TravelPackageResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update TravelPackage
     *
     * @param UpdateTravelPackageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateTravelPackageRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}