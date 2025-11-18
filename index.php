<?php
  session_start();
  require_once 'config.php';
  require_once 'auth_check.php';

  if (!isset($_SESSION['user_id'])) {
      header("Location: ./login.php");
      exit;
  }

  header("Location: ./dashboard.php");
  exit;
?>
