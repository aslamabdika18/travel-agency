document.addEventListener('DOMContentLoaded', function() {
    // Toggle functionality between login and register
    const authToggle = document.getElementById('authToggle');
    const authForm = document.getElementById('authForm');
    const nameInput = document.getElementById('name');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    // Elements for toggling between login and register
    const loginIcon = document.querySelector('.login-icon');
    const registerIcon = document.querySelector('.register-icon');
    const loginTitle = document.querySelector('.login-title');
    const registerTitle = document.querySelector('.register-title');
    const loginSubtitle = document.querySelector('.login-subtitle');
    const registerSubtitle = document.querySelector('.register-subtitle');
    const nameField = document.querySelector('.name-field');
    const confirmPasswordField = document.querySelector('.confirm-password-field');
    
    if (authToggle && authForm) {
        authToggle.addEventListener('change', function() {
            if (this.checked) {
                // Register mode
                authForm.action = registerRoute;
                if (nameInput) nameInput.setAttribute('required', 'required');
                if (confirmPasswordInput) confirmPasswordInput.setAttribute('required', 'required');
                
                // Update UI for register mode
                loginIcon.classList.add('hidden');
                registerIcon.classList.remove('hidden');
                loginTitle.classList.add('hidden');
                registerTitle.classList.remove('hidden');
                loginSubtitle.classList.add('hidden');
                registerSubtitle.classList.remove('hidden');
                nameField.classList.remove('hidden', 'opacity-0', 'translate-y-5');
                confirmPasswordField.classList.remove('hidden', 'opacity-0', 'translate-y-5');
            } else {
                // Login mode
                authForm.action = loginRoute;
                if (nameInput) nameInput.removeAttribute('required');
                if (confirmPasswordInput) confirmPasswordInput.removeAttribute('required');
                
                // Update UI for login mode
                loginIcon.classList.remove('hidden');
                registerIcon.classList.add('hidden');
                loginTitle.classList.remove('hidden');
                registerTitle.classList.add('hidden');
                loginSubtitle.classList.remove('hidden');
                registerSubtitle.classList.add('hidden');
                nameField.classList.add('hidden', 'opacity-0', 'translate-y-5');
                confirmPasswordField.classList.add('hidden', 'opacity-0', 'translate-y-5');
            }
        });
    }
    
    // Toggle password visibility functionality
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const eyeOpenIcon = this.querySelector('.eye-open');
            const eyeClosedIcon = this.querySelector('.eye-closed');
            
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpenIcon.classList.add('hidden');
                eyeClosedIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpenIcon.classList.remove('hidden');
                eyeClosedIcon.classList.add('hidden');
            }
        });
    });
    
    // Handle form submission
    if (authForm) {
        authForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Create FormData object
            const formData = new FormData(authForm);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            let csrfTokenValue = null;
            
            if (csrfToken) {
                csrfTokenValue = csrfToken.getAttribute('content');
                
                if (!formData.has('_token')) {
                    formData.append('_token', csrfTokenValue);
                }
            }
            
            // Get form action URL
            const actionUrl = authForm.getAttribute('action');
            
            // Tampilkan indikator loading
            const submitButton = authForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            
            // Reset status button setelah 10 detik (jika request tidak selesai)
            const buttonTimeout = setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }, 10000);
            
            // Submit form using fetch API
            const headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            // Add CSRF token to headers if available
            if (csrfTokenValue) {
                headers['X-CSRF-TOKEN'] = csrfTokenValue;
            }
            
            // Use the action URL from the form or fallback to routes from login-routes.js
            const url = actionUrl || (authToggle.checked ? window.loginRoutes.register : window.loginRoutes.login);
            
            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: headers
            })
            .then(response => {
                // Clear the button timeout and reset button state if not redirecting
                clearTimeout(buttonTimeout);
                
                if (response.redirected) {
                    // If server redirects, follow the redirect
                    window.location.href = response.url;
                    return;
                }
                
                // Check content type to determine how to handle the response
                const contentType = response.headers.get('content-type');
                
                // Get response as text first
                return response.text().then(text => {
                    // Try to parse as JSON if content type indicates JSON or text looks like JSON
                    if ((contentType && contentType.includes('application/json')) || text.trim().startsWith('{')) {
                        try {
                            const data = JSON.parse(text);
                            
                            if (data.success) {
                                // Redirect on success
                                if (data.data && data.data.redirect_url) {
                                    window.location.replace(data.data.redirect_url);
                                } else {
                                    window.location.replace('/');
                                }
                            } else {
                                // Show error message
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;
        
                            }
                            return;
                        } catch (e) {
                            // Continue to HTML handling if JSON parsing fails
                        }
                    }
                    
                    // Handle as HTML response
                    if (response.ok) {
                        // If response is OK but not JSON, it's likely a successful redirect
                        window.location.href = '/';
                    } else {
                        // Otherwise show error and don't reload immediately
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                        
                        // Try to extract error message from HTML response
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = text;
                        const errorElements = tempDiv.querySelectorAll('.invalid-feedback, .alert-danger');
                        
                        if (errorElements.length > 0) {
                            // Display first error message found
    
                        } else {
                            // Generic error message
    
                        }
                    }
                });
            })
            .catch(error => {
                // Clear the button timeout and reset button state
                clearTimeout(buttonTimeout);
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                
                
            });
        });
    }
});