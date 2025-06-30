<?php
session_start();
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f2027">
    <meta name="description" content="Predict tennis match outcomes and compete on the scoreboard">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Tennis Predictions">
    <link rel="manifest" href="../public/manifest.json">
    <link rel="apple-touch-icon" href="assets/img/icon-192.png">
    <title>Tennis Predictions</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Tennis Predictions</a>
            <ul>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'predictions.php' ? 'class="active"' : ''; ?>>Predictions</a></li>
                <li><a href="fixtures.php" <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'class="active"' : ''; ?>>Fixtures</a></li>
                <li><a href="scoreboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'scoreboard.php' ? 'class="active"' : ''; ?>>Scoreboard</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active"' : ''; ?>>Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>>Login</a></li>
                    <li><a href="register.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active"' : ''; ?>>Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <!-- Hamburger Icon for Mobile -->
    <button class="hamburger" id="sidebar-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <!-- Sidebar for Mobile -->
    <aside class="sidebar" id="sidebar" aria-label="Navigation menu">
        <div class="sidebar-header flex justify-between items-center">
            <a href="index.php" class="logo">Tennis Predictions</a>
            <button class="sidebar-close" id="sidebar-close" aria-label="Close navigation menu">&times;</button>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="fixtures.php" <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'class="active"' : ''; ?>>Fixtures</a></li>
                <li><a href="predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'predictions.php' ? 'class="active"' : ''; ?>>Predictions</a></li>
                <li><a href="scoreboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'scoreboard.php' ? 'class="active"' : ''; ?>>Scoreboard</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active"' : ''; ?>>Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>>Login</a></li>
                    <li><a href="register.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active"' : ''; ?>>Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    
    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
    
    <main>
<script src="assets/js/main.js"></script>
</body>
</html> 