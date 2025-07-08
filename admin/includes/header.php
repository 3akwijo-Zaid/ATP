<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../public/login.php');
    exit();
}
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#333">
    <title>Admin Panel</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/img/admin.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/img/admin.png">
    <link rel="shortcut icon" href="../public/assets/img/admin.png">
    <link rel="apple-touch-icon" href="../public/assets/img/admin.png">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="admin-navbar">
            <a href="dashboard.php" class="logo">Admin Panel</a>
            <button class="hamburger" id="sidebar-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-list">
                <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class="active"' : ''; ?>>Users</a></li>
                <li><a href="players.php" <?php echo basename($_SERVER['PHP_SELF']) == 'players.php' ? 'class="active"' : ''; ?>>Players</a></li>
                <li><a href="tournaments.php" <?php echo basename($_SERVER['PHP_SELF']) == 'tournaments.php' ? 'class="active"' : ''; ?>>Tournaments</a></li>
                <li><a href="matches.php" <?php echo basename($_SERVER['PHP_SELF']) == 'matches.php' ? 'class="active"' : ''; ?>>Matches</a></li>
                <li><a href="recalculate_points.php" <?php echo basename($_SERVER['PHP_SELF']) == 'recalculate_points.php' ? 'class="active"' : ''; ?>>Recalculate Points</a></li>
                <li><a href="unified_predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'unified_predictions.php' ? 'class="active"' : ''; ?>>Predictions</a></li>
                <li><a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : ''; ?>>Settings</a></li>
                <li><a href="../public/index.php" class="main-site-link">Main Site</a></li>
                <li><a href="logout.php" id="logout-btn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Sidebar for Mobile -->
    <aside class="sidebar" id="sidebar" aria-label="Admin navigation menu">
        <div class="sidebar-header flex justify-between items-center">
            <a href="dashboard.php" class="logo">Admin Panel</a>
            <button class="sidebar-close" id="sidebar-close" aria-label="Close navigation menu">&times;</button>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class=\"active\"' : ''; ?>>Dashboard</a></li>
                <li><a href="users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class=\"active\"' : ''; ?>>Users</a></li>
                <li><a href="players.php" <?php echo basename($_SERVER['PHP_SELF']) == 'players.php' ? 'class=\"active\"' : ''; ?>>Players</a></li>
                <li><a href="tournaments.php" <?php echo basename($_SERVER['PHP_SELF']) == 'tournaments.php' ? 'class=\"active\"' : ''; ?>>Tournaments</a></li>
                <li><a href="matches.php" <?php echo basename($_SERVER['PHP_SELF']) == 'matches.php' ? 'class=\"active\"' : ''; ?>>Matches</a></li>
                <li><a href="recalculate_points.php" <?php echo basename($_SERVER['PHP_SELF']) == 'recalculate_points.php' ? 'class=\"active\"' : ''; ?>>Recalculate Points</a></li>
                <li><a href="unified_predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'unified_predictions.php' ? 'class=\"active\"' : ''; ?>>Predictions</a></li>
                <li><a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class=\"active\"' : ''; ?>>Settings</a></li>
                <li><a href="../public/index.php" class="main-site-link">Main Site</a></li>
                <li><a href="logout.php" id="logout-btn">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>

    <main>
        <div class="container container--full">
<script src="assets/js/main.js"></script>