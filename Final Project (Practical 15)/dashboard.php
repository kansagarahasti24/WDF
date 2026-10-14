<?php
require 'config.php';
require 'audit.php';
require_role('admin');
mysqli_report(MYSQLI_REPORT_OFF);

function total($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? (int)$r->fetch_row()[0] : 0;
}

$stats = [
    'Total Users' => total($conn, "SELECT COUNT(*) FROM users"),
    'Admins'      => total($conn, "SELECT COUNT(*) FROM users WHERE role='admin'"),
    'Teachers'    => total($conn, "SELECT COUNT(*) FROM users WHERE role='teacher'"),
    'Students'    => total($conn, "SELECT COUNT(*) FROM users WHERE role='student'"),
    'Student Records' => total($conn, "SELECT COUNT(*) FROM students"),
    'Events'      => total($conn, "SELECT COUNT(*) FROM events")
];

$latest = $conn->query("SELECT name, email, role, created_at FROM users ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$logs = $conn->query("SELECT a.action, a.details, a.ip, a.created_at, u.name
                      FROM audit_log a LEFT JOIN users u ON u.id = a.user_id
                      ORDER BY a.id DESC LIMIT 10");
$logs = $logs ? $logs->fetch_all(MYSQLI_ASSOC) : [];

log_action($conn, 'view_dashboard', 'Opened admin dashboard');

$box = 'background:#fff; padding:20px; border-radius:20px; box-shadow:0 8px 18px rgba(0,0,0,.08); margin:20px auto; max-width:900px; width:100%;';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudentHub Portal</title>
</head>
<body>
<center>
    <header>
        <h1>STUDENTHUB PORTAL</h1>
    </header>
    <?php include 'nav.php'; ?>

    <div style="<?= $box ?>">
        <h2>Admin Dashboard</h2>
        <div style="display:flex; flex-wrap:wrap; gap:15px; justify-content:center; margin-top:15px;">
            <?php foreach ($stats as $label => $value): ?>
                <div style="background:#EAF6FF; border-radius:15px; padding:20px; min-width:130px;">
                    <div style="font-size:30px; font-weight:bold; color:#4A6FA5;"><?= $value ?></div>
                    <div><?= htmlspecialchars($label) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="<?= $box ?>">
        <h3>Latest Registrations</h3>
        <table border="1" cellpadding="8">
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Registered</th></tr>
            <?php foreach ($latest as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= htmlspecialchars($u['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="<?= $box ?>">
        <h3>Audit Log (last 10)</h3>
        <table border="1" cellpadding="8">
            <tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr>
            <?php if (!$logs): ?>
                <tr><td colspan="5">No activity yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= htmlspecialchars($l['created_at']) ?></td>
                    <td><?= htmlspecialchars($l['name'] ?? 'Guest') ?></td>
                    <td><?= htmlspecialchars($l['action']) ?></td>
                    <td><?= htmlspecialchars($l['details']) ?></td>
                    <td><?= htmlspecialchars($l['ip']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</center>
</body>
</html>