<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'travel_package_id',
        'booking_reference',
        'booking_date',
        'person_count',
        'base_price',
        'additional_price',
        'tax_amount',
        'total_price',
        'status',
        'payment_status',
        'special_requests',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    protected $appends = [
        // 'travel_package', // Komentar sementara untuk menghindari masalah loading
        'price_breakdown',
        'payment_status',
        'formatted_base_price',
        'formatted_additional_price',
        'formatted_tax_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'travel_package_id', 'id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }



    // Helper method to determine if this booking is a taxi booking






    // Calculate and update total price
    public function calculateTotalPrice()
    {
        try {
            $this->total_price = ($this->base_price * $this->person_count) + $this->additional_price + $this->tax_amount;
            return $this->total_price;
        } catch (\Exception $e) {
            // Log error tapi jangan crash
            \Illuminate\Support\Facades\Log::error('Error calculating total price: ' . $e->getMessage(), [
                'booking_id' => $this->id,
                'person_count' => $this->person_count
            ]);
            return 0;
        }
    }

    /**
     * Calculate and set pricing based on travel package and person count
     *
     * @param int $personCount
     * @return void
     */
    public function calculatePricing(int $personCount = null): void
    {
        try {
            // Pastikan relasi travelPackage dimuat
            if (!$this->relationLoaded('travelPackage') && $this->travel_package_id) {
                $this->load('travelPackage');
            }
            
            if (!$this->travelPackage) {
                // Coba ambil langsung dari database
                $travelPackage = \App\Models\TravelPackage::find($this->travel_package_id);
                if ($travelPackage) {
                    $this->setRelation('travelPackage', $travelPackage);
                } else {
                    \Illuminate\Support\Facades\Log::error('Travel package not found for booking', [
                        'booking_id' => $this->id,
                        'travel_package_id' => $this->travel_package_id
                    ]);
                    return;
                }
            }

        $personCount = $personCount ?? $this->person_count ?? 1;
        $priceData = $this->travelPackage->calculatePrice($personCount);

        $this->person_count = $personCount;
        $this->base_price = $priceData['base_price'];
        $this->additional_price = $priceData['additional_price'];
        $this->total_price = $priceData['total_price'];
        } catch (\Exception $e) {
            // Log error tapi jangan crash
            \Illuminate\Support\Facades\Log::error('Error calculating pricing: ' . $e->getMessage(), [
                'booking_id' => $this->id,
                'travel_package_id' => $this->travel_package_id,
                'person_count' => $personCount
            ]);
        }
    }

    /**
     * Get price breakdown for display
     *
     * @return array
     */
    public function getPriceBreakdown(): array
    {
        try {
            return [
                'person_count' => $this->person_count,
                'base_price' => $this->base_price,
                'additional_price' => $this->additional_price,
                'tax_amount' => $this->tax_amount,
                'total_price' => $this->total_price,
                'base_person_count' => $this->travelPackage->base_person_count ?? 0,
                'additional_person_price' => $this->travelPackage->additional_person_price ?? 0,
            ];
        } catch (\Exception $e) {
            // Log error tapi jangan crash
            \Illuminate\Support\Facades\Log::error('Error getting price breakdown: ' . $e->getMessage(), [
                'booking_id' => $this->id
            ]);
            return [
                'person_count' => $this->person_count ?? 0,
                'base_price' => $this->base_price ?? 0,
                'additional_price' => $this->additional_price ?? 0,
                'tax_amount' => $this->tax_amount ?? 0,
                'total_price' => $this->total_price ?? 0,
                'base_person_count' => 0,
                'additional_person_price' => 0,
            ];
        }
    }

    /**
     * Get formatted base price accessor
     *
     * @return string
     */
    public function getFormattedBasePriceAttribute(): string
    {
        return formatRupiah($this->base_price ?? 0);
    }

    /**
     * Get formatted additional price accessor
     *
     * @return string
     */
    public function getFormattedAdditionalPriceAttribute(): string
    {
        return formatRupiah($this->additional_price ?? 0);
    }

    /**
     * Get formatted tax amount accessor
     *
     * @return string
     */
    public function getFormattedTaxAmountAttribute(): string
    {
        return formatRupiah($this->tax_amount ?? 0);
    }

    /**
     * Get formatted price display (legacy method for backward compatibility)
     *
     * @return string
     */
    public function getFormattedTotalPrice(): string
    {
        // Ensure numeric values to prevent "non-numeric value encountered" error
        $totalPrice = is_numeric($this->total_price) ? (float)$this->total_price : 0;
        $basePrice = is_numeric($this->base_price) ? (float)$this->base_price : 0;
        $additionalPrice = is_numeric($this->additional_price) ? (float)$this->additional_price : 0;
        
        if ($additionalPrice > 0) {
            $extraPersons = $this->person_count - ($this->travelPackage->base_person_count ?? 0);
            return sprintf(
                'Rp %s (Base: Rp %s + Additional: Rp %s for %d extra person(s))',
                number_format((float)$totalPrice, 0, ',', '.'),
                number_format((float)$basePrice, 0, ',', '.'),
                number_format((float)$additionalPrice, 0, ',', '.'),
                $extraPersons
            );
        }

        return sprintf('Rp %s', number_format((float)$totalPrice, 0, ',', '.'));
    }

    /**
     * Check if booking has payment
     *
     * @return bool
     */
    public function hasPayment(): bool
    {
        return $this->payment()->exists();
    }

    /**
     * Check if booking is paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment && $this->payment->isPaid();
    }

    /**
     * Check if booking payment is pending
     *
     * @return bool
     */
    public function isPaymentPending(): bool
    {
        return $this->payment && $this->payment->isPending();
    }

    /**
     * Check if booking can be paid
     *
     * @return bool
     */
    public function canBePaid(): bool
    {
        return in_array($this->status, ['Pending', 'Confirmed']) && !$this->isPaid();
    }

    /**
     * Get payment status for display
     *
     * @return string
     */
    public function getPaymentStatus(): string
    {
        if (!$this->hasPayment()) {
            return 'Not Created';
        }

        return $this->payment->payment_status;
    }

    /**
     * Create payment for this booking
     *
     * @param array $paymentData
     * @return Payment
     */
    public function createPayment(array $paymentData = []): Payment
    {
        $defaultData = [
            'total_price' => $this->total_price,
            'payment_status' => 'Unpaid'
        ];

        $paymentData = array_merge($defaultData, $paymentData);

        return $this->payment()->create($paymentData);
    }

    /**
     * Update booking status based on payment
     *
     * @param string $paymentStatus
     * @return void
     */
    public function updateStatusFromPayment(string $paymentStatus): void
    {
        switch ($paymentStatus) {
            case 'Paid':
                $this->update(['status' => 'Confirmed']);
                break;
            case 'Failed':
                $this->update(['status' => 'Cancelled']);
                break;
            // 'Unpaid' doesn't change booking status
        }
    }

    /**
     * Scope for bookings that can be paid
     */
    public function scopeCanBePaid($query)
    {
        return $query->whereIn('status', ['Pending', 'Confirmed'])
                    ->whereDoesntHave('payment', function ($q) {
                        $q->where('payment_status', 'Paid');
                    });
    }

    /**
     * Scope for paid bookings
     */
    public function scopePaid($query)
    {
        return $query->whereHas('payment', function ($q) {
            $q->where('payment_status', 'Paid');
        });
    }

    /**
     * Accessor for travel_package (snake_case for frontend compatibility)
     */
    public function getTravelPackageAttribute()
    {
        try {
            // Jika relasi sudah dimuat, gunakan itu
            if ($this->relationLoaded('travelPackage')) {
                $travelPackage = $this->getRelation('travelPackage');
                if ($travelPackage !== null) {
                    return $travelPackage;
                }
            }
            
            // Jika belum dimuat atau null, coba ambil langsung dari database
            if ($this->travel_package_id) {
                // Simpan hasil query ke relasi agar tidak perlu query lagi
                $travelPackage = \App\Models\TravelPackage::find($this->travel_package_id);
                if ($travelPackage) {
                    $this->setRelation('travelPackage', $travelPackage);
                    return $travelPackage;
                }
            }
            
            // Jika masih null, kembalikan null
            return null;
        } catch (\Exception $e) {
            // Log error tapi jangan crash
            \Illuminate\Support\Facades\Log::error('Error getting travel package: ' . $e->getMessage(), [
                'booking_id' => $this->id,
                'travel_package_id' => $this->travel_package_id
            ]);
            return null;
        }
    }

    /**
     * Accessor for price_breakdown
     */
    public function getPriceBreakdownAttribute()
    {
        return $this->getPriceBreakdown();
    }

    /**
     * Accessor for payment_status
     */
    public function getPaymentStatusAttribute()
    {
        return $this->getPaymentStatus();
    }

    /**
     * Accessor for formatted_total_price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return $this->getFormattedTotalPrice();
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($booking) {
            // Auto-calculate pricing if travel_package_id and person_count are set
            if ($booking->travel_package_id && $booking->person_count) {
                $booking->calculatePricing($booking->person_count);
            }
        });
    }
}
