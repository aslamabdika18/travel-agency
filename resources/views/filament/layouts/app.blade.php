<x-filament-panels::layout.base :livewire="$livewire">
    {{ $slot }}
    
    <!-- Filament App JavaScript -->
    @vite('resources/js/filament-app.js')
</x-filament-panels::layout.base>