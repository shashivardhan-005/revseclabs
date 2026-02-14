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

        // Styles for the "AV Popup" look
        Object.assign(container.style, {
            display: 'none',
            position: 'absolute',
            zIndex: '1050',
            width: '100%',
            maxWidth: '280px',
            top: '100%',
            left: '0',
            marginTop: '8px',
            pointerEvents: 'none' // Let clicks pass through if it overlaps something irrelevant, though usually we want to see it. 
            // Actually, we might want to select text, so pointerEvents: auto is better. 
            // But if it hides on blur, clicking it might allow it to stay if we handle mousedown... 
            // For now, standard behavior.
        });

        container.innerHTML = `
            <div class="card shadow-lg border-0">
                <div class="card-body p-3 bg-white rounded-3 border border-opacity-10">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-uppercase small mb-0 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Password Strength</label>
                        <small class="password-strength-text fw-bold" style="font-size: 0.75rem;"></small>
                    </div>
                    
                    <div class="password-strength-bar mb-3" style="height: 4px; background: #eee; border-radius: 2px; overflow: hidden;">
                        <div class="password-strength-progress" style="width: 0%; height: 100%; transition: width 0.3s ease, background-color 0.3s ease;"></div>
                    </div>
                    
                    <div class="password-requirements">
                        <div class="requirements-list"></div>
                    </div>
                </div>
                <!-- Little arrow pointing up -->
                <div style="position: absolute; top: -6px; left: 20px; width: 12px; height: 12px; background: white; transform: rotate(45deg); border-left: 1px solid rgba(0,0,0,0.1); border-top: 1px solid rgba(0,0,0,0.1);"></div>
            </div>
        `;

        // Insert after the input group
        let targetParent = this.input.closest('.position-relative') || this.input.parentElement;

        // Ensure parent is relative for absolute positioning
        if (getComputedStyle(targetParent).position === 'static') {
            targetParent.style.position = 'relative';
        }

        targetParent.appendChild(container);

        // Add specific styles for the list items if needed
        if (!document.getElementById('password-validator-styles')) {
            const style = document.createElement('style');
            style.id = 'password-validator-styles';
            style.textContent = `
                .password-strength-progress.weak { background-color: #dc3545 !important; }
                .password-strength-progress.medium { background-color: #ffc107 !important; }
                .password-strength-progress.strong { background-color: #198754 !important; }
                
                .requirement-item {
                    font-size: 0.8rem;
                    line-height: 1.4;
                    color: #6c757d;
                    display: flex;
                    align-items: center;
                    margin-bottom: 2px;
                }
                .requirement-item i {
                    font-size: 1rem;
                    margin-right: 8px;
                    width: 16px; 
                    display: flex;
                    justify-content: center;
                }
                .requirement-met { color: #198754 !important; }
                .requirement-met i { color: #198754; }
                
                .requirement-unmet { color: #dc3545 !important; opacity: 0.8; }
                .requirement-unmet i { color: #dc3545; }
            `;
            document.head.appendChild(style);
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
        // Only add generator to specific "new password" fields
        const validFields = ['newPassword', 'newPasswordChange'];
        if (!validFields.includes(this.input.id)) return;

        // Create generator link if not already present
        const label = this.input.closest('.mb-3')?.querySelector('label');
        if (label && !label.querySelector('.generate-password-link')) {
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'float-end small text-decoration-none fw-bold generate-password-link';
            link.innerHTML = '<i class="bi bi-magic me-1"></i>Generate Password';
            link.style.color = 'var(--bs-primary)';

            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.generatePassword();
            });

            label.appendChild(link);
        }
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

    createStrengthMeter() {
        // Remove existing if any (prevent duplicates)
        const existing = document.getElementById(this.containerId);
        if (existing) existing.remove();

        const container = document.createElement('div');
        container.id = this.containerId;
        container.className = 'password-strength-container mt-3 mb-3 p-3 bg-light rounded-3 border';
        container.innerHTML = `
            <label class="form-label fw-bold text-muted small text-uppercase mb-2">Password Strength</label>
            <div class="password-strength-bar mb-3">
                <div class="password-strength-progress" style="width: 0%; transition: all 0.3s;"></div>
            </div>
            <div class="password-requirements">
                <small class="text-muted d-block mb-2 fw-bold">Policy Requirements:</small>
                <div class="requirements-list"></div>
            </div>
            <small class="password-strength-text text-muted mt-2 d-block fw-bold"></small>
        `;

        // Insert after the input group (parent of input)
        // Find the input's container (usually .mb-3 or .position-relative) and append after it
        let targetParent = this.input.closest('.mb-3');
        if (!targetParent) targetParent = this.input.parentElement;

        targetParent.appendChild(container);

        // Add CSS if not already present
        if (!document.getElementById('password-validator-styles')) {
            const style = document.createElement('style');
            style.id = 'password-validator-styles';
            style.textContent = `
                .password-strength-bar {
                    height: 6px;
                    background-color: #e9ecef;
                    border-radius: 3px;
                    overflow: hidden;
                }
                .password-strength-progress {
                    height: 100%;
                    background-color: #dc3545;
                    transition: all 0.3s ease;
                }
                .password-strength-progress.weak {
                    background-color: #dc3545;
                }
                .password-strength-progress.medium {
                    background-color: #ffc107;
                }
                .password-strength-progress.strong {
                    background-color: #198754;
                }
                .requirement-item {
                    font-size: 0.85rem;
                    padding: 3px 0;
                    color: #6c757d;
                }
                .requirement-item i {
                    width: 20px;
                    display: inline-block;
                }
                .requirement-met {
                    color: #198754;
                }
                .requirement-unmet {
                    color: #dc3545;
                }
            `;
            document.head.appendChild(style);
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
