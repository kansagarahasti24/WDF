const emailInput = document.getElementById('id');
const phoneInput = document.getElementById('phone');
const submitBtn = document.getElementById('btn');
const form = document.querySelector('form');

const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phonePattern = /^[0-9]{10}$/;

submitBtn.addEventListener('click', function(event) {
    event.preventDefault();
    validateForm();
});

function validateForm() {
    const email = emailInput.value.trim();
    const phone = phoneInput.value.trim();

    clearErrors();

    let isValid = true;

    if (email === '') {
        showError(emailInput, '*Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError(emailInput, '*Please enter a valid email address');
        isValid = false;
    }

    if (phone === '') {
        showError(phoneInput, '*Phone number is required');
        isValid = false;
    } else if (!phonePattern.test(phone)) {
        showError(phoneInput, '*Phone number must be 10 digits');
        isValid = false;
    }

    if (isValid) {
        submitForm(email, phone);
    }
}

function showError(input, message) {
   
    const errorMsg = document.createElement('div');
    errorMsg.className = 'error-message';
    errorMsg.style.color = 'rgb(195, 100, 100)';
    errorMsg.style.fontSize = '12px';
    errorMsg.textContent = message;
    
    input.parentNode.insertBefore(errorMsg, input.nextSibling);
}

function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    emailInput.style.borderColor = '';
    emailInput.style.backgroundColor = '';
    phoneInput.style.borderColor = '';
    phoneInput.style.backgroundColor = '';
}

function submitForm(email, phone) {
    const userData = {
        email: email,
        phone: phone,
        registeredAt: new Date().toLocaleString()
    };
    
    localStorage.setItem('userRegistration', JSON.stringify(userData));
    
    alert('Account created successfully!\nEmail: ' + email + '\nPhone: ' + phone);

    form.reset();
    clearErrors();
}

emailInput.addEventListener('blur', function() {
    if (this.value !== '' && !emailPattern.test(this.value)) {
        showError(this, 'Invalid email format');
    } else {
        clearErrors();
    }
});

phoneInput.addEventListener('blur', function() {
    if (this.value !== '' && !phonePattern.test(this.value)) {
        showError(this, 'Phone number must be 10 digits');
    } else {
        clearErrors();
    }
});

emailInput.addEventListener('focus', function() {
    this.style.borderColor = '';
    this.style.backgroundColor = '';
});

phoneInput.addEventListener('focus', function() {
    this.style.borderColor = '';
    this.style.backgroundColor = '';
});
