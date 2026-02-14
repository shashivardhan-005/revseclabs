/**
 * Password Complexity Validator
 * Real-time password strength checker with visual feedback
 */

class PasswordValidator {
    constructor(inputId, options = {}) {
        this.input = document.getElementById(inputId);
        if (!this.input) return;

        this.containerId = options.containerId || inputId + '_strength';
        this.settings = null;
        this.init();
    }

    async init() {
        // Fetch password settings from server
        await this.fetchSettings();

        // Create strength meter UI
        this.createStrengthMeter();

        // Attach event listeners
        this.input.addEventListener('input', () => {
            this.validatePassword();
            this.showRequirements(); // Ensure visible on input
        });

        // Popup interaction logic
        this.input.addEventListener('focus', () => this.showRequirements());
        this.input.addEventListener('blur', () => this.hideRequirements());

        // Setup Generator
        this.setupGenerator();

        // Find and attach to form submit
        this.form = this.input.closest('form');
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    createStrengthMeter() {
        // Remove existing if any
        const existing = document.getElementById(this.containerId);
        if (existing) existing.remove();

        // Create Container for Popup
        const container = document.createElement('div');
        container.id = this.containerId;
        container.className = 'password-strength-popup';

        // Base container style (positioning handled by CSS class to support media queries)
        // We set display:none initially. positioning is in CSS.
        container.style.display = 'none';

        container.innerHTML = `
            <div class="av-popup-card">
                <div class="av-popup-header">
                    <span>Security check</span>
                </div>
                <div class="av-popup-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white small">Password strength:</span>
                        <span class="password-strength-text fw-bold small"></span>
                    </div>
                    
                    <div class="password-strength-bar mb-3">
                        <div class="password-strength-progress" style="width: 0%;"></div>
                    </div>
                    
                    <div class="password-requirements mb-3">
                        <div class="text-white-50 small mb-2" style="font-size: 0.75rem;">Requirements:</div>
                        <div class="requirements-list"></div>
                    </div>

                    <button type="button" class="btn btn-sm btn-success w-100 generate-btn fw-bold">
                        <i class="bi bi-magic me-2"></i>Use password generator
                    </button>
                </div>
                <!-- Arrow pointing left (desktop) or up (mobile) - handled via CSS -->
                <div class="av-popup-arrow"></div>
            </div>
        `;

        // Insert after the input group
        let targetParent = this.input.closest('.position-relative') || this.input.parentElement;

        // Ensure parent is relative
        if (getComputedStyle(targetParent).position === 'static') {
            targetParent.style.position = 'relative';
        }

        targetParent.appendChild(container);

        // Attach event listener to the new button inside the popup
        const genBtn = container.querySelector('.generate-btn');
        if (genBtn) {
            genBtn.addEventListener('mousedown', (e) => {
                e.preventDefault(); // Prevent blur
                this.generatePassword();
            });
        }
    }

    showRequirements() {
        const popup = document.getElementById(this.containerId);
        if (popup) {
            popup.style.display = 'block';
            this.updateRequirementsList();
        }
    }

    hideRequirements() {
        const popup = document.getElementById(this.containerId);
        if (popup) {
            // Delay to prevent flickering or losing state too fast
            setTimeout(() => {
                if (document.activeElement !== this.input) {
                    popup.style.display = 'none';
                }
            }, 100);
        }
    }

    setupGenerator() {
        // Generator button is now inside the popup.
        // We do nothing here to avoid adding the external link.
    }

    generatePassword() {
        if (!this.settings) return;

        const length = Math.max(12, this.settings.min_length); // Default to 12 or min_length
        const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const lowercase = "abcdefghijklmnopqrstuvwxyz";
        const numbers = "0123456789";
        const special = "!@#$%^&*()_+-=[]{}|;:,.<>?";

        let charset = "";
        let password = "";

        // Ensure at least one of each required type
        if (this.settings.require_uppercase) {
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            charset += uppercase;
        }
        if (this.settings.require_lowercase) {
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            charset += lowercase;
        }
        if (this.settings.require_numbers) {
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            charset += numbers;
        }
        if (this.settings.require_special) {
            password += special.charAt(Math.floor(Math.random() * special.length));
            charset += special;
        }

        // Fill the rest
        if (charset === "") charset = lowercase + numbers; // Fallback

        while (password.length < length) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }

        // Shuffle password
        password = password.split('').sort(() => 0.5 - Math.random()).join('');

        this.fillPassword(password);
    }

    fillPassword(password) {
        // Set value
        this.input.value = password;

        // Handle confirm field
        let confirmId = '';
        if (this.input.id === 'newPassword') confirmId = 'confirmPassword';
        if (this.input.id === 'newPasswordChange') confirmId = 'confirmPasswordChange'; // Matches ID in password_change.php line 78

        if (confirmId) {
            const confirmInput = document.getElementById(confirmId);
            if (confirmInput) confirmInput.value = password;
        }

        // Show password (switch type to text)
        this.input.type = 'text';

        // Update toggle icon if present
        const toggleBtn = this.input.parentElement.querySelector('button i');
        if (toggleBtn) {
            toggleBtn.classList.remove('bi-eye-fill');
            toggleBtn.classList.add('bi-eye-slash-fill');
        }

        // Trigger validation
        this.validatePassword();
        this.showRequirements();
    }

