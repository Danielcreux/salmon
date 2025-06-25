<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';

redirectIfNotLoggedIn();
redirectBasedOnRole();
?>