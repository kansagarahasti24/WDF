var str1= /^[A-Za-z]+$/;
var str2= /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/;
var str3= /^[0-9]{10}$/;
var str4= /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
var str5= /^[A-Za-z0-9]+$/;

function checkInput(){
    var name = document.getElementById("name");
    var dob = document.getElementById("dob");
    var phone = document.getElementById("phone");
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var address = document.getElementById("address");
    var count=0;
    if(!str1.test(name.value)){
        document.getElementById('s1').innerHTML = "*Invalid Name";
        name.focus();
        return false;
    }else{
        document.getElementById('s1').innerHTML = "";
    }
    if(!str2.test(dob.value)){
        document.getElementById('s2').innerHTML = "*Invalid Date of Birth";
        dob.focus();
        return false;
    } else{
        document.getElementById('s2').innerHTML = "";
    }
    if(!str3.test(phone.value)){
        document.getElementById('s3').innerHTML = "*Invalid Phone Number";
        phone.focus();
        return false;
    } else{
        document.getElementById('s3').innerHTML = "";
    }
    if(!str4.test(email.value)){
        document.getElementById('s4').innerHTML = "*Invalid Email Address";
        email.focus();
        return false;
    } else{
        document.getElementById('s4').innerHTML = "";
    }
    if(!str5.test(password.value)){
        document.getElementById('s5').innerHTML = "*Invalid Password";
        password.focus();
        return false;
    } else{
        document.getElementById('s5').innerHTML = "";
    }
    var gender = document.getElementsByName("g");
    if (!gender[0].checked && !gender[1].checked) {
        document.getElementById("s7").innerHTML = "*Please select your gender";
        gender[0].focus();
        return false;
    } else {
        document.getElementById("s7").innerHTML = "";
    }
    var departments = document.querySelectorAll('input[name="department"]:checked');
    if (departments.length === 0) {
        document.getElementById("s8").innerHTML = "*Please select at least one department";
        document.querySelector('input[name="department"]').focus();
        return false;
    } else {
        document.getElementById("s8").innerHTML = "";
    }
    var course = document.getElementById("course");
    if (course.selectedIndex === 0) {
        document.getElementById("s9").innerHTML = "*Please select a course";
        course.focus();
        return false;
    } else {
        document.getElementById("s9").innerHTML = "";
    }
    var city = document.getElementById("city");
    if (city.value.trim() === "") {
        document.getElementById("s10").innerHTML = "*City cannot be empty";
        city.focus();
        return false;
    } else {
        document.getElementById("s10").innerHTML = "";
    }
     if(address.value.trim() === ""){
        document.getElementById('s6').innerHTML = "*Address cannot be empty";
        address.focus();
        return false;
    } else{
        document.getElementById('s6').innerHTML = "";
         count++;
    }
    if(count==1){
        alert("Registered Successfully!");
    }
    return true;
}