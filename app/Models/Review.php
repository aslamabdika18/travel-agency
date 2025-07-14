<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'travel_package_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'user_id' => 'integer',
        'travel_package_id' => 'integer',
    ];

    /**
     * Get the user who wrote the review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the travel package being reviewed
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }

    /**
     * Validate rating is within acceptable range
     */
    public function setRatingAttribute($value)
    {
        if ($value < 1 || $value > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }
        $this->attributes['rating'] = $value;
    }

    /**
     * Get formatted rating with stars
     */
    public function getFormattedRatingAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Scope for filtering by rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for high ratings (4-5 stars)
     */
    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }
}
