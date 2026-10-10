<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';
$role     = $_POST['role'] ?? '';

if (!in_array($role, ['student', 'teacher'], true)
    || strlen($name) < 2
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !preg_match('/^[0-9]{10}$/', $phone)
    || strlen($password) < 8
    || $password !== $confirm) {
    header('Location: register.html?error=invalid');
    exit;
}

$chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
$chk->bind_param("s", $email);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close();
    header('Location: register.html?error=exists');
    exit;
}
$chk->close();

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $hash, $role);

if ($stmt->execute()) {
    header('Location: login.html?msg=registered');
} else {
    header('Location: register.html?error=failed');
}
$stmt->close();
$conn->close();
exit;