   // Toggle Password Visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password Strength Indicator
        const passwordField = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength');

        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            // Calculate strength
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/\d/.test(password) && /[!@#$%^&*]/.test(password)) strength += 25;

            // Update bar
            strengthBar.style.width = strength + '%';

            // Color based on strength
            if (strength <= 25) {
                strengthBar.style.backgroundColor = '#ef4444'; // red
            } else if (strength <= 50) {
                strengthBar.style.backgroundColor = '#f59e0b'; // orange
            } else if (strength <= 75) {
                strengthBar.style.backgroundColor = '#eab308'; // yellow
            } else {
                strengthBar.style.backgroundColor = '#22c55e'; // green
            }
        });

        // Form Validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });

            // Validate first name
            const firstname = document.getElementById('firstname');
            if (firstname.value.trim().length < 2) {
                showError('firstname', 'First name must be at least 2 characters');
                isValid = false;
            }

            // Validate last name
            const lastname = document.getElementById('lastname');
            if (lastname.value.trim().length < 2) {
                showError('lastname', 'Last name must be at least 2 characters');
                isValid = false;
            }

            // Validate email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password
            const password = document.getElementById('password');
            if (password.value.length < 8) {
                showError('password', 'Password must be at least 8 characters');
                isValid = false;
            }

            // Validate password confirmation
            const confirmPassword = document.getElementById('confirm_password');
            if (password.value !== confirmPassword.value) {
                showError('confirm-password', 'Passwords do not match');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        function showError(fieldId, message) {
            const errorEl = document.getElementById(fieldId + '-error');
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }