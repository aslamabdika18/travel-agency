document.addEventListener('DOMContentLoaded', function() {
    // Confetti animation for success page
    function launchConfetti() {
        const duration = 3000;
        const end = Date.now() + duration;
        
        (function frame() {
            // Launch a few confetti from the left edge
            confetti({
                particleCount: 7,
                angle: 60,
                spread: 55,
                origin: { x: 0, y: 0.6 }
            });
            // and launch a few from the right edge
            confetti({
                particleCount: 7,
                angle: 120,
                spread: 55,
                origin: { x: 1, y: 0.6 }
            });
            
            // Keep going until we are out of time
            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    }
    
    // Check if confetti library is loaded
    if (typeof confetti !== 'undefined') {
        launchConfetti();
    }
});