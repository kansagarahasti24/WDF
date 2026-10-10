<nav>
    <a href="index.php">Home</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php">Profile</a>
        <a href="courses.php">Courses</a>
        <a href="attendance.php">Attendance</a>
        <a href="assignment.php">Assignment</a>
        <a href="result.php">Result</a>
        <a href="contact.php">Contact</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
    <?php else: ?>
        <a href="login.html">Login</a>
        <a href="register.html">Register</a>
    <?php endif; ?>
    <?php if (in_array($_SESSION['role'], ['admin', 'teacher'], true)): ?>
    <a href="students.php">Students</a>
    <a href="events.php">Events</a>
    <?php endif; ?>
</nav>