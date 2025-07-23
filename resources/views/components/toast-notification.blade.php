{{-- Toast Notification Component --}}
<div id="toast-container" class="fixed z-50 p-4 space-y-3 pointer-events-none" style="top: 1rem; right: 1rem;"></div>

{{-- Pass session messages via data attributes --}}
<div data-session-messages
     @if(session('success')) data-success="{{ session('success') }}" @endif
     @if(session('toast_success')) data-toast-success="{{ session('toast_success') }}" @endif
     @if(session('error')) data-error="{{ session('error') }}" @endif
     @if(session('toast_error')) data-toast-error="{{ session('toast_error') }}" @endif
     @if(session('warning')) data-warning="{{ session('warning') }}" @endif
     @if(session('toast_warning')) data-toast-warning="{{ session('toast_warning') }}" @endif
     @if(session('info')) data-info="{{ session('info') }}" @endif
     @if(session('toast_info')) data-toast-info="{{ session('toast_info') }}" @endif
     style="display: none;"></div>