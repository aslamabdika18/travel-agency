document.addEventListener('DOMContentLoaded', function() {
    // Simulate payment processing with progress bar
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    const progressSteps = [
        { width: '20%', text: 'Verifying payment details...' },
        { width: '40%', text: 'Processing transaction...' },
        { width: '60%', text: 'Confirming with payment provider...' },
        { width: '80%', text: 'Finalizing your booking...' },
        { width: '100%', text: 'Payment confirmed! Redirecting...' }
    ];
    
    let currentStep = 0;
    
    // Function to update progress
    function updateProgress() {
        if (currentStep < progressSteps.length) {
            progressBar.style.width = progressSteps[currentStep].width;
            progressText.textContent = progressSteps[currentStep].text;
            currentStep++;
            
            // If we've reached the final step, redirect after a delay
            if (currentStep === progressSteps.length) {
                setTimeout(function() {
                    // In a real application, this would be determined by the actual payment result
                    // For demo purposes, we'll randomly choose success or failure
                    const paymentSuccessful = Math.random() > 0.3; // 70% chance of success
                    
                    if (paymentSuccessful) {
                        window.location.href = window.paymentCallbackRoutes.success;
                    } else {
                        window.location.href = window.paymentCallbackRoutes.error;
                    }
                }, 2000);
            }
        }
    }
    
    // Start progress updates
    updateProgress();
    const progressInterval = setInterval(function() {
        if (currentStep < progressSteps.length) {
            updateProgress();
        } else {
            clearInterval(progressInterval);
        }
    }, 2000); // Update every 2 seconds
    
    // Simulate a timeout after 1 minute if no redirect happens
    setTimeout(function() {
        clearInterval(progressInterval);
        if (currentStep < progressSteps.length) {
            progressText.textContent = 'Payment processing is taking longer than expected. Please wait...';
        }
    }, 60000);
});