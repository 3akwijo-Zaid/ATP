<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! From here you can manage all aspects of the website.</p>
</div>

<div class="content-card">
    <div class="admin-dashboard-grid">
        <a href="users.php" class="admin-dashboard-card users">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#2196f3,#21cbf3);">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="#fff" stroke="none"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg>
            </span>
            <span>Users</span>
        </a>
        <a href="players.php" class="admin-dashboard-card players">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#43a047,#8bc34a);">
                <svg width="38" height="38" viewBox="0 0 48 48" fill="none"><ellipse cx="32" cy="16" rx="12" ry="4.5" fill="#fff"/><rect x="13" y="33" width="6" height="14" rx="3" transform="rotate(-45 13 33)" fill="#fff"/><circle cx="38" cy="10" r="3" fill="#43a047"/><ellipse cx="32" cy="16" rx="11.5" ry="4" stroke="#43a047" stroke-width="2"/><rect x="13" y="33" width="6" height="14" rx="3" transform="rotate(-45 13 33)" stroke="#388e3c" stroke-width="2"/></svg>
            </span>
            <span>Players</span>
        </a>
        <a href="tournaments.php" class="admin-dashboard-card tournaments">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#ffd54f,#ffb300);">
                <svg width="38" height="38" viewBox="0 0 48 48" fill="none"><rect x="14" y="36" width="20" height="6" rx="3" fill="#fff"/><path d="M24 36V30" stroke="#ffb300" stroke-width="2"/><ellipse cx="24" cy="20" rx="10" ry="8" fill="#fff" stroke="#ffd54f" stroke-width="2"/><path d="M14 20c0 7 20 7 20 0" stroke="#ffd54f" stroke-width="2"/><circle cx="24" cy="20" r="4" fill="#ffd54f"/><polygon points="24,10 26,16 32,16 27,19 29,25 24,21 19,25 21,19 16,16 22,16" fill="#ffb300"/></svg>
            </span>
            <span>Tournaments</span>
        </a>
        <a href="matches.php" class="admin-dashboard-card matches">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#00bcd4,#4fc3f7);">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="#fff" stroke="none"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="#fff" stroke-width="2"/></svg>
            </span>
            <span>Matches</span>
        </a>
        <a href="results.php" class="admin-dashboard-card results">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#ff9800,#ffc107);">
                <svg width="38" height="38" viewBox="0 0 48 48" fill="none"><rect x="6" y="32" width="10" height="8" rx="2" fill="#fff"/><rect x="19" y="24" width="10" height="16" rx="2" fill="#fff"/><rect x="32" y="28" width="10" height="12" rx="2" fill="#fff"/><circle cx="24" cy="20" r="4" fill="#ff9800"/><text x="24" y="24" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial" font-weight="bold">1</text></svg>
            </span>
            <span>Results & Statistics</span>
        </a>
        <a href="game_results.php" class="admin-dashboard-card game-results">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#e91e63,#f06292);">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </span>
            <span>Game Results</span>
        </a>
        <a href="settings.php" class="admin-dashboard-card settings">
            <span class="admin-dashboard-icon-bg" style="background: linear-gradient(135deg,#9c27b0,#e040fb);">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="#fff" stroke="none"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </span>
            <span>Settings</span>
        </a>
    </div>
</div>

<!-- Statistics Overview Section -->
<div class="content-card">
    <h2 style="color: #333; margin-bottom: 1.5rem; text-align: center;">System Overview</h2>
    <div class="stats-overview-grid" id="stats-overview">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #4CAF50, #8BC34A);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div class="stats-content">
                <h3 id="total-matches">-</h3>
                <p>Total Matches</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #2196F3, #03A9F4);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H17c-.8 0-1.54.37-2.01 1l-6.68 8.89c-.31.42-.31.98 0 1.4.31.42.89.71 1.46.71H16v4h4z"/></svg>
            </div>
            <div class="stats-content">
                <h3 id="total-users">-</h3>
                <p>Registered Users</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #FF9800, #FFC107);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div class="stats-content">
                <h3 id="total-predictions">-</h3>
                <p>Total Predictions</p>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2.2rem;
    margin-top: 2.5rem;
    margin-bottom: 2rem;
}
.admin-dashboard-card {
    background: rgba(255,255,255,0.18);
    border-radius: 22px;
    box-shadow: 0 8px 32px #0002, 0 1.5px 0 #fff3 inset;
    padding: 2.7rem 1.2rem 1.2rem 1.2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #222;
    font-size: 1.18em;
    font-weight: 600;
    transition: box-shadow 0.25s, transform 0.22s, background 0.22s, border 0.22s;
    position: relative;
    border: 2.5px solid rgba(255,255,255,0.18);
    backdrop-filter: blur(12px);
    overflow: hidden;
}
.admin-dashboard-card:before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 22px;
    background: linear-gradient(120deg,rgba(255,255,255,0.18) 0%,rgba(255,255,255,0.08) 100%);
    z-index: 0;
    pointer-events: none;
}
.admin-dashboard-card .admin-dashboard-icon-bg {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.1em;
    box-shadow: 0 4px 18px #0002, 0 0 0 6px rgba(255,255,255,0.10);
    position: relative;
    z-index: 1;
    transition: box-shadow 0.22s, transform 0.22s;
}
.admin-dashboard-card:hover {
    box-shadow: 0 16px 48px #0003, 0 2px 0 #fff6 inset;
    transform: translateY(-6px) scale(1.045);
    background: rgba(255,255,255,0.28);
    border: 2.5px solid #ffd54f;
    z-index: 2;
}
.admin-dashboard-card:hover .admin-dashboard-icon-bg {
    box-shadow: 0 8px 32px #ffd54f55, 0 0 0 10px #fff2;
    transform: scale(1.08) rotate(-2deg);
}

/* Statistics Overview Styles */
.stats-overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stats-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: #333;
}

.stats-content p {
    margin: 0;
    color: #666;
    font-weight: 500;
}

@media (max-width: 768px) {
    .stats-overview-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stats-card {
        padding: 1rem;
    }
    
    .stats-content h3 {
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Load dashboard statistics
    await loadDashboardStats();
});

async function loadDashboardStats() {
    try {
        // Load matches count
        const matchesResponse = await fetch('../api/matches.php');
        const matches = await matchesResponse.json();
        document.getElementById('total-matches').textContent = matches.length;

        // Load users count
        const usersResponse = await fetch('../api/users.php');
        const users = await usersResponse.json();
        document.getElementById('total-users').textContent = users.length;

        // Load predictions count (game predictions)
        const gamePredictionsResponse = await fetch('../api/game_predictions.php?stats=1');
        const gamePredictionsData = await gamePredictionsResponse.json();
        const gamePredictionsCount = gamePredictionsData.success ? gamePredictionsData.stats.total_predictions : 0;

        document.getElementById('total-predictions').textContent = gamePredictionsCount;

    } catch (error) {
        console.error('Error loading dashboard statistics:', error);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 