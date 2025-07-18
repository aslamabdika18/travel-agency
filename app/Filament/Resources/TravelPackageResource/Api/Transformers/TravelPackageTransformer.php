<?php
namespace App\Filament\Resources\TravelPackageResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\TravelPackage;

/**
 * TravelPackageTransformer - Transformer untuk model TravelPackage
 * 
 * Transformer ini mengubah model TravelPackage menjadi format JSON yang sesuai
 * untuk API. Transformer ini menambahkan URL untuk media (thumbnail dan gallery),
 * serta menyusun data terkait seperti itinerary, inclusions, dan exclusions.
 * 
 * @property TravelPackage $resource
 */
class TravelPackageTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     * 
     * Metode ini mengubah model TravelPackage menjadi array yang berisi:
     * - Semua atribut dasar dari model TravelPackage
     * - URL thumbnail dalam berbagai ukuran (original, thumb, medium)
     * - Galeri gambar dengan URL dalam berbagai ukuran
     * - Data itinerary yang terstruktur (day, activity, note)
     * - Daftar inclusions (yang termasuk dalam paket)
     * - Daftar exclusions (yang tidak termasuk dalam paket)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();

        // Add thumbnail URL if available
        $thumbnailMedia = $this->resource->getFirstMedia('thumbnail');
        $data['thumbnail_url'] = $thumbnailMedia ? $thumbnailMedia->getUrl() : null;

        // Check if thumb and medium conversions are available
        $data['thumbnail_thumb_url'] = ($thumbnailMedia && $thumbnailMedia->hasGeneratedConversion('thumb'))
            ? $thumbnailMedia->getUrl('thumb')
            : ($thumbnailMedia ? $thumbnailMedia->getUrl() : null);

        $data['thumbnail_medium_url'] = ($thumbnailMedia && $thumbnailMedia->hasGeneratedConversion('medium'))
            ? $thumbnailMedia->getUrl('medium')
            : ($thumbnailMedia ? $thumbnailMedia->getUrl() : null);

        // Add gallery URLs if available
        $galleryMedia = $this->resource->getMedia('gallery');
        $data['gallery'] = [];

        foreach ($galleryMedia as $media) {
            $data['gallery'][] = [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb_url' => ($media->hasGeneratedConversion('thumb'))
                    ? $media->getUrl('thumb')
                    : $media->getUrl(),
                'medium_url' => ($media->hasGeneratedConversion('medium'))
                    ? $media->getUrl('medium')
                    : $media->getUrl(),
            ];
        }

        // Add itinerary data
        $data['itinerary'] = $this->resource->itineraries ? $this->resource->itineraries->map(function ($item) {
            return [
                'day' => $item->day,
                'activity' => $item->activity,
                'note' => $item->note
            ];
        })->toArray() : [];

        // Add inclusions data
        $data['inclusions'] = $this->resource->travelIncludes ? $this->resource->travelIncludes->pluck('name')->toArray() : [];

        // Add exclusions data
        $data['exclusions'] = $this->resource->travelExcludes ? $this->resource->travelExcludes->pluck('name')->toArray() : [];

        return $data;
    }
}
