const form = document.querySelector('form');
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phonePattern = /^[0-9]{10}$/;

form.addEventListener('submit', function (event) {
    const role = document.querySelector('input[name="role"]:checked');
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('id').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm').value;
    let error = '';

    if (!role) {
        error = '*Please select a role';
    } else if (name.length < 2) {
        error = '*Please enter your name';
    } else if (!emailPattern.test(email)) {
        error = '*Please enter a valid email address';
    } else if (!phonePattern.test(phone)) {
        error = '*Phone number must be 10 digits';
    } else if (password.length < 8) {
        error = '*Password must be at least 8 characters';
    } else if (password !== confirm) {
        error = '*Passwords do not match';
    }

    if (error) {
        event.preventDefault();
        document.getElementById('msg').textContent = error;
    }
});

const params = new URLSearchParams(window.location.search);
const serverErrors = {
    exists: '*This email is already registered',
    invalid: '*Invalid details, please check and try again',
    failed: '*Something went wrong, try again'
};
if (serverErrors[params.get('error')]) {
    document.getElementById('msg').textContent = serverErrors[params.get('error')];
}