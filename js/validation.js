// Form validation for registration and login forms
document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation
    const registerForm = document.querySelector('form');
    if (registerForm && window.location.pathname.includes('register.html')) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.querySelector('input[type="text"]');
            const email = document.querySelector('input[type="email"]');
            const password = document.querySelector('input[type="password"]');
            const phone = document.querySelector('input[type="tel"]');
            
            let isValid = true;
            
            // Clear previous errors
            clearErrors();
            
            // Name validation
            if (!name.value.trim()) {
                showError(name, 'Full name is required');
                isValid = false;
            }
            
            // Email validation
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            }
            
            // Password validation
            if (!password.value) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (password.value.length < 6) {
                showError(password, 'Password must be at least 6 characters');
                isValid = false;
            }
            
            // Phone validation
            if (phone && !phone.value.trim()) {
                showError(phone, 'Phone number is required');
                isValid = false;
            }
            
            if (isValid) {
                // Show success message
                alert('Registration successful! Redirecting to login...');
                window.location.href = 'login.html';
            }
        });
    }
    
    // Login form validation
    const loginForm = document.querySelector('form');
    if (loginForm && window.location.pathname.includes('login.html')) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.querySelector('input[type="email"]');
            const password = document.querySelector('input[type="password"]');
            
            let isValid = true;
            
            clearErrors();
            
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            }
            
            if (!password.value) {
                showError(password, 'Password is required');
                isValid = false;
            }
            
            if (isValid) {
                // Simulate login process
                alert('Login successful! Redirecting to dashboard...');
                window.location.href = 'events.html';
            }
        });
    }
    
    // Event registration
    const registerButtons = document.querySelectorAll('.btn-primary');
    registerButtons.forEach(button => {
        if (button.textContent.includes('Register Now')) {
            button.addEventListener('click', function() {
                if (confirm('Do you want to register for this event?')) {
                    alert('Successfully registered for the event!');
                    this.textContent = 'Registered ✓';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    this.disabled = true;
                }
            });
        }
    });
});

// Helper functions
function showError(input, message) {
    const formGroup = input.parentElement;
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-danger small mt-1';
    errorDiv.textContent = message;
    formGroup.appendChild(errorDiv);
    input.classList.add('is-invalid');
}

function clearErrors() {
    // Remove existing error messages
    document.querySelectorAll('.text-danger').forEach(error => error.remove());
    // Remove invalid styling
    document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Real-time validation
document.addEventListener('input', function(e) {
    if (e.target.type === 'email' && e.target.value) {
        if (!isValidEmail(e.target.value)) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    }
});