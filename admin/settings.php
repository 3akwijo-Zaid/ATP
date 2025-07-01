<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Point System Settings</h1>
    <p>Customize the points awarded for different types of correct predictions.</p>
</div>

<div class="content-card">
    <form id="point-settings-form">
        <div class="settings-section">
            <h3>Match-Level Predictions</h3>
            <div class="form-group">
                <label for="match-winner">Correct Match Winner</label>
                <input type="number" id="match_winner_points" required min="0" max="100">
            </div>
            <div class="form-group">
                <label for="match-score">Correct Match Score</label>
                <input type="number" id="match_score_points" required min="0" max="100">
            </div>
            <div class="form-group">
                <label for="set-score">Correct Set Score</label>
                <input type="number" id="set_score_points" required min="0" max="50">
            </div>
            <div class="form-group">
                <label for="tiebreak-score">Correct Tiebreak Score</label>
                <input type="number" id="tiebreak_score_points" required min="0" max="30">
            </div>
        </div>

        <div class="settings-section">
            <h3>Game-Level Predictions (Set 1)</h3>
            <div class="form-group">
                <label for="game-winner">Correct Game Winner</label>
                <input type="number" id="game_winner_points" required min="0" max="20">
            </div>
            <div class="form-group">
                <label for="game-score">Correct Game Score (Partial)</label>
                <input type="number" id="game_score_points" required min="0" max="20">
            </div>
            <div class="form-group">
                <label for="exact-game-score">Exact Game Score</label>
                <input type="number" id="exact_game_score_points" required min="0" max="50">
            </div>
            <div class="form-group">
                <label for="set1-complete">Complete Set 1 Prediction</label>
                <input type="number" id="set1_complete_points" required min="0" max="100">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
        <div class="message-container">
            <p id="message"></p>
        </div>
    </form>
</div>

<style>
.settings-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    color: white;
}

.settings-section h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.9);
    font-size: 1rem;
    transition: all 0.3s ease;
    color: black;
}

.form-group input:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    border: none;
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.message-container {
    margin-top: 2rem;
    padding: 1rem;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

#message {
    margin: 0;
    text-align: center;
    font-weight: 600;
}

@media (max-width: 768px) {
    .settings-section {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .settings-section h3 {
        font-size: 1.2rem;
    }
    
    .btn-primary {
        width: 100%;
        padding: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const form = document.getElementById('point-settings-form');
    const matchWinnerInput = document.getElementById('match_winner_points');
    const matchScoreInput = document.getElementById('match_score_points');
    const setScoreInput = document.getElementById('set_score_points');
    const gameWinnerInput = document.getElementById('game_winner_points');
    const gameScoreInput = document.getElementById('game_score_points');
    const exactGameScoreInput = document.getElementById('exact_game_score_points');
    const set1CompleteInput = document.getElementById('set1_complete_points');
    const tiebreakScoreInput = document.getElementById('tiebreak_score_points');
    const messageEl = document.getElementById('message');

    // Fetch current settings
    async function fetchSettings() {
        const response = await fetch('../api/point_settings.php');
        const result = await response.json();
        if (result.success && result.settings) {
            const settings = result.settings;
            matchWinnerInput.value = settings.match_winner_points || 10;
            matchScoreInput.value = settings.match_score_points || 10;
            setScoreInput.value = settings.set_score_points || 5;
            gameWinnerInput.value = settings.game_winner_points || 2;
            gameScoreInput.value = settings.game_score_points || 5;
            exactGameScoreInput.value = settings.exact_game_score_points || 10;
            set1CompleteInput.value = settings.set1_complete_points || 20;
            if (settings.tiebreak_score_points !== undefined) {
                tiebreakScoreInput.value = settings.tiebreak_score_points;
            }
        } else {
            messageEl.textContent = result.message || 'Failed to load settings.';
            messageEl.style.color = '#f44336';
        }
    }

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const payload = {
            match_winner_points: parseInt(matchWinnerInput.value),
            match_score_points: parseInt(matchScoreInput.value),
            set_score_points: parseInt(setScoreInput.value),
            tiebreak_score_points: parseInt(tiebreakScoreInput.value),
            game_winner_points: parseInt(gameWinnerInput.value),
            game_score_points: parseInt(gameScoreInput.value),
            exact_game_score_points: parseInt(exactGameScoreInput.value),
            set1_complete_points: parseInt(set1CompleteInput.value)
        };

        const response = await fetch('../api/point_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        messageEl.textContent = result.message;
        messageEl.style.color = result.success ? '#4CAF50' : '#f44336';
    });

    fetchSettings();
});
</script>

<?php require_once 'includes/footer.php'; ?> 