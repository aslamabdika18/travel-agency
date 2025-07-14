<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // For list view, return minimal data
        $isDetailView = $request->route() && str_contains($request->route()->uri(), '{');
        
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'base_person_count' => $this->base_person_count,
            'additional_person_price' => $this->additional_person_price,
            'capacity' => $this->capacity,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Media collections - only thumbnail for list view
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
        ];
        
        // Add detailed data only for detail view
        if ($isDetailView) {
            $data = array_merge($data, [
                // Related data
                'itineraries' => ItineraryResource::collection($this->whenLoaded('itineraries')),
                'reviews' => $this->whenLoaded('reviews', function () {
                    return [
                        'average_rating' => $this->average_rating,
                        'review_count' => $this->review_count,
                        'reviews' => $this->reviews->map(function ($review) {
                            return [
                                'id' => $review->id,
                                'rating' => $review->rating,
                                'review' => $review->review,
                                // Optimized: gunakan relationLoaded untuk menghindari N+1 query
                                'user_name' => $review->relationLoaded('user') 
                                    ? ($review->user->name ?? 'Anonymous')
                                    : 'Anonymous',
                                'created_at' => $review->created_at,
                            ];
                        })
                    ];
                }),
                'includes' => TravelIncludeResource::collection($this->whenLoaded('travelIncludes')),
                'excludes' => TravelExcludeResource::collection($this->whenLoaded('travelExcludes')),
                
                // Full gallery for detail view
                'gallery' => $this->getMedia('gallery')->map(function ($media) {
                    return $media->getUrl();
                }),
                
                // Calculated price for base person count
                'price_calculation' => $this->calculatePrice($this->base_person_count ?? 1),
            ]);
        } else {
            // For list view, add empty arrays to maintain consistency
            $data = array_merge($data, [
                'itineraries' => [],
                'includes' => [],
                'excludes' => [],
                'gallery' => [],
            ]);
        }
        
        return $data;
    }
}