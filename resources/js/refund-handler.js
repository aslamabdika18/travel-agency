/**
 * Refund Handler JavaScript
 * Handles refund request functionality
 */

class RefundHandler {
    constructor() {
        this.selectedBooking = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadEligibleBookings();
        this.loadRefundHistory();
    }

    bindEvents() {
        // Search booking
        document.getElementById('search-booking-btn')?.addEventListener('click', () => {
            this.searchBooking();
        });

        // Enter key for search
        document.getElementById('booking-search')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.searchBooking();
            }
        });

        // Form submission
        document.getElementById('refund-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitRefund();
        });

        // Cancel button
        document.getElementById('cancel-refund-btn')?.addEventListener('click', () => {
            this.cancelRefund();
        });

        // Modal close buttons
        document.getElementById('close-success-modal')?.addEventListener('click', () => {
            this.hideModal('success-modal');
            this.refreshPage();
        });

        document.getElementById('close-error-modal')?.addEventListener('click', () => {
            this.hideModal('error-modal');
        });

        // Reason textarea validation
        document.getElementById('refund-reason')?.addEventListener('input', (e) => {
            this.validateReason(e.target);
        });
    }

    async loadEligibleBookings() {
        try {
            const response = await fetch('/api/refund/eligible-bookings', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                this.renderEligibleBookings(data.data.data);
            } else {
                this.showError('Failed to load eligible bookings');
            }
        } catch (error) {
            console.error('Error loading eligible bookings:', error);
            this.showError('Failed to load eligible bookings');
        }
    }

    async searchBooking() {
        const searchInput = document.getElementById('booking-search');
        const bookingReference = searchInput.value.trim();

        if (!bookingReference) {
            this.showError('Please enter a booking reference');
            return;
        }

        try {
            // Search in eligible bookings first
            const response = await fetch('/api/refund/eligible-bookings', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                const booking = data.data.data.find(b => 
                    b.booking_reference.toLowerCase() === bookingReference.toLowerCase()
                );

                if (booking) {
                    this.selectBooking(booking);
                } else {
                    this.showError('Booking not found or not eligible for refund');
                }
            }
        } catch (error) {
            console.error('Error searching booking:', error);
            this.showError('Failed to search booking');
        }
    }

    renderEligibleBookings(bookings) {
        const container = document.getElementById('eligible-bookings');
        
        if (!bookings || bookings.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <p>No bookings eligible for refund found.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = bookings.map(booking => `
            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary transition-colors cursor-pointer booking-item" 
                 data-booking='${JSON.stringify(booking)}'>
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${booking.booking_reference}</h3>
                        <p class="text-sm text-gray-600 mt-1">${booking.travel_package?.name || 'N/A'}</p>
                        <p class="text-sm text-gray-500 mt-1">Booking Date: ${booking.booking_date}</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">${booking.formatted_total_price}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            booking.refund_policy.can_refund ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }">
                            ${booking.refund_policy.can_refund ? `${booking.refund_policy.refund_percentage}% refund` : 'No refund'}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">${booking.refund_policy.days_until_departure} days left</p>
                    </div>
                </div>
            </div>
        `).join('');

        // Add click events to booking items
        container.querySelectorAll('.booking-item').forEach(item => {
            item.addEventListener('click', () => {
                const booking = JSON.parse(item.dataset.booking);
                this.selectBooking(booking);
            });
        });
    }

    async selectBooking(booking) {
        try {
            // Get detailed refund policy
            const response = await fetch(`/api/refund/policy?booking_id=${booking.id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                this.selectedBooking = data.data;
                this.showRefundForm();
            } else {
                this.showError(data.message || 'Failed to get refund policy');
            }
        } catch (error) {
            console.error('Error getting refund policy:', error);
            this.showError('Failed to get refund policy');
        }
    }

    showRefundForm() {
        if (!this.selectedBooking) return;

        const container = document.getElementById('refund-form-container');
        const bookingInfo = document.getElementById('selected-booking-info');
        const refundDetails = document.getElementById('refund-details');

        const booking = this.selectedBooking.booking;
        const policy = this.selectedBooking.refund_policy;

        // Show booking info
        bookingInfo.innerHTML = `
            <h3 class="font-semibold text-gray-900 mb-2">Selected Booking</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Booking Reference</p>
                    <p class="font-medium">${booking.booking_reference}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="font-medium">${booking.formatted_total_price}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Booking Date</p>
                    <p class="font-medium">${booking.booking_date}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-medium capitalize">${booking.status}</p>
                </div>
            </div>
        `;

        // Show refund details
        if (policy.can_refund) {
            refundDetails.innerHTML = `
                <h3 class="font-semibold text-blue-900 mb-2">Refund Calculation</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-blue-600">Days Until Departure</p>
                        <p class="font-medium text-blue-900">${policy.days_until_departure} days</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-600">Refund Percentage</p>
                        <p class="font-medium text-blue-900">${policy.refund_percentage}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-600">Refund Amount</p>
                        <p class="font-medium text-blue-900">${policy.formatted_refund_amount}</p>
                    </div>
                </div>
                <p class="text-sm text-blue-600 mt-2">${policy.message}</p>
            `;
        } else {
            refundDetails.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-red-600 font-medium">${policy.message}</p>
                </div>
            `;
        }

        // Show/hide form based on eligibility
        const submitBtn = document.getElementById('submit-refund-btn');
        if (policy.can_refund) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth' });
    }

    async submitRefund() {
        if (!this.selectedBooking) return;

        const form = document.getElementById('refund-form');
        const formData = new FormData(form);
        const reason = formData.get('reason');
        const confirm = formData.get('confirm');

        // Validation
        if (!confirm) {
            this.showError('Please confirm that you want to proceed with the refund');
            return;
        }

        if (reason && reason.length < 10) {
            this.showError('Reason must be at least 10 characters long');
            return;
        }

        this.setLoading(true);

        try {
            const response = await fetch('/api/refund/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    booking_id: this.selectedBooking.booking.id,
                    reason: reason || null,
                    confirm: true
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message || 'Refund processed successfully');
            } else {
                this.showError(data.message || 'Failed to process refund');
            }
        } catch (error) {
            console.error('Error processing refund:', error);
            this.showError('Failed to process refund');
        } finally {
            this.setLoading(false);
        }
    }

    cancelRefund() {
        this.selectedBooking = null;
        document.getElementById('refund-form-container').classList.add('hidden');
        document.getElementById('refund-form').reset();
    }

    async loadRefundHistory() {
        try {
            const response = await fetch('/api/refund/history', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                this.renderRefundHistory(data.data.data);
            }
        } catch (error) {
            console.error('Error loading refund history:', error);
        }
    }

    renderRefundHistory(refunds) {
        const container = document.getElementById('refund-history');
        
        if (!refunds || refunds.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <p>No refund history found.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = refunds.map(refund => `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${refund.booking_reference}</h3>
                        <p class="text-sm text-gray-600 mt-1">${refund.travel_package?.name || 'N/A'}</p>
                        <p class="text-sm text-gray-500 mt-1">Refunded: ${refund.refunded_at}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-900">${refund.formatted_total_price}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                            Refunded
                        </span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    validateReason(textarea) {
        const value = textarea.value;
        const minLength = 10;
        const maxLength = 500;
        
        let message = '';
        let isValid = true;

        if (value.length > 0 && value.length < minLength) {
            message = `Minimum ${minLength} characters required`;
            isValid = false;
        } else if (value.length > maxLength) {
            message = `Maximum ${maxLength} characters allowed`;
            isValid = false;
        } else if (value.length > 0) {
            message = `${value.length}/${maxLength} characters`;
        }

        // Update validation message
        let validationEl = textarea.parentNode.querySelector('.validation-message');
        if (!validationEl) {
            validationEl = document.createElement('p');
            validationEl.className = 'validation-message text-xs mt-1';
            textarea.parentNode.appendChild(validationEl);
        }

        validationEl.textContent = message;
        validationEl.className = `validation-message text-xs mt-1 ${
            isValid ? 'text-gray-500' : 'text-red-500'
        }`;

        return isValid;
    }

    setLoading(loading) {
        const submitBtn = document.getElementById('submit-refund-btn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        if (loading) {
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
        } else {
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    }

    showSuccess(message) {
        document.getElementById('success-message').textContent = message;
        this.showModal('success-modal');
    }

    showError(message) {
        document.getElementById('error-message').textContent = message;
        this.showModal('error-modal');
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    refreshPage() {
        window.location.reload();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new RefundHandler();
});