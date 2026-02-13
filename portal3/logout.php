<?php
session_start();
session_destroy();
// Redirect to Admin Login (Since index.php is now on BISP domain)
header("Location: login.php");
exit();
?>