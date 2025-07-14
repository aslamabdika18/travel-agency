<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class RefundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return false;
        }

        // If booking_id is provided, check if user owns the booking
        if ($this->has('booking_id')) {
            $booking = Booking::find($this->booking_id);
            return $booking && $booking->user_id === Auth::id();
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => [
                'required',
                'integer',
                'exists:bookings,id',
                function ($attribute, $value, $fail) {
                    $booking = Booking::find($value);
                    
                    if (!$booking) {
                        $fail('Booking tidak ditemukan.');
                        return;
                    }

                    // Check if booking belongs to authenticated user
                    if ($booking->user_id !== Auth::id()) {
                        $fail('Anda tidak memiliki akses ke booking ini.');
                        return;
                    }

                    // Check if booking can be refunded
                    if (!$booking->canBeRefunded()) {
                        $refundDetails = $booking->getRefundPolicyDetails();
                        $fail($refundDetails['message']);
                        return;
                    }

                    // Check if booking is already refunded
                    if ($booking->status === 'refunded') {
                        $fail('Booking ini sudah di-refund sebelumnya.');
                        return;
                    }

                    // Check if booking is cancelled
                    if ($booking->status === 'cancelled') {
                        $fail('Booking yang sudah dibatalkan tidak dapat di-refund.');
                        return;
                    }

                    // Check payment status
                    if (!$booking->payment || !$booking->payment->isPaid()) {
                        $fail('Hanya booking dengan pembayaran yang sudah lunas yang dapat di-refund.');
                        return;
                    }
                },
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
                'min:10'
            ],
            'confirm' => [
                'required',
                'boolean',
                'accepted'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'booking_id.required' => 'ID booking harus diisi.',
            'booking_id.integer' => 'ID booking harus berupa angka.',
            'booking_id.exists' => 'Booking tidak ditemukan.',
            'reason.string' => 'Alasan refund harus berupa teks.',
            'reason.max' => 'Alasan refund maksimal 500 karakter.',
            'reason.min' => 'Alasan refund minimal 10 karakter.',
            'confirm.required' => 'Konfirmasi refund harus diisi.',
            'confirm.boolean' => 'Konfirmasi refund harus berupa true/false.',
            'confirm.accepted' => 'Anda harus mengkonfirmasi untuk melanjutkan refund.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'booking_id' => 'ID Booking',
            'reason' => 'Alasan Refund',
            'confirm' => 'Konfirmasi'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success' => false,
            'message' => 'Data yang dikirim tidak valid.',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Anda tidak memiliki akses untuk melakukan refund pada booking ini.'
        );
    }
}