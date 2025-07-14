document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle
    const faqToggles = document.querySelectorAll('.faq-toggle');

    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('i');

            // Toggle the content
            content.classList.toggle('hidden');

            // Rotate the icon
            if (content.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
});