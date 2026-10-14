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
    <title>Attendance - StudentHub Portal</title>
</head>
<body>
    <center>
    <header>
        <h1>STUDENTHUB PORTAL</h1>
    </header>
    <?php include 'nav.php'; ?>
    <form>
        <table>
            <tr>
                <td align="left"><h2>Attendance of semester</h2></td>
            </tr>
            <tr>
                <td>Subject 1 - Data Structures - 80%</td>
            </tr>
            <tr>
                <td>Subject 2 - Operating Systems - 75%</td>
            </tr>
            <tr>
                <td>Subject 3 - Computer Networks - 90%</td>
            </tr>
            <tr>
                <td>Subject 4 - Database Management Systems - 85%</td>
            </tr>
            <tr>
                <td>Subject 5 - Software Engineering - 70%</td>
            </tr>
            <tr>
                <td><h3>Average Attendance : 80%</h3></td>
            </tr>
        </table>
    </form>
    </center>
</body>
</html>