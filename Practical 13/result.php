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
    <title>Result - StudentHub Portal</title>
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
                <td align="left"><h2> Result:- </h2></td>
            </tr>
            <tr>
                <td>Result of semester :</td>
            </tr>
            <tr>
                <td>Subject 1 - Data Structures - A</td>
            </tr>
            <tr>
                <td>Subject 2 - Operating Systems - B</td>
            </tr>
            <tr>
                <td>Subject 3 - Computer Networks - A</td>
            </tr>
            <tr>
                <td>Subject 4 - Database Management Systems - B</td>
            </tr>
            <tr>
                <td>Subject 5 - Software Engineering - A</td>
            </tr>
            <tr>
                <td><h3>Average Result : A</h3></td>
            </tr>
        </table>
    </form>
    </center>
</body>
</html>