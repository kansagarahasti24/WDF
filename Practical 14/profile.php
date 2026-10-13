<?php
require 'config.php';
require_login();

$stmt = $conn->prepare("SELECT name, email, phone, role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - StudentHub Portal</title>
</head>
<body>
    <center>
        <header>
            <h1>STUDENTHUB PORTAL</h1>
        </header>
        <?php include 'nav.php'; ?>
    </center>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h2>Profile Details</h2>
                <p>Your Account Information</p>
            </div>

            <div class="profile-item">
                <span class="profile-label">Name</span>
                <span class="profile-value"><?= htmlspecialchars($u['name']) ?></span>
            </div>

            <div class="profile-item">
                <span class="profile-label">Email</span>
                <span class="profile-value"><?= htmlspecialchars($u['email']) ?></span>
            </div>

            <div class="profile-item">
                <span class="profile-label">Phone</span>
                <span class="profile-value"><?= htmlspecialchars($u['phone']) ?></span>
            </div>

            <div class="profile-item">
                <span class="profile-label">Role</span>
                <span class="profile-value"><?= htmlspecialchars(ucfirst($u['role'])) ?></span>
            </div>
        </div>
    </div>
</body>
</html>