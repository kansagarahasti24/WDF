var str = /^[A-Za-z0-9]+$/;
var str1 = /^[0-9]+$/;

function validateForm(){
    var id = document.getElementById("id");
    var password = document.getElementById("password");
    var count = 0;
    if(!str.test(id.value)){
        document.getElementById('s').innerHTML = "*Invalid ID";
        id.focus();
        return false;
    } else {
        document.getElementById('s').innerHTML = "";
    }

    if(!str1.test(password.value)){
        document.getElementById('s1').innerHTML = "*Invalid Password";
        password.focus();
        return false;
    } else {
        document.getElementById('s1').innerHTML = "";
        count++;
    }
    if(count == 1){
        alert("Login Successful");
    }

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
};
