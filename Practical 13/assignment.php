<?php
require 'config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment - StudentHub Portal</title>
</head>
<body>
    <header>
        <h1>STUDENTHUB PORTAL</h1>
    </header>
    <?php include 'nav.php'; ?>
    <pre>
            <h4>Assignment Details :</h4>
            Assignment Done :    <input type="text" name="assignment_done" id="assignment_done">
            
            Assignment Pending : <input type="text" name="assignment_pending" id="assignment_pending">
        </pre>
</body>
</html>