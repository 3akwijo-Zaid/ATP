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

    <!-- Basic Meta Tags -->
    <title>Tennis Jma3a | Your Tennis Hub</title>
    <meta name="description" content="Tennis Jma3a is your go-to platform for discovering tennis events, joining local clubs, and connecting with players in your area." />

    <!-- Open Graph Tags (for Facebook, WhatsApp, LinkedIn, etc.) -->
    <meta property="og:title" content="Tennis Jma3a | Your Tennis Hub" />
    <meta property="og:description" content="Discover tennis events, join clubs, and connect with tennis lovers near you with Tennis Jma3a." />
    <meta property="og:image" content="http://tennis-jma3a.site/public/assets/img/icon-192.png" />
    <meta property="og:url" content="http://tennis-jma3a.site/" />
    <meta property="og:type" content="website" />

    <!-- Twitter Card (for Twitter/X previews) -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Tennis Jma3a | Your Tennis Hub" />
    <meta name="twitter:description" content="Discover tennis events, join clubs, and connect with tennis lovers near you with Tennis Jma3a." />
    <meta name="twitter:image" content="http://tennis-jma3a.site/public/assets/img/icon-192.png" />

    <meta name="apple-mobile-web-app-title" content="Tennis Predictions">
    <link rel="manifest" href="manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icon-192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/icon-192.png">
    <link rel="shortcut icon" href="assets/img/icon-192.png">
    <link rel="apple-touch-icon" href="assets/img/icon-192.png">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

</head>
<body>
    <header>
        <nav class="navbar" aria-label="Main navigation">
            <a href="index.php" class="logo" aria-label="Tennis Predictions Home">
                <img src="assets/img/icon-192.png" class="logo-img">
                Tennis Jma3a
            </a>
            <button class="hamburger" id="sidebar-toggle" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-links">
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-house"></i></span> Home</a></li>
                <li><a href="predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'predictions.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span> Predictions</a></li>
                <li><a href="fixtures.php" <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-calendar-days"></i></span> Fixtures</a></li>
                <li><a href="scoreboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'scoreboard.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-trophy"></i></span> Scoreboard</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-screwdriver-wrench"></i></span> Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-user"></i></span> Profile</a></li>
                    <li><a href="logout.php"><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-right-from-bracket"></i></span> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-key"></i></span> Login</a></li>
                    <li><a href="register.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-pen-to-square"></i></span> Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <!-- Sidebar for Mobile -->
    <aside class="sidebar" id="sidebar" aria-label="Navigation menu" tabindex="-1">
        <div class="sidebar-header flex justify-between items-center">
            <a href="index.php" class="logo">
                <img src="assets/img/icon-192.png" class="logo-img">
                Tennis Predictions
            </a>
            <button class="sidebar-close" id="sidebar-close" aria-label="Close navigation menu" tabindex="0">&times;</button>
        </div>
        <nav class="sidebar-nav" aria-label="Sidebar navigation">
            <ul>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-house"></i></span> Home</a></li>
                <li><a href="fixtures.php" <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-calendar-days"></i></span> Fixtures</a></li>
                <li><a href="predictions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'predictions.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span> Predictions</a></li>
                <li><a href="scoreboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'scoreboard.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-trophy"></i></span> Scoreboard</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="../admin/dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-screwdriver-wrench"></i></span> Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-user"></i></span> Profile</a></li>
                    <li><a href="logout.php"><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-right-from-bracket"></i></span> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-key"></i></span> Login</a></li>
                    <li><a href="register.php" <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active" aria-current="page"' : ''; ?>><span class="nav-icon" aria-hidden="true"><i class="fa-solid fa-pen-to-square"></i></span> Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    
    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
    
    <main>
<script src="assets/js/main.js?v=20240608"></script>
</body>
</html> 