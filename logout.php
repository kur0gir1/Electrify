<?php
session_start();

// Destroy the session and log out the user
session_unset();
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>
