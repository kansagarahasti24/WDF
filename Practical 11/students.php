<?php
require 'config.php';
require_role(['admin', 'teacher']);

$courses = ['BCA', 'MCA', 'B.Tech', 'B.Sc IT'];
$msg = '';
$ok = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $course = $_POST['course'] ?? '';
    $sem    = (int)($_POST['semester'] ?? 0);

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute() && $stmt->affected_rows === 1;
        $msg = $ok ? 'Student deleted successfully.' : 'Failed to delete the student.';
        $stmt->close();
    } elseif (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || !in_array($course, $courses, true) || $sem < 1 || $sem > 8) {
        $ok = false;
        $msg = 'Please enter a valid name, email, course and semester (1-8).';
    } elseif ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO students (name, email, course, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $course, $sem);
        $ok = $stmt->execute();
        $msg = $ok ? 'Student added successfully.' : ($conn->errno === 1062 ? 'This email already exists.' : 'Failed to add the student.');
        $stmt->close();
    } elseif ($action === 'update') {
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, semester = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $email, $course, $sem, $id);
        $ok = $stmt->execute();
        $msg = $ok ? 'Student updated successfully.' : ($conn->errno === 1062 ? 'This email already exists.' : 'Failed to update the student.');
        $stmt->close();
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$search = trim($_GET['search'] ?? '');
$filter = $_GET['course'] ?? '';

$sql = "SELECT * FROM students WHERE (name LIKE ? OR email LIKE ?)";
$types = "ss";
$like = "%$search%";
$params = [$like, $like];

if (in_array($filter, $courses, true)) {
    $sql .= " AND course = ?";
    $types .= "s";
    $params[] = $filter;
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$plain = 'display:inline;margin:0;padding:0;box-shadow:none;width:auto;background:none;';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - StudentHub Portal</title>
</head>
<body>
<center>
    <header>
        <h1>STUDENTHUB PORTAL</h1>
    </header>
    <?php include 'nav.php'; ?>

    <?php if ($msg !== ''): ?>
        <p style="color:<?= $ok ? 'green' : 'crimson' ?>; font-weight:bold; margin-top:20px;">
            <?= htmlspecialchars($msg) ?>
        </p>
    <?php endif; ?>

    <form method="post" action="students.php">
        <h3><?= $edit ? 'Edit Student' : 'Add Student' ?></h3>
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
        <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">

        <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($edit['name'] ?? '') ?>" required>
        <br><br>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>" required>
        <br><br>

        <select name="course" required>
            <option value="">Select course</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= $c ?>" <?= ($edit['course'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="semester" min="1" max="8" placeholder="Semester" value="<?= htmlspecialchars($edit['semester'] ?? '') ?>" required>
        <br><br>

        <button type="submit"><?= $edit ? 'Update' : 'Add' ?></button>
        <?php if ($edit): ?>
            <br><br><a href="students.php">Cancel</a>
        <?php endif; ?>
    </form>

    <form method="get" action="students.php">
        <h3>Search / Filter</h3>
        <input type="search" name="search" placeholder="Search name or email" value="<?= htmlspecialchars($search) ?>">
        <select name="course">
            <option value="">All Courses</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= $c ?>" <?= $filter === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Search</button>
    </form>

    <div style="max-width:900px; width:100%; background:#fff; padding:20px; border-radius:20px;">
        <h3>Students (<?= count($rows) ?>)</h3>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Sem</th><th>Action</th>
            </tr>
            <?php if (!$rows): ?>
                <tr><td colspan="6">No students found.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['course']) ?></td>
                    <td><?= (int)$r['semester'] ?></td>
                    <td>
                        <a href="students.php?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form method="post" action="students.php" style="<?= $plain ?>" onsubmit="return confirm('Delete this student?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button type="submit" style="width:auto; padding:4px 12px; font-size:14px;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</center>
</body>
</html>