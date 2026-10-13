<?php
require 'config.php';
require_role('admin');

$dir = __DIR__ . '/uploads/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
$maxSize = 2 * 1024 * 1024;
$msg = '';
$ok = true;

function save_poster($file, $dir, $allowed, $maxSize, &$error) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxSize) {
        $error = 'Poster must be 2 MB or smaller.';
        return false;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Poster upload failed.';
        return false;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        $error = 'Only JPG, PNG or GIF images are allowed.';
        return false;
    }
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        $error = 'Could not save the poster.';
        return false;
    }
    return $name;
}

function old_poster($conn, $id) {
    $stmt = $conn->prepare("SELECT poster FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row['poster'] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $title  = trim($_POST['title'] ?? '');
    $date   = $_POST['event_date'] ?? '';
    $venue  = trim($_POST['venue'] ?? '');

    if ($action === 'delete') {
        $old = old_poster($conn, $id);
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute() && $stmt->affected_rows === 1;
        $stmt->close();
        if ($ok && $old && is_file($dir . $old)) {
            unlink($dir . $old);
        }
        $msg = $ok ? 'Event deleted successfully.' : 'Failed to delete the event.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $date);

        if (strlen($title) < 2 || strlen($venue) < 2 || !$d || $d->format('Y-m-d') !== $date) {
            $ok = false;
            $msg = 'Please enter a valid title, date and venue.';
        } else {
            $err = '';
            $poster = save_poster($_FILES['poster'] ?? ['error' => UPLOAD_ERR_NO_FILE], $dir, $allowed, $maxSize, $err);

            if ($poster === false) {
                $ok = false;
                $msg = $err;
            } elseif ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO events (title, event_date, venue, poster) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $date, $venue, $poster);
                $ok = $stmt->execute();
                $stmt->close();
                $msg = $ok ? 'Event added successfully.' : 'Failed to add the event.';
                if (!$ok && $poster) {
                    unlink($dir . $poster);
                }
            } elseif ($action === 'update') {
                $old = old_poster($conn, $id);
                $stmt = $conn->prepare("UPDATE events SET title = ?, event_date = ?, venue = ?, poster = COALESCE(?, poster) WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $date, $venue, $poster, $id);
                $ok = $stmt->execute();
                $stmt->close();
                $msg = $ok ? 'Event updated successfully.' : 'Failed to update the event.';
                if ($ok && $poster && $old && is_file($dir . $old)) {
                    unlink($dir . $old);
                }
                if (!$ok && $poster) {
                    unlink($dir . $poster);
                }
            }
        }
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$rows = $conn->query("SELECT * FROM events ORDER BY event_date DESC")->fetch_all(MYSQLI_ASSOC);
$plain = 'display:inline;margin:0;padding:0;box-shadow:none;width:auto;background:none;';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - StudentHub Portal</title>
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

    <form method="post" action="events.php" enctype="multipart/form-data">
        <h3><?= $edit ? 'Edit Event' : 'Add Event' ?></h3>
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
        <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">

        <input type="text" name="title" placeholder="Event title" value="<?= htmlspecialchars($edit['title'] ?? '') ?>" required>
        <br><br>
        <input type="date" name="event_date" value="<?= htmlspecialchars($edit['event_date'] ?? '') ?>" required>
        <br><br>
        <input type="text" name="venue" placeholder="Venue" value="<?= htmlspecialchars($edit['venue'] ?? '') ?>" required>
        <br><br>

        <?php if (!empty($edit['poster'])): ?>
            <img src="uploads/<?= htmlspecialchars($edit['poster']) ?>" width="100" alt="Poster"><br>
            <small>Choose a new file only if you want to replace this poster.</small><br>
        <?php endif; ?>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.gif">
        <br><small>JPG, PNG or GIF, max 2 MB</small>
        <br><br>

        <button type="submit"><?= $edit ? 'Update' : 'Add' ?></button>
        <?php if ($edit): ?>
            <br><br><a href="events.php">Cancel</a>
        <?php endif; ?>
    </form>

    <div style="max-width:900px; width:100%; background:#fff; padding:20px; border-radius:20px; margin-bottom:30px;">
        <h3>Events (<?= count($rows) ?>)</h3>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th><th>Poster</th><th>Title</th><th>Date</th><th>Venue</th><th>Action</th>
            </tr>
            <?php if (!$rows): ?>
                <tr><td colspan="6">No events found.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td>
                        <?php if ($r['poster']): ?>
                            <img src="uploads/<?= htmlspecialchars($r['poster']) ?>" width="60" alt="Poster">
                        <?php else: ?>
                            No poster
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['event_date']) ?></td>
                    <td><?= htmlspecialchars($r['venue']) ?></td>
                    <td>
                        <a href="events.php?edit=<?= (int)$r['id'] ?>">Edit</a>
                        <form method="post" action="events.php" style="<?= $plain ?>" onsubmit="return confirm('Delete this event?');">
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