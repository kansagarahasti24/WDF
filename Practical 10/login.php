<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email    = trim($_POST['id'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

$stmt = $conn->prepare("SELECT id, name, password, role, failed_attempts, locked_until FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
    header('Location: login.html?error=locked');
    exit;
}

if ($user && password_verify($password, $user['password'])) {

    $reset = $conn->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
    $reset->bind_param("i", $user['id']);
    $reset->execute();
    $reset->close();

    login_user($user['id'], $user['name'], $user['role']);

    if ($remember) {
        create_remember_token($conn, $user['id']);
    }

    header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'profile.php'));
    exit;
}

if ($user) {
    $attempts = $user['failed_attempts'] + 1;
    $lockUntil = null;
    if ($attempts >= MAX_ATTEMPTS) {
        $lockUntil = date('Y-m-d H:i:s', time() + LOCK_MINUTES * 60);
        $attempts = 0;
    }
    $upd = $conn->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
    $upd->bind_param("isi", $attempts, $lockUntil, $user['id']);
    $upd->execute();
    $upd->close();
}

header('Location: login.html?error=invalid');
exit;