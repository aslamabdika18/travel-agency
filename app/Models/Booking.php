<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RefundProcessedNotification;

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
     * Check if booking can be refunded based on departure date
     *
     * @return bool
     */
    public function canBeRefunded(): bool
    {
        // Check if refund system is globally enabled
        if (!config('app.refund_enabled', env('REFUND_ENABLED', true))) {
            return false;
        }

        // Only paid bookings can be refunded
        if (!$this->isPaid() || $this->status === 'cancelled' || $this->status === 'refunded') {
            return false;
        }

        // Check if travel package has departure date (booking_date)
        if (!$this->booking_date) {
            return false;
        }

        // Calculate days until departure
        $daysUntilDeparture = now()->diffInDays($this->booking_date, false);
        
        // Can refund if departure is in the future
        return $daysUntilDeparture > 0;
    }

    /**
     * Get refund percentage based on cancellation policy v2
     * 30+ days: 100%, 15-29 days: 50%, 7-14 days: 25%, <7 days: 0%
     *
     * @return int
     */
    public function getRefundPercentage(): int
    {
        if (!$this->canBeRefunded()) {
            return 0;
        }

        $daysUntilDeparture = now()->diffInDays($this->booking_date, false);

        if ($daysUntilDeparture >= 30) {
            return 100; // Full refund
        } elseif ($daysUntilDeparture >= 15) {
            return 50;  // 50% refund
        } elseif ($daysUntilDeparture >= 7) {
            return 25;  // 25% refund
        } else {
            return 0;   // No refund
        }
    }

    /**
     * Calculate refund amount based on current policy
     *
     * @return float
     */
    public function calculateRefundAmount(): float
    {
        $refundPercentage = $this->getRefundPercentage();
        
        if ($refundPercentage === 0) {
            return 0;
        }

        return ($this->total_price * $refundPercentage) / 100;
    }

    /**
     * Get days until departure
     *
     * @return int
     */
    public function getDaysUntilDeparture(): int
    {
        if (!$this->booking_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->booking_date, false));
    }

    /**
     * Get refund policy details for this booking
     *
     * @return array
     */
    public function getRefundPolicyDetails(): array
    {
        $daysUntilDeparture = $this->getDaysUntilDeparture();
        $refundPercentage = $this->getRefundPercentage();
        $refundAmount = $this->calculateRefundAmount();

        return [
            'can_be_refunded' => $this->canBeRefunded(),
            'days_until_departure' => $daysUntilDeparture,
            'refund_percentage' => $refundPercentage,
            'refund_amount' => $refundAmount,
            'formatted_refund_amount' => formatRupiah($refundAmount),
            'policy_tier' => $this->getRefundPolicyTier($daysUntilDeparture),
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'total_price' => $this->total_price,
        ];
    }

    /**
     * Get refund policy tier description
     *
     * @param int $daysUntilDeparture
     * @return string
     */
    private function getRefundPolicyTier(int $daysUntilDeparture): string
    {
        if ($daysUntilDeparture >= 30) {
            return '30+ days before departure';
        } elseif ($daysUntilDeparture >= 15) {
            return '15-29 days before departure';
        } elseif ($daysUntilDeparture >= 7) {
            return '7-14 days before departure';
        } else {
            return 'Less than 7 days before departure';
        }
    }

    /**
     * Process refund for this booking
     *
     * @param string|null $reason
     * @return array
     */
    public function processRefund(?string $reason = null): array
    {
        if (!$this->canBeRefunded()) {
            return [
                'success' => false,
                'message' => 'This booking cannot be refunded',
                'details' => $this->getRefundPolicyDetails()
            ];
        }

        $refundAmount = $this->calculateRefundAmount();
        
        if ($refundAmount <= 0) {
            return [
                'success' => false,
                'message' => 'No refund amount available for this booking',
                'details' => $this->getRefundPolicyDetails()
            ];
        }

        try {
            // Update booking status
            $this->update([
                'status' => 'refunded',
                'payment_status' => 'refunded'
            ]);

            // Mark payment as refunded if exists
            if ($this->payment) {
                $this->payment->markAsRefunded();
            }

            // Send notification to user
            if ($this->user) {
                $this->user->notify(new RefundProcessedNotification(
                    $this,
                    $refundAmount,
                    $this->getRefundPercentage(),
                    $reason
                ));
            }

            // Log the refund
            \Illuminate\Support\Facades\Log::info('Booking refund processed', [
                'booking_id' => $this->id,
                'booking_reference' => $this->booking_reference,
                'refund_amount' => $refundAmount,
                'refund_percentage' => $this->getRefundPercentage(),
                'reason' => $reason,
                'processed_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_amount' => $refundAmount,
                'formatted_refund_amount' => formatRupiah($refundAmount),
                'details' => $this->getRefundPolicyDetails()
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to process refund', [
                'booking_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage(),
                'details' => $this->getRefundPolicyDetails()
            ];
        }
    }

    /**
     * Scope for bookings eligible for refund
     */
    public function scopeEligibleForRefund($query)
    {
        return $query->whereHas('payment', function ($q) {
                $q->where('payment_status', 'Paid');
            })
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->where('booking_date', '>', now());
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
