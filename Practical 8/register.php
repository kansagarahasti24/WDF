<?php

$conn = new mysqli("localhost", "root", "", "register");

if ($conn->connect_error) {
    die("Connection failed");
}

$email = $_POST["email"];
$phone = $_POST["phone"];

$sql = "INSERT INTO users (email, phone)
        VALUES ('$email', '$phone')";

if ($conn->query($sql)) {

    echo "<script>
            alert('Your data is successfully stored');
            window.location='register.html';
          </script>";

} else {

    echo "Error: " . $conn->error;

}

$conn->close();

?>