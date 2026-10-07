<?php

$conn = new mysqli("localhost", "root", "", "login");

if ($conn->connect_error) {
    die("Connection failed");
}

$username = $_POST["id"] ?? "";
$password = $_POST["password"] ?? "";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {

    echo "<script>
            alert('Your data is successfully stored');
            window.location='login.html';
          </script>";

} else {

    echo "Error: " . $stmt->error;

}

$stmt->close();
$conn->close();

?>