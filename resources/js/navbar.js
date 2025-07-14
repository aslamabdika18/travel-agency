/**
 * Navbar JavaScript functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isHidden = mobileMenu.classList.contains('hidden');

            if (isHidden) {
                // Tampilkan menu mobile dengan animasi
                mobileMenu.classList.remove('hidden');
                requestAnimationFrame(() => {
                    mobileMenu.classList.remove('opacity-0', 'max-h-0');
                    mobileMenu.classList.add('opacity-100', 'max-h-96');
                });
                // Ubah ikon menjadi X
                mobileMenuButton.innerHTML = '<svg class="h-6 w-6 xs:h-7 xs:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            } else {
                // Sembunyikan menu mobile dengan animasi
                mobileMenu.classList.remove('opacity-100', 'max-h-96');
                mobileMenu.classList.add('opacity-0', 'max-h-0');
                setTimeout(() => mobileMenu.classList.add('hidden'), 300);
                // Ubah ikon menjadi hamburger
                mobileMenuButton.innerHTML = '<svg class="h-6 w-6 xs:h-7 xs:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
            }
        });
    }

    // User dropdown functionality
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenu = document.getElementById('userMenu');

    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            userMenu.classList.toggle('hidden');
            
            // Toggle chevron icon if it exists
            const chevron = userMenuButton.querySelector('.fa-chevron-down, .fa-chevron-up');
            if (chevron) {
                if (userMenu.classList.contains('hidden')) {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                } else {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-up');
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
                
                // Reset chevron icon if it exists
                const chevron = userMenuButton.querySelector('.fa-chevron-up, .fa-chevron-down');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                }
            }
        });
    }

    // Close mobile menu on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('opacity-100', 'max-h-96');
            mobileMenu.classList.add('opacity-0', 'max-h-0', 'hidden');
            mobileMenuButton.innerHTML = '<svg class="h-6 w-6 xs:h-7 xs:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
        }
    });
});