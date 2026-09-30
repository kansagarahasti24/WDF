<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', $phone)) {
        exit('Enter a valid email and 10-digit phone number.');
    }

    $file = fopen(__DIR__ . '/register_records.csv', 'a');
    if ($file === false) {
        exit('Unable to save registration.');
    }

    fputcsv($file, [$email, $phone]);
    fclose($file);

    echo 'Registration saved. <a href="register.html">Return to registration</a>';
} else {
    echo 'Please submit the registration form.';
}
?>
