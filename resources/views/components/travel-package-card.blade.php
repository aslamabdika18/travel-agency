@props(['package', 'isBestseller' => false, 'isEcoTour' => false])

<div class="bg-neutral rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-on-scroll">
    <div class="relative">
        @if($package->thumbnailUrl)
            <img src="{{ $package->thumbnailUrl }}" alt="{{ $package->name }}" class="w-full h-40 xs:h-44 sm:h-48 md:h-52 lg:h-56 object-cover transition duration-300 hover:scale-105">
        @else
            <div class="w-full h-40 xs:h-44 sm:h-48 md:h-52 lg:h-56 bg-neutral-dark flex items-center justify-center">
                <i class="fas fa-image text-3xl xs:text-4xl text-neutral-light"></i>
            </div>
        @endif

        <div class="absolute top-2 xs:top-3 sm:top-4 right-2 xs:right-3 sm:right-4 bg-primary text-white text-2xs xs:text-xs sm:text-sm font-bold px-2 xs:px-3 py-1 rounded-full shadow-md">
            {{ $package->duration }}
        </div>

        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent h-14 xs:h-16 sm:h-18 md:h-20">
            <div class="absolute bottom-1 xs:bottom-2 sm:bottom-3 md:bottom-4 left-2 xs:left-3 sm:left-4 text-white">
                <span class="text-2xs xs:text-xs sm:text-sm font-medium">Starting from</span>
                <p class="text-lg xs:text-xl sm:text-2xl font-bold">{{ $package->formattedPrice }} <span class="text-2xs xs:text-xs sm:text-sm font-normal">/person</span></p>
            </div>
        </div>

        @if($isBestseller)
            <div class="absolute top-2 xs:top-3 sm:top-4 left-2 xs:left-3 sm:left-4 bg-primary-light text-primary text-2xs xs:text-xs font-semibold px-1.5 py-0.5 xs:px-2 xs:py-0.5 sm:px-2.5 sm:py-0.5 rounded shadow-md">
                BESTSELLER
            </div>
        @elseif($isEcoTour)
            <div class="absolute top-2 xs:top-3 sm:top-4 left-2 xs:left-3 sm:left-4 bg-green-100 text-green-800 text-2xs xs:text-xs font-semibold px-1.5 py-0.5 xs:px-2 xs:py-0.5 sm:px-2.5 sm:py-0.5 rounded shadow-md">
                ECO-TOUR
            </div>
        @endif
    </div>

    <div class="p-3 xs:p-4 sm:p-5 md:p-6">
        <div class="flex flex-wrap justify-between items-start mb-2 xs:mb-3 sm:mb-4">
            <h3 class="text-base xs:text-lg sm:text-xl font-bold text-secondary-dark pr-2 flex-1 line-clamp-2">{{ $package->name }}</h3>
        </div>

        @if($package->location)
            <div class="flex items-center text-secondary mb-2 xs:mb-3 sm:mb-4 text-2xs xs:text-sm sm:text-base">
                <i class="fas fa-map-marker-alt mr-1.5 xs:mr-2 text-primary"></i>
                <span class="truncate">{{ $package->location }}</span>
            </div>
        @endif

        @if($package->travelIncludes && $package->travelIncludes->count() > 0)
            <ul class="space-y-0.5 xs:space-y-1 sm:space-y-2 mb-3 xs:mb-4 sm:mb-6 text-2xs xs:text-sm sm:text-base">
                @foreach($package->travelIncludes->take(4) as $include)
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1.5 xs:mr-2 text-2xs xs:text-xs sm:text-sm flex-shrink-0"></i>
                    <span class="line-clamp-1">{{ $include->name }}</span>
                </li>
                @endforeach
            </ul>
        @endif

        @if($package->reviewCount > 0)
            <div class="flex items-center mb-3 xs:mb-4 sm:mb-6">
                <div class="flex items-center">
                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                    <span class="text-secondary-dark font-medium text-2xs xs:text-sm sm:text-base">{{ number_format((float)$package->averageRating, 1) }} ({{ $package->reviewCount }})</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col xs:flex-row justify-between items-stretch xs:items-center gap-2 xs:gap-3">
            <a href="{{ route('travel-package-detail', $package->slug) }}" class="text-primary font-semibold hover:text-primary-dark text-2xs xs:text-sm sm:text-base transition duration-200 text-center xs:text-left">View Details</a>
            <a href="{{ route('travel-package-detail', $package->slug) }}" class="bg-primary hover:bg-primary-dark text-white font-bold py-1.5 xs:py-2 sm:py-2.5 px-3 xs:px-4 sm:px-5 rounded-full transition duration-300 transform hover:scale-105 text-2xs xs:text-xs sm:text-sm text-center whitespace-nowrap">Book Now</a>
        </div>
    </div>
</div>
