// DOM Elements
const loginForm = document.getElementById('loginForm');
const indexNumberInput = document.getElementById('indexNumber');
const passwordInput = document.getElementById('password');

// Frontend validation
function validateForm() {
    const indexNumber = indexNumberInput.value.trim();
    const password = passwordInput.value.trim();

    if (indexNumber === '' || password === '') {
        showError('Please fill in all fields.');
        return false;
    }

    const indexPattern = /^[A-Za-z0-9]+$/;
    if (!indexPattern.test(indexNumber)) {
        showError('Index number format is invalid.');
        return false;
    }

    return true;
}

// Show error
function showError(message) {
    const existingError = document.querySelector('.error-message');
    if (existingError) existingError.remove();

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = "color:red; margin-bottom:10px;";
    errorDiv.textContent = message;

    loginForm.insertBefore(errorDiv, loginForm.firstChild);

    setTimeout(() => { errorDiv.remove(); }, 5000);
}

// Input validation styling
[indexNumberInput, passwordInput].forEach(input => {
    input.addEventListener('input', () => input.style.borderColor = '#e5e7eb');
    input.addEventListener('focus', () => input.style.borderColor = '#6b46c1');
    input.addEventListener('blur', () => {
        if (input.value.trim() === '') input.style.borderColor = '#e5e7eb';
    });
});

// Form submit
loginForm.addEventListener('submit', function(e) {
    if (!validateForm()) e.preventDefault();
});

// Forgot password link
document.getElementById('forgotPasswordLink').addEventListener('click', e => {
    e.preventDefault();
    alert('Forgot password functionality would be implemented here.');
});
