<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'travel_package_id',
        'day',
        'activity',
        'note'
    ];

    protected $casts = [
        'travel_package_id' => 'integer',
        'day' => 'integer',
    ];

    /**
     * Get the travel package that owns this itinerary
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class);
    }

    /**
     * Scope for specific travel package
     */
    public function scopeForPackage($query, int $packageId)
    {
        return $query->where('travel_package_id', $packageId);
    }

    /**
     * Scope for specific day
     */
    public function scopeForDay($query, int $day)
    {
        return $query->where('day', $day);
    }

    /**
     * Scope ordered by day
     */
    public function scopeOrderedByDay($query)
    {
        return $query->orderBy('day');
    }

    /**
     * Validate day is positive
     */
    public function setDayAttribute($value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Day must be greater than 0');
        }
        $this->attributes['day'] = $value;
    }

    /**
     * Validate activity is not empty
     */
    public function setActivityAttribute($value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Activity cannot be empty');
        }
        $this->attributes['activity'] = trim($value);
    }

    /**
     * Set note attribute (can be empty)
     */
    public function setNoteAttribute($value)
    {
        $this->attributes['note'] = $value ? trim($value) : null;
    }
}
