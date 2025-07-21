/**
 * User Bookings JavaScript functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ensure page starts from top on load (best practice)
    if (history.scrollRestoration) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);

    // Add click effect to booking cards
    const bookingCards = document.querySelectorAll('.grid > div:not(.col-span-full)');
    bookingCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button or link
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON' && !e.target
                .closest('a') && !e.target.closest('button')) {
                // Find the view details link and click it
                const viewDetailsLink = card.querySelector('a[href*="booking"]');
                if (viewDetailsLink) {
                    viewDetailsLink.click();
                }
            }
        });
    });

    // Filter functionality
    const filterSelect = document.querySelector('select');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            // In a real application, this would filter the bookings
        });
    }

    // Profile edit button functionality
    const editProfileBtns = document.querySelectorAll('button');
    editProfileBtns.forEach(btn => {
        if (btn.textContent.includes('Edit Profile')) {
            btn.addEventListener('click', function() {
                // Add your edit profile functionality here
            });
        }
    });

    // Navigation tab functionality
    function setActiveTab(activeTab) {
        // Remove active state from all tabs
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.classList.remove('text-primary', 'border-b-2', 'border-primary');
            tab.classList.add('text-secondary');
        });

        // Add active state to clicked tab
        const activeTabElement = document.querySelector(`[data-tab="${activeTab}"]`);
        if (activeTabElement) {
            activeTabElement.classList.remove('text-secondary');
            activeTabElement.classList.add('text-primary', 'border-b-2', 'border-primary');
            
            // Ensure proper hover state
            activeTabElement.classList.add('hover:text-primary-dark');
        }

        // Hide all content sections
        document.querySelectorAll(
            '#dashboard-content, #bookings-content, #settings-content, #help-content'
        ).forEach(content => {
            content.classList.add('hidden');
        });

        // Show corresponding content
        const contentMap = {
            'dashboard': 'dashboard-content',
            'bookings': 'bookings-content',
            'settings': 'settings-content',
            'help': 'help-content'
        };

        const contentId = contentMap[activeTab];
        if (contentId) {
            const contentElement = document.getElementById(contentId);
            if (contentElement) {
                contentElement.classList.remove('hidden');
            }
        }
    }

    // Set active filter button
    function setActiveFilter(button) {
        // Remove active class from all filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-primary-light', 'text-primary-dark', 'border-primary-light');
            btn.classList.add('bg-neutral-light', 'text-secondary');
        });

        // Add active class to selected button
        button.classList.remove('bg-neutral-light', 'text-secondary');
        button.classList.add('bg-primary-light', 'text-primary-dark', 'border-primary-light');
    }

    // Set active view button
    function setActiveView(button) {
        // Remove active class from all view buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-secondary-dark', 'shadow-sm');
            btn.classList.add('text-secondary');
        });

        // Add active class to selected button
        button.classList.remove('text-secondary');
        button.classList.add('bg-white', 'text-secondary-dark', 'shadow-sm');
    }

    // Add click event listeners to navigation tabs
    const navTabs = document.querySelectorAll('.nav-tab');

    navTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            setActiveTab(tabName);
        });
    });

    // Add click event listeners to filter buttons
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            setActiveFilter(this);
            // Here you can add logic to filter bookings based on the selected filter
        });
    });

    // Add click event listeners to view toggle buttons
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            setActiveView(this);
            // Here you can add logic to change the view layout
        });
    });

    // Set default active tab (dashboard as default)
    setActiveTab('dashboard');
});