{{-- Toast Notification Component --}}
<div id="toast-container" class="fixed z-50 p-4 space-y-3 pointer-events-none" style="top: 1rem; right: 1rem;"></div>

@push('scripts')
<script>
    // Check if there's a flash message from the server
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.toast.success("{{ session('success') }}");
        @endif

        @if(session('toast_success'))
            window.toast.success("{{ session('toast_success') }}");
        @endif

        @if(session('error'))
            window.toast.error("{{ session('error') }}");
        @endif

        @if(session('toast_error'))
            window.toast.error("{{ session('toast_error') }}");
        @endif

        @if(session('warning'))
            window.toast.warning("{{ session('warning') }}");
        @endif

        @if(session('toast_warning'))
            window.toast.warning("{{ session('toast_warning') }}");
        @endif

        @if(session('info'))
            window.toast.info("{{ session('info') }}");
        @endif

        @if(session('toast_info'))
            window.toast.info("{{ session('toast_info') }}");
        @endif
    });
</script>
@endpush