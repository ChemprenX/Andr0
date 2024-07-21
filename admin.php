<?php
session_start();
if (!isset($_SESSION['token'])) {
    header('Location: client.php');
    exit;
}

echo 'Admin Dashboard - Welcome, Admin!';
?>
