<?php require_once 'includes/header.php'; ?>

<div class="container">
    <h1>Welcome to Tennis Predictions</h1>
    <p style="text-align: center; margin-bottom: 2rem;">Predict match outcomes and compete on the scoreboard!</p>

    <h2>Upcoming Matches</h2>
    <div id="matches-list">
        <!-- Matches will be loaded here via JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const matchesList = document.getElementById('matches-list');

    async function fetchMatches() {
        try {
            const response = await fetch('../api/matches.php');
            const matches = await response.json();

            if (matches.length > 0) {
                matchesList.innerHTML = '';
                matches.forEach(match => {
                    const matchElement = document.createElement('div');
                    matchElement.className = 'list-item';
                    matchElement.innerHTML = `
                        <div>
                            <strong>${match.competition_name}</strong><br>
                            <span>${match.player1} vs ${match.player2}</span>
                        </div>
                        <span>${new Date(match.start_time).toLocaleString()}</span>
                        <a href="predictions.php?match_id=${match.id}" class="btn" style="width: auto; padding: 0.5rem 1rem;">Predict</a>
                    `;
                    matchesList.appendChild(matchElement);
                });
            } else {
                matchesList.innerHTML = '<p>No upcoming matches.</p>';
            }
        } catch (error) {
            console.error('Error fetching matches:', error);
            matchesList.innerHTML = '<p>Could not load matches.</p>';
        }
    }

    fetchMatches();
});
</script>

<?php require_once 'includes/footer.php'; ?> 