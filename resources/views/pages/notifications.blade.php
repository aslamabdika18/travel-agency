@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-gray-600 mt-2">Lihat semua notifikasi pembayaran dan aktivitas akun Anda</p>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="filterNotifications('all')" id="tab-all" class="tab-button active py-2 px-1 border-b-2 border-primary font-medium text-sm text-primary">
                        Semua
                    </button>
                    <button onclick="filterNotifications('success')" id="tab-success" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Berhasil
                    </button>
                    <button onclick="filterNotifications('failed')" id="tab-failed" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Gagal
                    </button>
                    <button onclick="filterNotifications('pending')" id="tab-pending" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Pending
                    </button>
                    <button onclick="filterNotifications('refund')" id="tab-refund" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Refund
                    </button>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $type = '';
                    $bgColor = '';
                    $borderColor = '';
                    $iconColor = '';
                    $icon = '';

                    if (str_contains($notification->type, 'PaymentSuccessNotification')) {
                        $type = 'success';
                        $bgColor = 'bg-green-50';
                        $borderColor = 'border-green-200';
                        $iconColor = 'text-green-600';
                        $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                    } elseif (str_contains($notification->type, 'PaymentFailedNotification')) {
                        $type = 'failed';
                        $bgColor = 'bg-red-50';
                        $borderColor = 'border-red-200';
                        $iconColor = 'text-red-600';
                        $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
                    } elseif (str_contains($notification->type, 'PaymentPendingNotification')) {
                        $type = 'pending';
                        $bgColor = 'bg-yellow-50';
                        $borderColor = 'border-yellow-200';
                        $iconColor = 'text-yellow-600';
                        $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>';
                    } elseif (str_contains($notification->type, 'RefundProcessedNotification')) {
                        $type = 'refund';
                        $bgColor = 'bg-blue-50';
                        $borderColor = 'border-blue-200';
                        $iconColor = 'text-blue-600';
                        $icon = '<path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>';
                    }
                @endphp

                <div class="notification-item {{ $type }} {{ $bgColor }} border {{ $borderColor }} rounded-lg p-4" data-type="{{ $type }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                {!! $icon !!}
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium text-gray-900">
                                    @if($type === 'success')
                                        Pembayaran Berhasil
                                    @elseif($type === 'failed')
                                        Pembayaran Gagal
                                    @elseif($type === 'pending')
                                        Pembayaran Pending
                                    @elseif($type === 'refund')
                                        Refund Diproses
                                    @endif
                                </h3>
                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="mt-1 text-sm text-gray-700">{{ $data['message'] ?? 'Tidak ada pesan' }}</p>

                            @if(isset($data['booking_reference']))
                <p class="mt-1 text-xs text-gray-600"><strong>Booking:</strong> {{ $data['booking_reference'] }}</p>
            @endif

            @if(isset($data['travel_package_name']))
                <p class="mt-1 text-xs text-gray-600"><strong>Paket:</strong> {{ $data['travel_package_name'] }}</p>
            @endif

            @if(isset($data['formatted_amount']))
                <p class="mt-1 text-xs text-gray-600"><strong>Total:</strong> {{ $data['formatted_amount'] }}</p>
            @endif

                            @if(isset($data['failure_reason']) && $type === 'failed')
                                <p class="mt-1 text-xs text-red-600"><strong>Alasan:</strong> {{ $data['failure_reason'] }}</p>
                            @endif

                            @if(isset($data['expiry_time']) && $type === 'pending')
                <p class="mt-1 text-xs text-yellow-600"><strong>Kadaluarsa:</strong> {{ $data['expiry_time'] }}</p>
            @endif

                            <!-- Action Buttons -->
            @if($type === 'failed' && isset($data['booking_id']))
                <div class="mt-3">
                    <a href="{{ route('user-bookings') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                        Lihat Booking
                    </a>
                </div>
            @elseif($type === 'success' && isset($data['booking_id']))
                <div class="mt-3">
                    <a href="{{ route('user-bookings') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                        Lihat Detail
                    </a>
                </div>
            @elseif($type === 'pending' && isset($data['booking_id']))
                <div class="mt-3">
                    <a href="{{ route('user-bookings') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700">
                        Cek Status
                    </a>
                </div>
            @endif
                        </div>

                        <!-- Mark as Read Button -->
                        @if($notification->read_at === null)
                            <button onclick="markAsRead('{{ $notification->id }}')" class="ml-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5zm6 10V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h6a2 2 0 002-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                    <p class="mt-1 text-sm text-gray-500">Anda belum memiliki notifikasi apapun.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function filterNotifications(type) {
    // Update active tab
    document.querySelectorAll('.tab-button').forEach(tab => {
        tab.classList.remove('active', 'border-primary', 'text-primary');
        tab.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById(`tab-${type}`).classList.add('active', 'border-primary', 'text-primary');
    document.getElementById(`tab-${type}`).classList.remove('border-transparent', 'text-gray-500');

    // Filter notifications
    document.querySelectorAll('.notification-item').forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
