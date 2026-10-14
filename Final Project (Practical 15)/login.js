var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function validateForm() {
    var id = document.getElementById("id");
    var password = document.getElementById("password");
    var role = document.querySelector('input[name="role"]:checked');

    if (!role) {
        document.getElementById('msg').textContent = "Please select a role.";
        return false;
    }

    if (!emailPattern.test(id.value.trim())) {
        document.getElementById('s').innerHTML = "*Invalid email";
        id.focus();
        return false;
    }
    document.getElementById('s').innerHTML = "";

    if (password.value.length < 6) {
        document.getElementById('s1').innerHTML = "*Password must be at least 6 characters";
        password.focus();
        return false;
    }
    document.getElementById('s1').innerHTML = "";
    return true;
}

function togglePassword() {
    var password = document.getElementById("password");
    var showPassword = document.getElementById("showPassword");
    if (password && showPassword) {
        password.type = showPassword.checked ? "text" : "password";
    }
}

window.onload = function () {
    var showPassword = document.getElementById("showPassword");
    if (showPassword) {
        showPassword.addEventListener("click", togglePassword);
    }

    var params = new URLSearchParams(window.location.search);
    var messages = {
        invalid: "Invalid email or password.",
        role: "Your account does not have the selected role. Choose the correct role and try again.",
        locked: "Too many failed attempts. Try again in a few minutes.",
        timeout: "Your session expired due to inactivity. Please log in again.",
        required: "Please log in to access that page.",
        loggedout: "You have been logged out.",
        registered: "Account created. Please log in."
    };
    var key = params.get("error") || params.get("msg");
    var box = document.getElementById("msg");
    if (key && messages[key] && box) {
        box.textContent = messages[key];
    }
};