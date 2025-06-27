<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="dashboard.php" class="logo">Admin Panel</a>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="matches.php">Manage Matches</a></li>
                <li><a href="results.php">Update Results</a></li>
                <li><a href="settings.php">Point Settings</a></li>
                <li><a href="users.php">User Management</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container"> 