<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelInclude extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'travel_package_id',
        'name'
    ];

    protected $casts = [
        'travel_package_id' => 'integer',
    ];

    /**
     * Get the travel package that owns this include
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
     * Validate name is not empty
     */
    public function setNameAttribute($value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Include name cannot be empty');
        }
        $this->attributes['name'] = trim($value);
    }
}
