<?php
function log_action($conn, $action, $details = '') {
    $uid = $_SESSION['user_id'] ?? null;
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, details, ip) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $uid, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}