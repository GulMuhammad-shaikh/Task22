document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const captchaNum1 = document.getElementById('num1');
    const captchaNum2 = document.getElementById('num2');
    const num1Hidden = document.getElementById('num1-hidden');
    const num2Hidden = document.getElementById('num2-hidden');
    
    // Generate random CAPTCHA numbers
    function generateCaptcha() {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        captchaNum1.textContent = num1;
        captchaNum2.textContent = num2;
        num1Hidden.value = num1;
        num2Hidden.value = num2;
        return num1 + num2;
    }
    
    let captchaAnswer = generateCaptcha();
    
    // Validation rules
    const validationRules = {
        name: {
            validate: (value) => value.trim().length >= 3,
            message: 'Name must be at least 3 characters'
        },
        email: {
            validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            message: 'Please enter a valid email address'
        },
        subject: {
            validate: (value) => value.trim().length > 0,
            message: 'Subject is required'
        },
        message: {
            validate: (value) => value.trim().length >= 10,
            message: 'Message must be at least 10 characters'
        },
        captcha: {
            validate: (value) => parseInt(value) === captchaAnswer,
            message: 'Incorrect answer'
        }
    };
    
    // Real-time validation
    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        subject: document.getElementById('subject'),
        message: document.getElementById('message'),
        captcha: document.getElementById('captcha')
    };
    
    Object.keys(fields).forEach(fieldName => {
        const field = fields[fieldName];
        const errorElement = document.getElementById(`${fieldName}-error`);
        const successElement = document.getElementById(`${fieldName}-success`);
        
        // Validate on input
        field.addEventListener('input', function() {
            validateField(fieldName, field.value);
        });
        
        // Validate on blur
        field.addEventListener('blur', function() {
            if (field.value) {
                validateField(fieldName, field.value);
            }
        });
    });
    
    function validateField(fieldName, value) {
        const rule = validationRules[fieldName];
        const field = fields[fieldName];
        const errorElement = document.getElementById(`${fieldName}-error`);
        const successElement = document.getElementById(`${fieldName}-success`);
        const inputWrapper = field.closest('.input-wrapper');
        
        if (rule.validate(value)) {
            // Valid field
            field.classList.remove('invalid');
            field.classList.add('valid');
            inputWrapper.classList.add('valid');
            inputWrapper.classList.remove('invalid');
            errorElement.style.display = 'none';
            successElement.style.display = 'block';
        } else {
            // Invalid field
            field.classList.remove('valid');
            field.classList.add('invalid');
            inputWrapper.classList.remove('valid');
            inputWrapper.classList.add('invalid');
            errorElement.textContent = rule.message;
            errorElement.style.display = 'block';
            successElement.style.display = 'none';
        }
        
        updateSubmitButton();
    }
    
    function updateSubmitButton() {
        const allValid = Object.values(fields).every(field => 
            field.classList.contains('valid') && field.value
        );
        submitBtn.disabled = !allValid;
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check honeypot
        const honeypot = document.getElementById('website');
        if (honeypot.value) {
            showNotification('Bot detected. Submission rejected.', 'error');
            return;
        }
        
        // Validate all fields one more time
        let isFormValid = true;
        Object.keys(fields).forEach(fieldName => {
            const field = fields[fieldName];
            if (!field.classList.contains('valid') || !field.value) {
                validateField(fieldName, field.value);
                if (!field.classList.contains('valid')) {
                    isFormValid = false;
                }
            }
        });
        
        if (!isFormValid) {
            showNotification('Please fix all errors before submitting.', 'error');
            return;
        }
        
        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Submit form
        const formData = new FormData(form);
        formData.append('num1', num1Hidden.value);
        formData.append('num2', num2Hidden.value);
        
        fetch('php/submit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            submitBtn.classList.remove('loading');
            
            if (data === 'success') {
                showNotification('Your message has been sent successfully!', 'success');
                form.reset();
                generateCaptcha();
                
                // Reset field states
                Object.values(fields).forEach(field => {
                    field.classList.remove('valid', 'invalid');
                    field.closest('.input-wrapper').classList.remove('valid', 'invalid');
                    document.getElementById(`${field.id}-error`).style.display = 'none';
                    document.getElementById(`${field.id}-success`).style.display = 'none';
                });
                
                updateSubmitButton();
            } else {
                showNotification(data, 'error');
            }
        })
        .catch(error => {
            submitBtn.classList.remove('loading');
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        });
    });
    
    // Regenerate CAPTCHA on click
    document.querySelector('.captcha-question').addEventListener('click', function() {
        generateCaptcha();
        fields.captcha.value = '';
        fields.captcha.classList.remove('valid', 'invalid');
        fields.captcha.closest('.input-wrapper').classList.remove('valid', 'invalid');
        document.getElementById('captcha-error').style.display = 'none';
        document.getElementById('captcha-success').style.display = 'none';
        updateSubmitButton();
    });
});