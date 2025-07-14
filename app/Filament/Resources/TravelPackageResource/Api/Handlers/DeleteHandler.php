<?php
namespace App\Filament\Resources\TravelPackageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TravelPackageResource;

class DeleteHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = TravelPackageResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Delete TravelPackage - Endpoint private (tidak dapat diakses publik)
     * 
     * Endpoint ini hanya dapat diakses oleh pengguna yang terautentikasi
     * dan memiliki izin yang sesuai. Endpoint ini digunakan untuk menghapus
     * TravelPackage yang ada.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->delete();

        return static::sendSuccessResponse($model, "Successfully Delete Resource");
    }
}