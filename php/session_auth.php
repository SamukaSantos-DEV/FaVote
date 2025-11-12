<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    session_unset();
    session_destroy();
    
    header("Location: ../login.php?error=not_logged_in");
    exit(); 
}
?>