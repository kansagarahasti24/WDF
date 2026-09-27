<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>
<body>

<h2>Student Registration</h2>

<form method="POST">

    <label>Name:</label>
    <input type="text" name="name" required>
    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Course:</label>
    <input type="text" name="course" required>
    <br><br>

    <button type="submit" name="save">Save Data</button>

</form>

<?php

if (isset($_POST['save'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];

    $fileName = "students.csv";

    $file = fopen($fileName, "a");

    if (filesize($fileName) == 0) {
        fputcsv($file, ["Name", "Email", "Course"]);
    }

    fputcsv($file, [$name, $email, $course]);

    fclose($file);

    echo "<h3>Data saved successfully!</h3>";
}

?>
</body>
</html>