/**
 * Toast notification system for Sumatra Tour Travel
 */

class Toast {
    constructor() {
        this.defaultOptions = {
            position: 'top-right',
            autoClose: 2500,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: false,
            draggable: true,
            progress: undefined,
        };

        this.createContainer();
    }

    createContainer() {
        // Check if container already exists
        if (document.getElementById('toast-container')) return;

        // Create container
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed z-50 p-4 space-y-3 pointer-events-none';
        container.style.cssText = 'top: 1rem; right: 1rem;';
        document.body.appendChild(container);
    }

    createToast(message, type = 'info', options = {}) {
        const container = document.getElementById('toast-container');
        if (!container) this.createContainer();

        // Merge options with defaults
        const toastOptions = { ...this.defaultOptions, ...options };

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `relative flex items-center w-full max-w-xs p-4 mb-3 text-gray-500 bg-white rounded-lg shadow transition-all transform translate-x-0 pointer-events-auto ${this.getTypeClass(type)} overflow-hidden`;
        toast.role = 'alert';
        toast.innerHTML = this.getToastContent(message, type, toastOptions);

        // Add to container with animation
        toast.style.transition = 'opacity 300ms ease-out, transform 300ms ease-out';
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 10);

        // Add close button event
        const closeButton = toast.querySelector('.toast-close-button');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                this.removeToast(toast);
            });
        }

        // Close on click if enabled
        if (toastOptions.closeOnClick) {
            toast.addEventListener('click', (e) => {
                if (!e.target.closest('.toast-close-button')) {
                    this.removeToast(toast);
                }
            });
        }

        // Add progress bar and timer functionality
        let timeoutId;
        let progressBar = toast.querySelector('.toast-progress-bar');
        let opacityInterval;

        if (toastOptions.pauseOnHover && toastOptions.autoClose) {
            let remainingTime = toastOptions.autoClose;
            let startTime = Date.now();
            let isPaused = false;
            let opacityStarted = false;
            let opacityTimeoutId;

            const startTimer = () => {
                if (isPaused) return;

                startTime = Date.now();

                // Start progress bar animation
                if (progressBar) {
                    progressBar.style.transition = `width ${remainingTime}ms linear`;
                    progressBar.style.width = '0%';
                }

                // Start opacity fade animation after 50% of time
                const opacityStartDelay = remainingTime * 0.5; // Mulai pada 50% waktu
                const opacityDuration = remainingTime * 0.5; // Durasi fade 50% waktu tersisa
                
                opacityTimeoutId = setTimeout(() => {
                    if (isPaused) return;
                    
                    opacityStarted = true;
                    const opacityStep = 1 / (opacityDuration / 50); // Update every 50ms
                    let currentOpacity = 1;
                    
                    opacityInterval = setInterval(() => {
                        if (isPaused) return;
                        currentOpacity -= opacityStep;
                        if (currentOpacity <= 0) {
                            currentOpacity = 0;
                            clearInterval(opacityInterval);
                        }
                        toast.style.opacity = currentOpacity;
                    }, 50);
                }, opacityStartDelay);

                timeoutId = setTimeout(() => {
                    clearInterval(opacityInterval);
                    clearTimeout(opacityTimeoutId);
                    this.removeToast(toast);
                }, remainingTime);
            };

            const pauseTimer = () => {
                isPaused = true;
                clearTimeout(timeoutId);
                clearTimeout(opacityTimeoutId);
                clearInterval(opacityInterval);
                remainingTime -= Date.now() - startTime;

                // Pause progress bar
                if (progressBar) {
                    const currentWidth = progressBar.getBoundingClientRect().width;
                    const containerWidth = progressBar.parentElement.getBoundingClientRect().width;
                    const percentage = ((containerWidth - currentWidth) / containerWidth) * 100;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = percentage + '%';
                }
            };

            const resumeTimer = () => {
                isPaused = false;
                opacityStarted = false;
                startTimer();
            };

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', resumeTimer);

            startTimer();
        } else if (toastOptions.autoClose) {
            // Start progress bar animation
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 10);
            }

            // Start opacity fade animation after 50% of time
            const opacityStartDelay = toastOptions.autoClose * 0.5; // Mulai pada 50% waktu
            const opacityDuration = toastOptions.autoClose * 0.5; // Durasi fade 50% waktu tersisa
            
            setTimeout(() => {
                const opacityStep = 1 / (opacityDuration / 50); // Update every 50ms
                let currentOpacity = 1;
                
                opacityInterval = setInterval(() => {
                    currentOpacity -= opacityStep;
                    if (currentOpacity <= 0) {
                        currentOpacity = 0;
                        clearInterval(opacityInterval);
                    }
                    toast.style.opacity = currentOpacity;
                }, 50);
            }, opacityStartDelay);

            timeoutId = setTimeout(() => {
                clearInterval(opacityInterval);
                this.removeToast(toast);
            }, toastOptions.autoClose);
        }

        return toast;
    }

    removeToast(toast) {
        // Add smooth fade out animation
        toast.style.transition = 'opacity 300ms ease-out, transform 300ms ease-out';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    getTypeClass(type) {
        switch (type) {
            case 'success':
                return 'border-l-4 border-green-500';
            case 'error':
                return 'border-l-4 border-red-500';
            case 'warning':
                return 'border-l-4 border-yellow-500';
            case 'info':
                return 'border-l-4 border-blue-500';
            default:
                return 'border-l-4 border-gray-500';
        }
    }

    getToastContent(message, type, options = {}) {
        const icons = {
            success: '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg"><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg><span class="sr-only">Success icon</span></div>',
            error: '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg"><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.5 11.5-3 3a1 1 0 0 1-1.42 0l-3-3a1 1 0 0 1 1.42-1.42L9 12.1V6a1 1 0 0 1 2 0v6.1l1.58-1.59a1 1 0 0 1 1.42 1.42Z"/></svg><span class="sr-only">Error icon</span></div>',
            warning: '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-yellow-500 bg-yellow-100 rounded-lg"><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg><span class="sr-only">Warning icon</span></div>',
            info: '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg"><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg><span class="sr-only">Info icon</span></div>',
            default: '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-gray-500 bg-gray-100 rounded-lg"><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg><span class="sr-only">Info icon</span></div>'
        };

        // Get progress bar color based on type
        const progressColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
            default: 'bg-gray-500'
        };

        // Create progress bar if not hidden and autoClose is enabled
        const progressBar = (!options.hideProgressBar && options.autoClose) ?
            `<div class="absolute bottom-0 left-0 h-1 ${progressColors[type] || progressColors.default} toast-progress-bar" style="width: 100%; transition: width ${options.autoClose}ms linear;"></div>` : '';

        return `
            ${icons[type] || icons.default}
            <div class="ml-3 text-sm font-normal">${message}</div>
            <button type="button" class="toast-close-button ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
            ${progressBar}
        `;
    }

    // Public methods
    success(message, options = {}) {
        return this.createToast(message, 'success', options);
    }

    error(message, options = {}) {
        return this.createToast(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.createToast(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.createToast(message, 'info', options);
    }
}

// Create global toast instance
window.toast = new Toast();

// Export for module usage
export default window.toast;
