<?php
require 'config.php';
require 'audit.php';

if (isset($_SESSION['user_id'])) {
    log_action($conn, 'logout', 'User logged out');
}

logout_user($conn);
header('Location: login.html?msg=loggedout');
exit;