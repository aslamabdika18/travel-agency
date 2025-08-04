<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TravelPackage extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'base_person_count',
        'additional_person_price',
        'capacity',
        'duration',
        'tax_percentage',
        'category_id',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'additional_person_price' => 'decimal:2',
        'base_person_count' => 'integer',
        'capacity' => 'integer',
        'tax_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'base_person_count' => 1,
        'additional_person_price' => 0,
    ];

    /**
     * Automatically generate slug when name is set
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => [
                'name' => $value,
                'slug' => $this->generateUniqueSlug($value),
            ],
        );
    }

    /**
     * Generate unique slug for travel package
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->useDisk('public')
            ->acceptsFile(function (\Spatie\MediaLibrary\MediaCollections\File $file) {
                return $file->mimeType === 'image/jpeg' || $file->mimeType === 'image/png';
            });

        $this->addMediaCollection('thumbnail')
            ->useDisk('public')
            ->singleFile()
            ->acceptsFile(function (\Spatie\MediaLibrary\MediaCollections\File $file) {
                return $file->mimeType === 'image/jpeg' || $file->mimeType === 'image/png';
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300);

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600);
    }



    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the category that owns the travel package.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get reviews for this travel package
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get average rating from reviews
     * Optimized to avoid N+1 query by using loaded relationship
     */
    public function getAverageRatingAttribute(): float
    {
        // If reviews are already loaded, use the collection
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->avg('rating') ?? 0;
        }

        // Fallback to query if not loaded
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total review count
     * Optimized to avoid N+1 query by using loaded relationship
     */
    public function getReviewCountAttribute(): int
    {
        // If reviews are already loaded, use the collection
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }

        // Fallback to query if not loaded
        return $this->reviews()->count();
    }
    /**
     * Get itineraries for this travel package
     */
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderBy('day');
    }

    /**
     * Get travel includes for this package
     */
    public function travelIncludes()
    {
        return $this->hasMany(TravelInclude::class);
    }

    /**
     * Get travel excludes for this package
     */
    public function travelExcludes()
    {
        return $this->hasMany(TravelExclude::class);
    }

    /**
     * Get includes as array of names
     * Optimized to avoid N+1 query
     */
    public function getIncludesListAttribute(): array
    {
        // If travelIncludes are already loaded, use the collection
        if ($this->relationLoaded('travelIncludes')) {
            return $this->travelIncludes->pluck('name')->toArray();
        }

        // Fallback to query if not loaded
        return $this->travelIncludes()->pluck('name')->toArray();
    }

    /**
     * Get excludes as array of names
     * Optimized to avoid N+1 query
     */
    public function getExcludesListAttribute(): array
    {
        // If travelExcludes are already loaded, use the collection
        if ($this->relationLoaded('travelExcludes')) {
            return $this->travelExcludes->pluck('name')->toArray();
        }

        // Fallback to query if not loaded
        return $this->travelExcludes()->pluck('name')->toArray();
    }

    /**
     * Get itinerary grouped by day
     * Optimized to avoid N+1 query
     */
    public function getItineraryByDayAttribute(): array
    {
        // If itineraries are already loaded, use the collection
        if ($this->relationLoaded('itineraries')) {
            return $this->itineraries->groupBy('day')->map(function ($dayItineraries) {
                return $dayItineraries->map(function ($itinerary) {
                    return [
                        'activity' => $itinerary->activity,
                        'note' => $itinerary->note,
                    ];
                });
            })->toArray();
        }

        // Fallback to query if not loaded
        return $this->itineraries()->get()->groupBy('day')->map(function ($dayItineraries) {
            return $dayItineraries->map(function ($itinerary) {
                return [
                    'activity' => $itinerary->activity,
                    'note' => $itinerary->note,
                ];
            });
        })->toArray();
    }

    /**
     * Calculate price for a specific number of people
     */
    public function calculatePrice(int $personCount): array
    {
        $this->validatePersonCount($personCount);

        $basePrice = $this->price;
        $basePersonCount = $this->base_person_count;
        $additionalPersonPrice = $this->additional_person_price;

        $additionalPrice = $this->calculateAdditionalPrice($personCount, $basePersonCount, $additionalPersonPrice);
        $subtotal = $basePrice + $additionalPrice;
        $taxAmount = $this->calculateTax($subtotal);
        $totalPrice = $subtotal + $taxAmount;

        return [
            'base_price' => $basePrice,
            'additional_price' => $additionalPrice,
            'tax_amount' => $taxAmount,
            'total_price' => $totalPrice,
            'person_count' => $personCount,
            'base_person_count' => $basePersonCount,
            'additional_person_price' => $additionalPersonPrice,
            'tax_percentage' => $this->tax_percentage ?? 10.00,
            'extra_persons' => max(0, $personCount - $basePersonCount)
        ];
    }

    /**
     * Validate person count input
     */
    private function validatePersonCount(int $personCount): void
    {
        if ($personCount <= 0) {
            throw new \InvalidArgumentException('Person count must be greater than 0');
        }

        if ($this->capacity && $personCount > $this->capacity) {
            throw new \InvalidArgumentException("Person count ({$personCount}) exceeds package capacity ({$this->capacity})");
        }
    }

    /**
     * Calculate additional price for extra persons
     */
    private function calculateAdditionalPrice(int $personCount, int $basePersonCount, float $additionalPersonPrice): float
    {
        if ($personCount <= $basePersonCount) {
            return 0;
        }

        $extraPersons = $personCount - $basePersonCount;
        return $extraPersons * $additionalPersonPrice;
    }

    /**
     * Calculate tax amount based on subtotal
     */
    private function calculateTax(float $subtotal): float
    {
        $taxPercentage = $this->tax_percentage ?? 10.00;
        return ($subtotal * $taxPercentage) / 100;
    }

    /**
     * Check if package is available for booking
     */
    public function isAvailable(): bool
    {
        return !$this->trashed() && $this->price > 0;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        // Ensure numeric value to prevent "non-numeric value encountered" error
        $price = is_numeric($this->price) ? (float)$this->price : 0;
        return 'Rp ' . number_format((float)$price, 0, ',', '.');
    }

    /**
     * Get thumbnail image URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        // Try to get the original image first, fallback to thumb conversion
        $url = $this->getFirstMediaUrl('thumbnail');
        if (!$url) {
            $url = $this->getFirstMediaUrl('thumbnail', 'thumb');
        }
        
        // If no media found, use placeholder image
        if (!$url) {
            // Try SVG first, fallback to JPG
            if (file_exists(public_path('images/placeholder-travel.svg'))) {
                $url = asset('images/placeholder-travel.svg');
            } else {
                $url = asset('images/placeholder-travel.jpg');
            }
        }
        
        return $url;
    }

    /**
     * Get featured image URL (uses thumbnail)
     */
    public function getFeaturedImageAttribute(): ?string
    {
        return $this->thumbnailUrl;
    }

    /**
     * Get gallery images
     */
    public function getGalleryImagesAttribute(): array
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
                'medium' => $media->getUrl('medium'),
            ];
        })->toArray();
    }

    /**
     * Get images attribute (accessor)
     * Returns all images from both gallery and thumbnail collections
     */
    public function getImagesAttribute()
    {
        $galleryImages = $this->getMedia('gallery');
        $thumbnailImages = $this->getMedia('thumbnail');
        
        return $galleryImages->merge($thumbnailImages);
    }

    /**
     * Get all images as a collection (compatibility method)
     * Use this instead of relationship when you need all images
     */
    public function getAllImages()
    {
        return $this->images;
    }

    /**
     * Get gallery images only
     */
    public function getGalleryImages()
    {
        return $this->getMedia('gallery');
    }

    /**
     * Get thumbnail images only
     */
    public function getThumbnailImages()
    {
        return $this->getMedia('thumbnail');
    }
}
