<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: ./login.php");
    exit;
}

function checkAdminAccess() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ./dashboard.php");
        exit;
    }
}
?>