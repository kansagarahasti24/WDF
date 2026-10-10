
<?php
require_once "auth.php";
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentHub Dashboard</title>
</head>
<body>
    <h1>Welcome to StudentHub</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8") ?>!
    </p>

    <p>Your role:
        <?= htmlspecialchars($_SESSION["role"], ENT_QUOTES, "UTF-8") ?>
    </p>

    <?php if ($_SESSION["role"] === "student"): ?>
        <a href="courses.php">View Courses</a>
    <?php endif; ?>

    <?php if ($_SESSION["role"] === "admin"): ?>
        <a href="admin.php">Admin Panel</a>
    <?php endif; ?>

    <br><br>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>