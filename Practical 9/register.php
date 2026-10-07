<?php

$conn = new mysqli("localhost", "root", "", "register");

if ($conn->connect_error) {
    die("Connection failed");
}

$email = $_POST["email"] ?? "";
$phone = $_POST["phone"] ?? "";

$stmt = $conn->prepare("INSERT INTO users (email, phone) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $phone);

if ($stmt->execute()) {

    echo "<script>
            alert('Your data is successfully stored');
            window.location='register.html';
          </script>";

} else {

    echo "Error: " . $stmt->error;

}

$stmt->close();
$conn->close();

?>