<?php
session_start();
require_once '../includes/db.php';

if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}
?>
