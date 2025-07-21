document.addEventListener('DOMContentLoaded', function() {
    // Ensure page starts from top on load (best practice)
    if (history.scrollRestoration) {
        history.scrollRestoration = 'manual';
    }
    
    // Only scroll to top if not configured to scroll to booking
    const scrollConfig = window.travelPackageConfig || {};
    if (!scrollConfig.shouldScrollToBooking) {
        window.scrollTo(0, 0);
    }
    
    // Fix animate-on-scroll elements that are already visible to prevent double-click issues
    document.querySelectorAll('.animate-on-scroll').forEach(item => {
        const rect = item.getBoundingClientRect();
        const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;
        
        if (isInViewport) {
            item.classList.add('visible');
        }
        
        // Ensure interactive elements inside animate-on-scroll remain clickable
        item.querySelectorAll('a, button, input, select, textarea').forEach(interactive => {
            interactive.style.pointerEvents = 'auto';
        });
    });
    
    // Get configuration from global variables set in the blade template
    // Use the combined config from travel-package-config.js if available, or fallback to separate configs
    const fullConfig = window.travelPackageFullConfig || {};
    const config = fullConfig.slug ? fullConfig : (window.travelPackageConfig || {});
    const data = fullConfig.basePersonCount ? fullConfig : (window.travelPackageData || {});

    // Initialize variables
    let personCount = data.basePersonCount || 1;
    const capacity = data.capacity || 12;
    const basePrice = data.price || 0;
    const additionalPersonPrice = data.additionalPersonPrice || 0;
    const taxPercentage = data.taxPercentage || 0;

    // DOM Elements
    const personCountEl = document.getElementById('person-count');
    const decrementBtn = document.getElementById('decrement-person');
    const incrementBtn = document.getElementById('increment-person');
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    const bookingForm = document.getElementById('bookingForm');
    const loginRedirectBtn = document.getElementById('login-redirect-btn');



    // Add form submit handler to prevent default submission on step 1
    if (bookingForm) {

        bookingForm.addEventListener('submit', function(event) {
            // Prevent form submission if we're on step 1
            const step1Element = document.getElementById('step1');
            if (step1Element && !step1Element.classList.contains('hidden')) {

                event.preventDefault();
                event.stopPropagation();

                // Try to find and click the next-to-step2 button
                const nextToStep2Button = document.getElementById('next-to-step2');
                if (nextToStep2Button) {

                    nextToStep2Button.click();
                } else {
                    // Try to handle the next step manually
                    handleNextToStep2();
                }
                return false;
            }
        });
        
        // Add keypress event listener to handle Enter key
        bookingForm.addEventListener('keypress', function(event) {
            // If Enter key is pressed and we're on step1
            if (event.key === 'Enter') {
                const step1Element = document.getElementById('step1');
                if (step1Element && !step1Element.classList.contains('hidden')) {
                    event.preventDefault();

                    handleNextToStep2();
                }
            }
        }, true); // Use capturing phase
    }

    // jQuery event handlers removed to prevent double click issues



    // Booking Steps Elements
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextToStep2Btn = document.getElementById('next-to-step2');
    const backToStep1Btn = document.getElementById('back-to-step1');
    const nextToStep3Btn = document.getElementById('next-to-step3'); // Now acts as Pay Now button
    const priceDetailsSidebar = document.getElementById('price-details-sidebar');



    // Add single event listener to next-to-step2 button if it exists
    if (nextToStep2Btn) {

        nextToStep2Btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            handleNextToStep2();
        }, { once: false });
    } else {

    }

    // Scroll to booking section if needed
    if (config.shouldScrollToBooking) {
        const bookingSection = document.getElementById('booking');
        if (bookingSection) {
            setTimeout(() => {
                bookingSection.scrollIntoView({ behavior: 'smooth' });
            }, 500);
        }
    }

    // Handle login redirect for guest users
    if (loginRedirectBtn && config.isGuest) {
        loginRedirectBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = config.loginUrl;
        });
    }

    // Initialize person count display
    if (personCountEl) {
        personCountEl.textContent = personCount;
    }

    // Handle decrement button
    if (decrementBtn) {
        decrementBtn.addEventListener('click', function() {
            if (personCount > 1) {
                personCount--;
                updatePersonCount();
                updatePriceCalculation();
            }
        });
    }

    // Handle increment button
    if (incrementBtn) {
        incrementBtn.addEventListener('click', function() {
            if (personCount < capacity) {
                personCount++;
                updatePersonCount();
                updatePriceCalculation();
            }
        });
    }

    // Handle person count select change
    const personCountSelect = document.getElementById('person_count');
    if (personCountSelect) {
        personCountSelect.addEventListener('change', function() {
            personCount = parseInt(this.value, 10) || 1;
            updatePersonCount();
            updatePriceCalculation();
        });
    }

    // Update person count display
    function updatePersonCount() {
        if (personCountEl) {
            personCountEl.textContent = personCount;
        }

        // Update hidden input for form submission
        const personCountInput = document.getElementById('person_count');
        if (personCountInput) {
            personCountInput.value = personCount;
        }

        // Update UI for increment/decrement buttons
        if (decrementBtn) {
            decrementBtn.classList.toggle('opacity-50', personCount <= 1);
            decrementBtn.classList.toggle('cursor-not-allowed', personCount <= 1);
        }

        if (incrementBtn) {
            incrementBtn.classList.toggle('opacity-50', personCount >= capacity);
            incrementBtn.classList.toggle('cursor-not-allowed', personCount >= capacity);
        }
    }

    // Calculate and update price display
    function updatePriceCalculation() {
        // Calculate additional person cost
        const additionalPersons = Math.max(0, personCount - data.basePersonCount);
        const additionalCost = additionalPersons * additionalPersonPrice;

        // Calculate subtotal
        const subtotal = basePrice + additionalCost;

        // Calculate tax
        const tax = subtotal * (taxPercentage / 100);

        // Calculate total
        const total = subtotal + tax;

        // Update display
        if (subtotalEl) {
            subtotalEl.textContent = formatCurrency(subtotal);
        }

        if (taxEl) {
            taxEl.textContent = formatCurrency(tax);
        }

        if (totalEl) {
            totalEl.textContent = formatCurrency(total);
        }

        // Update hidden input for form submission
        const totalInput = document.getElementById('total_price');
        if (totalInput) {
            totalInput.value = total;
        }

        // Update price details sidebar
        const basePriceEl = document.getElementById('base-price');
        const taxPriceEl = document.getElementById('tax-price');
        const totalPriceEl = document.getElementById('total-price');
        const basePriceLabelEl = document.getElementById('base-price-label');
        const totalLabelEl = document.getElementById('total-label');
        const additionalPersonCostEl = document.getElementById('additional-person-cost');

        if (basePriceEl) {
            basePriceEl.textContent = formatCurrency(basePrice);
        }

        if (taxPriceEl) {
            taxPriceEl.textContent = formatCurrency(tax);
        }

        if (totalPriceEl) {
            totalPriceEl.textContent = formatCurrency(total);
        }

        if (basePriceLabelEl) {
            basePriceLabelEl.textContent = `Base Price (${data.basePersonCount} ${data.basePersonCount === 1 ? 'person' : 'people'})`;
        }

        if (totalLabelEl) {
            totalLabelEl.textContent = `Total (for ${personCount} ${personCount === 1 ? 'person' : 'people'})`;
        }

        if (additionalPersonCostEl) {
            additionalPersonCostEl.innerHTML = '';

            if (additionalPersons > 0) {
                const div = document.createElement('div');
                div.className = 'flex justify-between';
                div.innerHTML = `
                    <span class="text-secondary text-sm sm:text-base">Additional ${additionalPersons} ${additionalPersons === 1 ? 'person' : 'people'}</span>
                    <span class="font-medium text-secondary-dark text-sm sm:text-base">${formatCurrency(additionalCost)}</span>
                `;
                additionalPersonCostEl.appendChild(div);
            }
        }

        // If we're on step 2, update the confirmation price summary as well
        if (step2 && !step2.classList.contains('hidden')) {
            updateConfirmationPriceSummary();
        }
    }

    // Format currency for display
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Initialize price calculation
    updatePriceCalculation();

    // Handle booking form submission
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            // Form validation can be added here if needed
        });
    }

    // Function to handle next-to-step2 button click
    function handleNextToStep2() {
    

        // Validate step 1 fields
        const fullName = document.getElementById('name');
        const contactNumber = document.getElementById('contact');
        const bookingDate = document.getElementById('booking_date');
        const terms = document.getElementById('terms');

    

        if (!fullName || !fullName.value) {
            return;
            if (fullName) fullName.focus();
            return;
        }

        if (!contactNumber || !contactNumber.value) {
            return;
            if (contactNumber) contactNumber.focus();
            return;
        }

        if (!bookingDate || !bookingDate.value) {
            return;
            if (bookingDate) bookingDate.focus();
            return;
        }

        if (!terms || !terms.checked) {
            return;
            if (terms) terms.focus();
            return;
        }

        // Update confirmation page with values
        const confirmName = document.getElementById('confirm-name');
        const confirmContact = document.getElementById('confirm-contact');
        const confirmDate = document.getElementById('confirm-date');

    

        if (confirmName) confirmName.textContent = fullName.value;
        if (confirmContact) confirmContact.textContent = contactNumber.value;
        if (confirmDate) confirmDate.textContent = new Date(bookingDate.value).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const confirmPersonCount = document.getElementById('confirm-person-count');
        if (confirmPersonCount) confirmPersonCount.textContent = personCount + (personCount === 1 ? ' Person' : ' People');

        const specialRequests = document.getElementById('special_requests');
        const specialRequestsContainer = document.getElementById('confirm-special-requests-container');
        const confirmSpecialRequests = document.getElementById('confirm-special-requests');

        if (specialRequests && specialRequests.value.trim() && specialRequestsContainer && confirmSpecialRequests) {
            confirmSpecialRequests.textContent = specialRequests.value;
            specialRequestsContainer.classList.remove('hidden');
        } else if (specialRequestsContainer) {
            specialRequestsContainer.classList.add('hidden');
        }

        // Update price summary
        updateConfirmationPriceSummary();

        // Show step 2, hide step 1
        const step1Element = document.getElementById('step1');
        const step2Element = document.getElementById('step2');

    

        if (step1Element) step1Element.classList.add('hidden');
        if (step2Element) step2Element.classList.remove('hidden');

        // Update step indicators
        const stepIndicator1 = document.getElementById('step-indicator-1');
        const stepIndicator2 = document.getElementById('step-indicator-2');
        const progressBar1 = document.getElementById('progress-bar-1');
        const stepLabel1 = stepIndicator1 ? stepIndicator1.nextElementSibling : null;
        const stepLabel2 = stepIndicator2 ? stepIndicator2.nextElementSibling : null;
        
        if (stepIndicator1) {
            stepIndicator1.classList.remove('bg-primary', 'text-white');
            stepIndicator1.classList.add('bg-green-500', 'text-white');
        }
        
        if (stepLabel1) {
            stepLabel1.classList.remove('text-primary');
            stepLabel1.classList.add('text-green-500');
        }
        
        if (stepIndicator2) {
            stepIndicator2.classList.remove('bg-gray-200', 'text-gray-500');
            stepIndicator2.classList.add('bg-primary', 'text-white');
        }
        
        if (stepLabel2) {
            stepLabel2.classList.remove('text-gray-500');
            stepLabel2.classList.add('text-primary');
        }
        
        if (progressBar1) {
            progressBar1.style.width = '100%';
        }

        // Show price details sidebar
        const priceDetailsSidebarElement = document.getElementById('price-details-sidebar');
        if (priceDetailsSidebarElement) {
            priceDetailsSidebarElement.classList.remove('hidden');
        }
    }

    // Event delegation removed to prevent double click issues

    // Simplified function removed to prevent multiple event listeners

    // Simple event delegation for next-to-step2 button
    document.addEventListener('click', function(event) {
        const target = event.target;
        
        // Check if clicked element is the next-to-step2 button or contains the right text
        if (target.id === 'next-to-step2' || 
            (target.tagName === 'BUTTON' && target.textContent && target.textContent.trim() === 'Continue to Confirmation')) {
            event.preventDefault();
            event.stopPropagation();
            handleNextToStep2();
        }
    });

    // Simple Enter key handler for step1
    document.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            const step1Element = document.getElementById('step1');
            const step1Visible = step1Element && !step1Element.classList.contains('hidden');
            
            if (step1Visible && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
                handleNextToStep2();
            }
        }
    });

    // Handle back to step 1 button
    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'back-to-step1') {
    

            const step1Element = document.getElementById('step1');
            const step2Element = document.getElementById('step2');

            if (step2Element) step2Element.classList.add('hidden');
            if (step1Element) step1Element.classList.remove('hidden');
            
            // Reset step indicators
            const stepIndicator1 = document.getElementById('step-indicator-1');
            const stepIndicator2 = document.getElementById('step-indicator-2');
            const progressBar1 = document.getElementById('progress-bar-1');
            const stepLabel1 = stepIndicator1 ? stepIndicator1.nextElementSibling : null;
            const stepLabel2 = stepIndicator2 ? stepIndicator2.nextElementSibling : null;
            
            if (stepIndicator1) {
                stepIndicator1.classList.remove('bg-green-500');
                stepIndicator1.classList.add('bg-primary', 'text-white');
            }
            
            if (stepLabel1) {
                stepLabel1.classList.remove('text-green-500');
                stepLabel1.classList.add('text-primary');
            }
            
            if (stepIndicator2) {
                stepIndicator2.classList.remove('bg-primary', 'text-white');
                stepIndicator2.classList.add('bg-gray-200', 'text-gray-500');
            }
            
            if (stepLabel2) {
                stepLabel2.classList.remove('text-primary');
                stepLabel2.classList.add('text-gray-500');
            }
            
            if (progressBar1) {
                progressBar1.style.width = '50%';
            }
        }
    });

    // Handle pay now button (AJAX submit)
    document.addEventListener('click', function(event) {
        if (event.target && (event.target.id === 'next-to-step3' || 
            (event.target.parentNode && event.target.parentNode.id === 'next-to-step3'))) {
    
            event.preventDefault();
            event.stopPropagation();
            
            // Check if form exists
            if (!bookingForm) {
    
                return;
                return;
            }
            
            // Validate form data
            const formData = new FormData(bookingForm);

            
            // Check required fields
            const requiredFields = ['name', 'contact', 'booking_date', 'person_count', 'terms'];
            let missingFields = [];
            
            for (let field of requiredFields) {
                const value = formData.get(field);
                if (!value || (field === 'terms' && value !== 'on')) {
                    missingFields.push(field);
                }
            }
            
            if (missingFields.length > 0) {

                return;
                return;
            }
            
            // Update step indicators
            const stepIndicator2 = document.getElementById('step-indicator-2');
            const progressBar1 = document.getElementById('progress-bar-1');
            
            if (stepIndicator2) {
                stepIndicator2.classList.remove('bg-gray-200', 'text-gray-500');
                stepIndicator2.classList.add('bg-primary', 'text-white');
            }
            
            if (progressBar1) {
                progressBar1.style.width = '100%';
            }
            
            // Show loading indicator
            const payButton = document.getElementById('next-to-step3');
            if (!payButton) {

                return;
                return;
            }
            const originalButtonText = payButton.innerHTML;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            payButton.disabled = true;
            
            // Submit the form using fetch API to get JSON response

            
            // Convert FormData to URL-encoded string
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            
            // Add header to indicate we want JSON response
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            
            // Get CSRF token from meta tag
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenMeta) {
                const csrfToken = csrfTokenMeta.getAttribute('content');
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }
            }
            
            try {
                fetch(bookingForm.action, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(formDataObj)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
    
                    
                    if (data.success && data.data && data.data.snap_redirect_url) {
                        // Redirect to Midtrans Snap URL
    
                        window.location.href = data.data.snap_redirect_url;
                    } else {
                        // Handle error
    

                        if (payButton) {
                            payButton.innerHTML = originalButtonText;
                            payButton.disabled = false;
                        }
                    }
                })
                .catch(error => {
    

                    if (payButton) {
                        payButton.innerHTML = originalButtonText;
                        payButton.disabled = false;
                    }
                });
            } catch (error) {


                if (payButton) {
                    payButton.innerHTML = originalButtonText;
                    payButton.disabled = false;
                }
            }
        }
    });

    // Function to update confirmation price summary
    function updateConfirmationPriceSummary() {


        const confirmBasePrice = document.getElementById('confirm-base-price');
        const confirmTaxPrice = document.getElementById('confirm-tax-price');
        const confirmTotalPrice = document.getElementById('confirm-total-price');
        const confirmBasePriceLabel = document.getElementById('confirm-base-price-label');
        const confirmTotalLabel = document.getElementById('confirm-total-label');
        const confirmAdditionalPersonCost = document.getElementById('confirm-additional-person-cost');



        // Calculate additional person cost
        const additionalPersons = Math.max(0, personCount - data.basePersonCount);
        const additionalCost = additionalPersons * additionalPersonPrice;

        // Calculate subtotal
        const subtotal = basePrice + additionalCost;

        // Calculate tax
        const tax = subtotal * (taxPercentage / 100);

        // Calculate total
        const total = subtotal + tax;



        // Update base price label
        if (confirmBasePriceLabel) {
            confirmBasePriceLabel.textContent = `Base Price (${data.basePersonCount} ${data.basePersonCount === 1 ? 'person' : 'people'})`;
        }

        // Update total label
        if (confirmTotalLabel) {
            confirmTotalLabel.textContent = `Total (for ${personCount} ${personCount === 1 ? 'person' : 'people'})`;
        }

        // Update display
        if (confirmBasePrice) confirmBasePrice.textContent = formatCurrency(basePrice);
        if (confirmTaxPrice) confirmTaxPrice.textContent = formatCurrency(tax);
        if (confirmTotalPrice) confirmTotalPrice.textContent = formatCurrency(total);

        // Update additional person cost if any
        if (confirmAdditionalPersonCost) {
            confirmAdditionalPersonCost.innerHTML = '';

            if (additionalPersons > 0) {
                const div = document.createElement('div');
                div.className = 'flex justify-between';
                div.innerHTML = `
                    <span class="text-secondary">Additional ${additionalPersons} ${additionalPersons === 1 ? 'person' : 'people'}</span>
                    <span class="font-medium text-secondary-dark">${formatCurrency(additionalCost)}</span>
                `;
                confirmAdditionalPersonCost.appendChild(div);
            }
        }
    }

    // Handle gallery image click for lightbox
    const galleryImages = document.querySelectorAll('.gallery-image');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const closeLightbox = document.getElementById('close-lightbox');

    if (galleryImages.length > 0 && lightbox && lightboxImage) {
        galleryImages.forEach(image => {
            image.addEventListener('click', function() {
                const imgSrc = this.getAttribute('src');
                lightboxImage.setAttribute('src', imgSrc);
                lightbox.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        if (closeLightbox) {
            closeLightbox.addEventListener('click', function() {
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }

        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    }
});