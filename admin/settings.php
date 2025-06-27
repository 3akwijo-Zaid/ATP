<?php require_once 'includes/header.php'; ?>

<h2>Point System Settings</h2>
<p>Customize the points awarded for different types of correct predictions.</p>

<form id="point-settings-form">
    <div class="form-group">
        <label for="match-winner">Correct Match Winner</label>
        <input type="number" id="match_winner_points" required>
    </div>
    <div class="form-group">
        <label for="set-winner">Correct Set Winner</label>
        <input type="number" id="set_winner_points" required>
    </div>
    <div class="form-group">
        <label for="set-score">Correct Set Score</label>
        <input type="number" id="set_score_points" required>
    </div>
    <button type="submit" class="btn">Save Settings</button>
    <p id="message"></p>
</form>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const form = document.getElementById('point-settings-form');
    const matchWinnerInput = document.getElementById('match_winner_points');
    const setWinnerInput = document.getElementById('set_winner_points');
    const setScoreInput = document.getElementById('set_score_points');
    const messageEl = document.getElementById('message');

    // Fetch current settings
    async function fetchSettings() {
        const response = await fetch('../api/admin.php?action=get_point_settings');
        const settings = await response.json();
        if (settings) {
            matchWinnerInput.value = settings.match_winner_points;
            setWinnerInput.value = settings.set_winner_points;
            setScoreInput.value = settings.set_score_points;
        }
    }

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const payload = {
            match_winner_points: matchWinnerInput.value,
            set_winner_points: setWinnerInput.value,
            set_score_points: setScoreInput.value,
        };

        const response = await fetch('../api/admin.php?action=update_point_settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        messageEl.textContent = result.message;
    });

    fetchSettings();
});
</script>

<?php require_once 'includes/footer.php'; ?> 