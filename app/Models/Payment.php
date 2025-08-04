<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\User;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_reference',
        'total_price',
        'payment_status',
        'transaction_id',
        'payment_date',
        'gateway_transaction_id',
        'gateway_status',
        'gateway_response'
    ];

    /**
     * Kolom yang digunakan untuk pencarian
     */
    protected $searchable = [
        'payment_reference',
        'transaction_id',
        'gateway_transaction_id'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'total_price' => 'integer',
        'gateway_response' => 'array'
    ];

    /**
     * Relationship with Booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that owns the payment through booking.
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Booking::class,
            'id', // Foreign key on bookings table
            'id', // Foreign key on users table
            'booking_id', // Local key on payments table
            'user_id' // Local key on bookings table
        );
    }

    /**
     * Update gateway status
     */
    public function updateGatewayStatus($status)
    {
        $this->gateway_status = $status;
        $this->save();
    }

    /**
     * Scope untuk mencari berdasarkan payment_reference
     */
    public function scopeByPaymentReference($query, $reference)
    {
        return $query->where('payment_reference', $reference);
    }

    /**
     * Scope untuk mencari berdasarkan transaction_id
     */
    public function scopeByTransactionId($query, $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    /**
     * Scope untuk mencari berdasarkan gateway_transaction_id
     */
    public function scopeByGatewayTransactionId($query, $gatewayTransactionId)
    {
        return $query->where('gateway_transaction_id', $gatewayTransactionId);
    }

    /**
     * Menandai pembayaran sebagai sukses
     */
    public function markAsPaid()
    {
        $this->payment_status = 'Paid';
        $this->payment_date = now();
        $this->save();

        // Update booking status jika ada
        if ($this->booking) {
            $this->booking->status = 'Confirmed';
            $this->booking->save();
        }

        return $this;
    }

    /**
     * Menandai pembayaran sebagai gagal
     */
    public function markAsFailed()
    {
        $this->payment_status = 'Failed';
        $this->save();

        return $this;
    }

    /**
     * Mark payment as refunded
     */
    public function markAsRefunded(): void
    {
        $this->update([
            'payment_status' => 'Refunded',
            'payment_date' => $this->payment_date ?? now() // Keep original payment date if exists
        ]);
        
        // Update booking status
        $this->booking->update([
            'status' => 'refunded',
            'payment_status' => 'refunded'
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        // Ensure numeric value to prevent "non-numeric value encountered" error
        $totalPrice = is_numeric($this->total_price) ? (float)$this->total_price : 0;
        return 'Rp ' . number_format((float)$totalPrice, 0, ',', '.');
    }

    /**
     * Check if payment is paid
     */
    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['Paid', 'completed']);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return in_array($this->payment_status, ['Unpaid', 'pending']);
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return in_array($this->payment_status, ['Failed', 'failed']);
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->payment_status === 'Refunded';
    }



    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->whereIn('payment_status', ['Paid', 'completed']);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->whereIn('payment_status', ['Unpaid', 'pending']);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('payment_status', ['Failed', 'failed']);
    }

    /**
     * Scope untuk filter payment yang refunded
     */
    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'Refunded');
    }
}
