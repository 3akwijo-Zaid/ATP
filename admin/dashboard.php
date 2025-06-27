<?php require_once 'includes/header.php'; ?>

<h1>Admin Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<p>From here you can manage the website's matches, results, and settings.</p>

<div style="display: flex; gap: 1rem; margin-top: 2rem;">
    <a href="matches.php" class="btn">Manage Matches</a>
    <a href="results.php" class="btn">Update Results</a>
    <a href="settings.php" class="btn">Point Settings</a>
</div>

<?php require_once 'includes/footer.php'; ?> 