<?php

$conn = new mysqli("localhost", "root", "", "login");

if ($conn->connect_error) {
    die("Connection failed");
}

$username = $_POST["id"];
$password = $_POST["password"];

$sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$password')";

if ($conn->query($sql)) {

    echo "<script>
            alert('Your data is successfully stored');
            window.location='login.html';
          </script>";

} else {

    echo "Error: " . $conn->error;

}

$conn->close();

?>