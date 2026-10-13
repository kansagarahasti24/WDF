<?php
require 'config.php';
logout_user($conn);
header('Location: login.html?msg=loggedout');
exit;