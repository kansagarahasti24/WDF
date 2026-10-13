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
    <title>Courses - StudentHub Portal</title>
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
                <td>
                    <div class="course-toolbar">
                        <h3>Courses: </h3>
                        <input type="search" id="courseSearch" placeholder="Search courses">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="courseList"></div>
                </td>
            </tr>
        </table>
    </form>
    </center>
    <script src="courses.js"></script>
</body>
</html>