    handleFormSubmit(event) {
        const password = this.input.value;
        if (!password || !this.settings) return true;

        const errors = [];

        // Check all requirements
        if (password.length < this.settings.min_length) {
            errors.push('length');
        }

        if (this.settings.require_uppercase && !/[A-Z]/.test(password)) {
            errors.push('uppercase');
        }

        if (this.settings.require_lowercase && !/[a-z]/.test(password)) {
            errors.push('lowercase');
        }

        if (this.settings.require_numbers && !/[0-9]/.test(password)) {
            errors.push('numbers');
        }

        if (this.settings.require_special && !/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) {
            errors.push('special');
        }

        if (errors.length > 0) {
            event.preventDefault();
            this.showError('Password does not meet the criteria. Please check the requirements above.');
            this.input.focus();
            return false;
        }

        return true;
    }

    showError(message) {
        // Remove any existing error
        const existingError = document.getElementById('password-policy-error');
        if (existingError) {
            existingError.remove();
        }

        // Create error alert
        const errorDiv = document.createElement('div');
        errorDiv.id = 'password-policy-error';
        errorDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
        errorDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert before form
        this.form.insertBefore(errorDiv, this.form.firstChild);

        // Scroll to error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async fetchSettings() {
        try {
            const response = await fetch(baseUrl + '/api/password-settings');
            if (response.ok) {
                this.settings = await response.json();
            } else {
                // Fallback to defaults
                this.settings = {
                    min_length: 8,
                    require_uppercase: true,
                    require_lowercase: true,
                    require_numbers: true,
                    require_special: false
                };
            }
        } catch (error) {
            console.error('Failed to fetch password settings:', error);
            // Use defaults
            this.settings = {
                min_length: 8,
                require_uppercase: true,
                require_lowercase: true,
                require_numbers: true,
                require_special: false
            };
        }
    }



    showRequirements() {
        const requirementsDiv = document.querySelector(`#${this.containerId} .password-requirements`);
        if (requirementsDiv) {
            requirementsDiv.style.display = 'block';
            this.updateRequirementsList();
        }
    }

    updateRequirementsList() {
        const requirementsList = document.querySelector(`#${this.containerId} .requirements-list`);
        if (!requirementsList || !this.settings) return;

        const requirements = [];

        requirements.push({
            key: 'length',
            text: `At least ${this.settings.min_length} characters`,
            check: (pwd) => pwd.length >= this.settings.min_length
        });

        if (this.settings.require_uppercase) {
            requirements.push({
                key: 'uppercase',
                text: 'One uppercase letter (A-Z)',
                check: (pwd) => /[A-Z]/.test(pwd)
            });
        }

        if (this.settings.require_lowercase) {
            requirements.push({
                key: 'lowercase',
                text: 'One lowercase letter (a-z)',
                check: (pwd) => /[a-z]/.test(pwd)
            });
        }

        if (this.settings.require_numbers) {
            requirements.push({
                key: 'numbers',
                text: 'One number (0-9)',
                check: (pwd) => /[0-9]/.test(pwd)
            });
        }

        if (this.settings.require_special) {
            requirements.push({
                key: 'special',
                text: 'One special character (!@#$%^&*)',
                check: (pwd) => /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(pwd)
            });
        }

        const password = this.input.value;
        requirementsList.innerHTML = requirements.map(req => {
            const met = req.check(password);
            return `
                <div class="requirement-item ${met ? 'requirement-met' : 'requirement-unmet'}">
                    <i class="bi ${met ? 'bi-check-circle-fill' : 'bi-x-circle-fill'}"></i>
                    ${req.text}
                </div>
            `;
        }).join('');
    }

    validatePassword() {
        const password = this.input.value;
        if (!password || !this.settings) return;

        let score = 0;
        let totalRequirements = 0;

        // Check length
        totalRequirements++;
        if (password.length >= this.settings.min_length) score++;

        // Check uppercase
        if (this.settings.require_uppercase) {
            totalRequirements++;
            if (/[A-Z]/.test(password)) score++;
        }

        // Check lowercase
        if (this.settings.require_lowercase) {
            totalRequirements++;
            if (/[a-z]/.test(password)) score++;
        }

        // Check numbers
        if (this.settings.require_numbers) {
            totalRequirements++;
            if (/[0-9]/.test(password)) score++;
        }

        // Check special characters
        if (this.settings.require_special) {
            totalRequirements++;
            if (/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) score++;
        }

        const percentage = (score / totalRequirements) * 100;
        this.updateStrengthMeter(percentage, score, totalRequirements);
        this.updateRequirementsList();
    }

    updateStrengthMeter(percentage, score, total) {
        const progressBar = document.querySelector(`#${this.containerId} .password-strength-progress`);
        const strengthText = document.querySelector(`#${this.containerId} .password-strength-text`);

        if (!progressBar || !strengthText) return;

        progressBar.style.width = percentage + '%';

        // Remove all strength classes
        progressBar.classList.remove('weak', 'medium', 'strong');

        let strength = '';
        let strengthClass = '';

        if (score === total) {
            strength = 'Strong password';
            strengthClass = 'strong';
            strengthText.className = 'password-strength-text text-success';
        } else if (percentage >= 60) {
            strength = 'Medium strength';
            strengthClass = 'medium';
            strengthText.className = 'password-strength-text text-warning';
        } else {
            strength = 'Weak password';
            strengthClass = 'weak';
            strengthText.className = 'password-strength-text text-danger';
        }

        progressBar.classList.add(strengthClass);
        strengthText.textContent = strength;
    }
}

// Auto-initialize for common password fields
document.addEventListener('DOMContentLoaded', function () {
    // Initialize for new password fields
    const passwordFields = [
        'newPassword',          // Reset password page
        'newPasswordChange'     // Password change page
    ];

    passwordFields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
            new PasswordValidator(fieldId);
        }
    });
});
