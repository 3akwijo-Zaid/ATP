<?php require_once 'includes/header.php'; ?>

<div class="container">
    <h1>Scoreboard</h1>
    <div id="scoreboard-list">
        <!-- Scoreboard will be loaded here -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreboardList = document.getElementById('scoreboard-list');

    async function fetchScoreboard() {
        try {
            const response = await fetch('../api/users.php?action=scoreboard');
            const users = await response.json();

            if (users.length > 0) {
                scoreboardList.innerHTML = '';
                users.forEach((user, index) => {
                    const userElement = document.createElement('div');
                    userElement.className = 'list-item';
                    userElement.innerHTML = `
                        <span><strong>${index + 1}. ${user.username}</strong></span>
                        <span>${user.points} Points</span>
                    `;
                    scoreboardList.appendChild(userElement);
                });
            } else {
                scoreboardList.innerHTML = '<p>No users on the scoreboard yet.</p>';
            }
        } catch (error) {
            console.error('Error fetching scoreboard:', error);
            scoreboardList.innerHTML = '<p>Could not load scoreboard.</p>';
        }
    }

    fetchScoreboard();
});
</script>

<?php require_once 'includes/footer.php'; ?> 