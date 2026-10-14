<?php
require 'config.php';
mysqli_report(MYSQLI_REPORT_OFF);
header('Content-Type: application/json');

function out($ok, $msg, $extra = [], $code = 200) {
    http_response_code($code);
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra));
    exit;
}

if (!isset($_SESSION['user_id'])) out(false, 'Please log in.', [], 401);
if (!in_array($_SESSION['role'], ['admin', 'teacher'], true)) out(false, 'Access denied.', [], 403);

$courses = ['BCA', 'MCA', 'B.Tech', 'B.Sc IT'];
$action  = $_GET['action'] ?? '';
$in      = json_decode(file_get_contents('php://input'), true) ?? [];

if ($action === 'list') {
    $like = '%' . trim($_GET['search'] ?? '') . '%';
    $stmt = $conn->prepare("SELECT id, name, email, course, semester FROM students WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    out(true, 'OK', ['students' => $rows]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'POST method required.', [], 405);

$id     = (int)($in['id'] ?? 0);
$name   = trim($in['name'] ?? '');
$email  = trim($in['email'] ?? '');
$course = $in['course'] ?? '';
$sem    = (int)($in['semester'] ?? 0);

if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute() && $stmt->affected_rows === 1;
    out($ok, $ok ? 'Student deleted successfully.' : 'Failed to delete the student.');
}

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !in_array($course, $courses, true) || $sem < 1 || $sem > 8) {
    out(false, 'Enter a valid name, email, course and semester (1-8).', [], 422);
}

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO students (name, email, course, semester) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $course, $sem);
    $ok = $stmt->execute();
    out($ok, $ok ? 'Student added successfully.' : ($conn->errno === 1062 ? 'This email already exists.' : 'Failed to add the student.'));
}

if ($action === 'update') {
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, semester = ? WHERE id = ?");
    $stmt->bind_param("sssii", $name, $email, $course, $sem, $id);
    $ok = $stmt->execute();
    out($ok, $ok ? 'Student updated successfully.' : ($conn->errno === 1062 ? 'This email already exists.' : 'Failed to update the student.'));
}

out(false, 'Unknown action.', [], 400);