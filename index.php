<?php
// Start session and include necessary files
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in - redirect to login
    header("Location: ./login.php");
    exit;
}

// Check user role and redirect accordingly
if ($_SESSION['role'] === 'admin') {
    header("Location: ./inventory.php");
} else {
    header("Location: ./catalog.php");
}
exit;
?>