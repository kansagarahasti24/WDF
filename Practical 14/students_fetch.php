<?php
require 'config.php';
require_role(['admin', 'teacher']);
$courses = ['BCA', 'MCA', 'B.Tech', 'B.Sc IT'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students (AJAX) - StudentHub Portal</title>
</head>
<body>
<center>
    <header>
        <h1>STUDENTHUB PORTAL</h1>
    </header>
    <?php include 'nav.php'; ?>

    <p id="msg" style="font-weight:bold; margin-top:20px;"></p>

    <form id="studentForm">
        <h3 id="formTitle">Add Student</h3>
        <input type="hidden" id="id" value="0">
        <input type="text" id="name" placeholder="Name" required>
        <br><br>
        <input type="email" id="email" placeholder="Email" required>
        <br><br>
        <select id="course" required>
            <option value="">Select course</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= $c ?>"><?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="semester" min="1" max="8" placeholder="Semester" required>
        <br><br>
        <button type="submit" id="saveBtn">Add</button>
        <br><br>
        <a href="#" id="cancelLink" style="display:none;">Cancel</a>
    </form>

    <div style="max-width:900px; width:100%; background:#fff; padding:20px; border-radius:20px; margin-bottom:30px;">
        <input type="search" id="search" placeholder="Search name or email" style="margin-bottom:15px;">
        <h3>Students (<span id="count">0</span>)</h3>
        <table border="1" cellpadding="8">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Sem</th><th>Action</th></tr>
            </thead>
            <tbody id="rows"></tbody>
        </table>
    </div>
</center>

<script>
const api = 'api_students.php';
const $ = id => document.getElementById(id);

function show(text, ok) {
    $('msg').textContent = text;
    $('msg').style.color = ok ? 'green' : 'crimson';
}

async function call(action, method = 'GET', body = null, query = '') {
    try {
        const res = await fetch(api + '?action=' + action + query, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: body ? JSON.stringify(body) : null
        });
        return await res.json();
    } catch (e) {
        return { success: false, message: 'Request failed. Your session may have expired, please log in again.' };
    }
}

async function load() {
    const data = await call('list', 'GET', null, '&search=' + encodeURIComponent($('search').value));
    if (!data.success) return show(data.message, false);

    const tbody = $('rows');
    tbody.innerHTML = '';
    $('count').textContent = data.students.length;

    if (data.students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">No students found.</td></tr>';
        return;
    }

    data.students.forEach(s => {
        const tr = tbody.insertRow();
        [s.id, s.name, s.email, s.course, s.semester].forEach(v => tr.insertCell().textContent = v);

        const td = tr.insertCell();
        const editBtn = document.createElement('button');
        editBtn.textContent = 'Edit';
        editBtn.style.cssText = 'width:auto; padding:4px 12px; font-size:14px; margin-right:6px;';
        editBtn.onclick = () => startEdit(s);

        const delBtn = document.createElement('button');
        delBtn.textContent = 'Delete';
        delBtn.style.cssText = 'width:auto; padding:4px 12px; font-size:14px;';
        delBtn.onclick = () => remove(s.id);

        td.append(editBtn, delBtn);
    });
}

function startEdit(s) {
    $('id').value = s.id;
    $('name').value = s.name;
    $('email').value = s.email;
    $('course').value = s.course;
    $('semester').value = s.semester;
    $('formTitle').textContent = 'Edit Student';
    $('saveBtn').textContent = 'Update';
    $('cancelLink').style.display = 'inline';
    window.scrollTo(0, 0);
}

function resetForm() {
    $('studentForm').reset();
    $('id').value = 0;
    $('formTitle').textContent = 'Add Student';
    $('saveBtn').textContent = 'Add';
    $('cancelLink').style.display = 'none';
}

async function remove(id) {
    if (!confirm('Delete this student?')) return;
    const data = await call('delete', 'POST', { id: id });
    show(data.message, data.success);
    load();
}

$('studentForm').addEventListener('submit', async e => {
    e.preventDefault();
    const id = parseInt($('id').value);
    const data = await call(id > 0 ? 'update' : 'add', 'POST', {
        id: id,
        name: $('name').value,
        email: $('email').value,
        course: $('course').value,
        semester: $('semester').value
    });
    show(data.message, data.success);
    if (data.success) {
        resetForm();
        load();
    }
});

$('cancelLink').addEventListener('click', e => {
    e.preventDefault();
    resetForm();
});

$('search').addEventListener('input', load);

load();
</script>
</body>
</html>