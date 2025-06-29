<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please <a href='login.php'>login</a> to make predictions.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

if (!isset($_GET['match_id'])) {
    echo "<div class='container'><p>No match selected. Go to the <a href='index.php'>homepage</a> to pick a match.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
?>

<div class="container container--full">
    <div id="match-details" class="match-header"></div>
    
    <!-- Match Level Predictions -->
    <div class="prediction-section">
        <div class="section-header">
            <h2>Match Prediction</h2>
            <p>Predict the match winner and set scores</p>
        </div>
        
        <div class="match-prediction-form">
            <div class="winner-selection">
                <h3>Match Winner</h3>
                <div class="player-options">
                    <label class="player-option">
                        <input type="radio" name="winner" value="player1" id="winner-player1">
                        <div class="player-card">
                            <img id="player1-avatar" src="assets/img/default-avatar.png" alt="Player 1">
                            <span id="player1-name">Player 1</span>
                        </div>
                    </label>
                    <label class="player-option">
                        <input type="radio" name="winner" value="player2" id="winner-player2">
                        <div class="player-card">
                            <img id="player2-avatar" src="assets/img/default-avatar.png" alt="Player 2">
                            <span id="player2-name">Player 2</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="sets-prediction">
                <h3>Set Scores</h3>
                <div class="sets-grid" id="sets-grid">
                    <!-- Sets will be dynamically generated -->
                </div>
            </div>
            
            <div class="prediction-controls">
                <button type="button" class="btn btn-primary" id="save-match-prediction">Save Match Prediction</button>
                <button type="button" class="btn btn-secondary" id="clear-match-prediction">Clear</button>
            </div>
        </div>
    </div>
    
    <!-- Game Level Predictions -->
    <div class="prediction-section">
        <div class="section-header">
            <h2>Game Predictions (Set 1)</h2>
            <p>Predict individual game outcomes for extra points</p>
        </div>
        
        <div class="games-grid" id="games-grid">
            <!-- Games will be dynamically generated -->
        </div>
        
        <div class="prediction-controls">
            <button type="button" class="btn btn-primary" id="save-game-predictions">Save Game Predictions</button>
            <button type="button" class="btn btn-secondary" id="clear-game-predictions">Clear All</button>
        </div>
    </div>
    
    <!-- Prediction Summary -->
    <div class="prediction-section">
        <div class="section-header">
            <h2>Your Predictions</h2>
        </div>
        <div id="prediction-summary" class="prediction-summary">
            <!-- Summary will be loaded here -->
        </div>
    </div>
    
    <div id="lock-message" class="lock-message" style="display:none;">
        <div class="lock-icon">🔒</div>
        <p>Predictions are locked for this match</p>
    </div>
</div>

<style>
body {
    background: linear-gradient(120deg, #232526 0%, #414345 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
}
.container.container--full {
    max-width: 900px;
    margin: 2rem auto;
    background: rgba(255,255,255,0.95);
    border-radius: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    padding: 2.5rem 2rem 2rem 2rem;
}
.match-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem 2rem 2rem 2rem;
    border-radius: 20px;
    margin-bottom: 2.5rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18);
    position: relative;
    overflow: hidden;
}
.match-header:before {
    content: '';
    position: absolute;
    top: -40px; left: -40px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    z-index: 0;
}
.match-header h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2.3rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    position: relative;
    z-index: 1;
}
.match-header p {
    font-size: 1.1rem;
    opacity: 0.95;
    margin-bottom: 0.2rem;
    position: relative;
    z-index: 1;
}
.prediction-section {
    background: white;
    border-radius: 24px;
    padding: 2.2rem 1.5rem 1.5rem 1.5rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.09);
    margin-bottom: 2.2rem;
    position: relative;
    overflow: hidden;
}
.section-header {
    text-align: center;
    margin-bottom: 2.2rem;
}
.section-header h2 {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 2rem;
    letter-spacing: 0.5px;
    font-weight: 700;
}
.section-header p {
    color: #666;
    font-size: 1.13rem;
    opacity: 0.92;
}
.winner-selection h3,
.sets-prediction h3 {
    color: #333;
    margin-bottom: 1.1rem;
    font-size: 1.25rem;
    font-weight: 600;
}

/* --- Improved Player Card Styles --- */
.player-options {
    display: flex;
    gap: 2.5rem;
    justify-content: center;
    margin-bottom: 2.2rem;
    flex-wrap: wrap;
}
.player-option {
    cursor: pointer;
    transition: transform 0.22s cubic-bezier(.4,2,.3,1);
    border-radius: 22px;
    padding: 0.2rem;
    background: transparent;
    box-shadow: none;
    position: relative;
}
.player-option input[type="radio"] {
    display: none;
}
.player-card {
    background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
    border-radius: 32px;
    padding: 2.7rem 2.2rem 2.1rem 2.2rem;
    color: #fff;
    text-align: center;
    transition: all 0.32s cubic-bezier(.4,2,.3,1);
    box-shadow: 0 16px 48px 0 rgba(24,90,157,0.18), 0 4px 16px rgba(67,206,162,0.13);
    border: 4px solid transparent;
    min-width: 240px;
    max-width: 320px;
    position: relative;
    z-index: 1;
    overflow: visible;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.player-option input[type="radio"]:checked + .player-card {
    border-color: #ffb347;
    box-shadow: 0 24px 60px 0 rgba(255,179,71,0.22), 0 4px 16px rgba(67,206,162,0.18);
    transform: translateY(-12px) scale(1.07);
}
.player-card img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin-bottom: 1.2rem;
    border: 5px solid rgba(255,255,255,0.38);
    box-shadow: 0 8px 32px rgba(24,90,157,0.13), 0 2px 8px rgba(67,206,162,0.10);
    background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
    object-fit: cover;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.player-option input[type="radio"]:checked + .player-card img {
    border-color: #ffb347;
    box-shadow: 0 0 0 6px #ffb34755, 0 8px 32px rgba(24,90,157,0.13);
}
.player-card span {
    font-weight: 900;
    font-size: 1.45rem;
    text-shadow: 0 4px 16px rgba(24,90,157,0.18);
    letter-spacing: 1px;
    display: block;
    margin-top: 0.3rem;
    margin-bottom: 0.2rem;
    line-height: 1.22;
    text-transform: uppercase;
    letter-spacing: 1.2px;
}
.player-card::after {
    content: '';
    display: block;
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    width: 70%;
    height: 14px;
    background: linear-gradient(90deg, #ffb347 0%, #43cea2 100%);
    border-radius: 0 0 24px 24px;
    opacity: 0.22;
    z-index: 0;
}
.player-option:hover .player-card {
    box-shadow: 0 18px 40px 0 rgba(240,147,251,0.18), 0 2px 8px rgba(0,0,0,0.13);
    transform: translateY(-4px) scale(1.025);
}
.player-option:active .player-card {
    transform: scale(0.98);
}
@media (max-width: 768px) {
    .player-options {
        flex-direction: column;
        align-items: center;
        gap: 1.1rem;
    }
    .player-card {
        min-width: 180px;
        max-width: 100%;
        padding: 1.7rem 0.9rem 1.4rem 0.9rem;
    }
    .player-card img {
        width: 90px;
        height: 90px;
    }
    .player-card span {
        font-size: 1.1rem;
    }
}
.sets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1.2rem;
    margin-bottom: 2.2rem;
}
.set-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 14px;
    padding: 1.2rem 1rem 1rem 1rem;
    color: white;
    text-align: center;
    box-shadow: 0 4px 18px rgba(0,0,0,0.10);
    position: relative;
}
.set-card h4 {
    margin: 0 0 1.1rem 0;
    font-size: 1.13rem;
    font-weight: 600;
}
.set-scores {
    display: flex;
    gap: 0.7rem;
    justify-content: center;
    align-items: center;
}
.set-scores input {
    width: 54px;
    padding: 9px;
    border: none;
    border-radius: 7px;
    text-align: center;
    font-weight: bold;
    font-size: 1.1rem;
    background: rgba(255,255,255,0.85);
    color: #333;
    box-shadow: 0 2px 6px rgba(0,0,0,0.07);
    transition: box-shadow 0.2s;
}
.set-scores input:focus {
    outline: none;
    box-shadow: 0 0 0 3px #ffd54f44;
}
.set-scores span {
    font-weight: bold;
    font-size: 1.25rem;
}
.tiebreak-row label {
    color: #fff;
    font-size: 0.92em;
    font-weight: 500;
    margin-right: 0.4em;
    letter-spacing: 0.1px;
    padding-left: 0.5em;
}
.tiebreak-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.7em;
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 8px;
    padding: 0.25em 0.3em;
    margin: 0.7rem auto 0.1rem auto;
    left: unset;
    position: static;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    width: 95%;
    max-width: 340px;
    min-width: 0;
    box-sizing: border-box;
}
.tiebreak-row input[type="number"] {
    width: 32px !important;
    padding: 4px 0;
    border: none;
    border-radius: 5px;
    text-align: center;
    font-weight: 500;
    font-size: 0.98rem;
    background: rgba(255,255,255,0.92);
    color: #333;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    transition: box-shadow 0.2s;
}
.tiebreak-row input[type="number"]:focus {
    outline: none;
    background: #fff;
    box-shadow: 0 0 0 2px #ffd54f88;
}
.tiebreak-row span {
    color: #fff;
    font-weight: bold;
    font-size: 1.15em;
    margin: 0 0.2em;
}
/* 2 cards per row for game cards */
.games-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.7rem;
    margin-bottom: 2.2rem;
    width: 100%;
}
@media (max-width: 1024px) {
    .games-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .games-grid {
        grid-template-columns: 1fr;
        gap: 1.1rem;
    }
    .game-card {
        padding: 1.1rem;
    }
}
.game-card {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    border-radius: 18px;
    padding: 1.7rem 1.2rem;
    color: #333;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(.4,2,.3,1);
    box-shadow: 0 8px 25px rgba(0,0,0,0.13);
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
}
.game-card:hover {
    transform: translateY(-7px) scale(1.03);
    box-shadow: 0 18px 40px rgba(0,0,0,0.18);
}
.game-card h3 {
    margin: 0 0 1.1rem 0;
    font-size: 1.25rem;
    text-align: center;
    color: #333;
    font-weight: 700;
}
.game-inputs {
    display: grid;
    gap: 1.1rem;
}
.input-group {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.input-group label {
    font-weight: 600;
    min-width: 80px;
    color: #333;
}
.input-group select,
.input-group input {
    flex: 1 1 0%;
    min-width: 0;
    max-width: 100%;
    width: 100%;
    padding: 11px 16px;
    border: none;
    border-radius: 9px;
    background: rgba(255,255,255,0.93);
    font-size: 1.05rem;
    transition: all 0.3s;
    box-sizing: border-box;
    overflow: hidden;
    text-overflow: ellipsis;
}
.input-group select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 0.7em center;
    background-size: 1.2em;
    padding-right: 2.2em;
}
.input-group select:focus,
.input-group input:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px #ffd54f44;
    transform: translateY(-2px);
}
.score-input {
    display: flex;
    gap: 0.7rem;
    align-items: center;
}
.score-input input {
    width: 62px;
    text-align: center;
    font-weight: bold;
}
.score-input span {
    font-weight: bold;
    font-size: 1.25rem;
    color: #333;
}
.prediction-controls {
    display: flex;
    gap: 1.2rem;
    justify-content: center;
    margin-bottom: 1.2rem;
}
.btn {
    padding: 13px 28px;
    border: none;
    border-radius: 12px;
    font-size: 1.08rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(.4,2,.3,1);
    text-decoration: none;
    display: inline-block;
    text-align: center;
    letter-spacing: 0.2px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.10);
}
.btn-primary {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.18);
}
.btn-primary:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 25px rgba(0,0,0,0.22);
}
.btn-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
    box-shadow: 0 5px 15px rgba(0,0,0,0.10);
}
.btn-secondary:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 25px rgba(0,0,0,0.18);
}
.prediction-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 18px;
    padding: 2.2rem 1.5rem 1.5rem 1.5rem;
    color: white;
    margin-top: 2.2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.13);
}
.prediction-summary h3 {
    margin-top: 0;
    margin-bottom: 1.7rem;
    text-align: center;
    font-size: 1.55rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    font-weight: 700;
}
.game-predictions-sequence {
    margin-top: 1.1rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.85rem;
    max-width: 100%;
}
.game-prediction-item {
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255,255,255,0.22);
    border-radius: 13px;
    padding: 0.85rem 0.5rem;
    text-align: center;
    font-size: 0.98rem;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.09);
}
.game-prediction-item:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 25px rgba(0,0,0,0.18);
    background: rgba(255,255,255,0.23);
}
.game-prediction-item .game-number {
    font-weight: bold;
    margin-bottom: 0.5rem;
    font-size: 1.05rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.18);
}
.game-prediction-item .game-score {
    font-size: 0.89rem;
    margin-bottom: 0.25rem;
    font-weight: 700;
}
.game-prediction-item .game-winner {
    font-size: 0.8rem;
    opacity: 0.93;
    font-style: italic;
}
.lock-message {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 2.2rem 1.5rem;
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18);
    margin-top: 2rem;
}
.lock-icon {
    font-size: 3.2rem;
    margin-bottom: 1.1rem;
}
.message {
    padding: 1.1rem;
    border-radius: 12px;
    margin: 1.1rem 0;
    text-align: center;
    font-weight: 700;
    font-size: 1.08rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.message.success {
    background: #4CAF50;
    color: white;
}
.message.error {
    background: #f44336;
    color: white;
}
.message.warning {
    background: #ff9800;
    color: white;
}
@media (max-width: 768px) {
    .container.container--full {
        padding: 1.2rem 0.5rem 1rem 0.5rem;
    }
    .player-options {
        flex-direction: column;
        align-items: center;
        gap: 1.1rem;
    }
    .sets-grid {
        grid-template-columns: 1fr;
    }
    .games-grid {
        grid-template-columns: 1fr;
        gap: 1.1rem;
    }
    .game-card {
        padding: 1.1rem;
    }
    .prediction-controls {
        flex-direction: column;
        gap: 0.7rem;
    }
    .btn {
        width: 100%;
    }
    .input-group {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    .input-group label {
        min-width: auto;
        text-align: center;
    }
    .game-predictions-sequence {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.6rem;
    }
    .game-prediction-item {
        padding: 0.6rem 0.3rem;
        font-size: 0.85rem;
    }
}
</style>

<script>
const user_id = <?php echo $user_id; ?>;

document.addEventListener('DOMContentLoaded', async function() {
    const matchId = new URLSearchParams(window.location.search).get('match_id');
    const matchDetails = document.getElementById('match-details');
    const gamesGrid = document.getElementById('games-grid');
    const setsGrid = document.getElementById('sets-grid');
    const saveMatchBtn = document.getElementById('save-match-prediction');
    const clearMatchBtn = document.getElementById('clear-match-prediction');
    const saveGameBtn = document.getElementById('save-game-predictions');
    const clearGameBtn = document.getElementById('clear-game-predictions');
    const lockMessage = document.getElementById('lock-message');
    const predictionSummary = document.getElementById('prediction-summary');

    let match = null;

    // Fetch match details
    try {
        const response = await fetch(`../api/matches.php?id=${matchId}`);
        match = await response.json();

        if (match) {
            matchDetails.innerHTML = `
                <h1>${match.player1_name} vs ${match.player2_name}</h1>
                <p>${match.tournament_name} - ${match.round}</p>
                <p>${new Date(match.start_time).toLocaleString()}</p>
            `;
            
            // Update player names and avatars
            document.getElementById('player1-name').textContent = match.player1_name;
            document.getElementById('player2-name').textContent = match.player2_name;
            
            if (match.player1_image) {
                document.getElementById('player1-avatar').src = match.player1_image;
            }
            if (match.player2_image) {
                document.getElementById('player2-avatar').src = match.player2_image;
            }
            
            // Check if predictions are locked
            const startTime = new Date(match.start_time);
            const now = new Date();
            const timeDiff = (startTime - now) / 1000; // seconds
            
            if (timeDiff <= 3600) {
                lockMessage.style.display = 'block';
                document.querySelectorAll('.prediction-section').forEach(section => {
                    section.style.display = 'none';
                });
                return;
            }
            
            // Generate sets and games
            generateSetsGrid(match);
            generateGameCards(match);
            
            // Load existing predictions
            await loadExistingPredictions();
            await loadPredictionSummary();
        }
    } catch (error) {
        console.error('Error fetching match:', error);
        matchDetails.innerHTML = '<p>Error loading match details.</p>';
    }

    function generateSetsGrid(match) {
        setsGrid.innerHTML = '';
        // Dynamically determine number of sets to show based on match format and any previous prediction
        let maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        let previousSets = [];
        if (match.prediction_data && Array.isArray(match.prediction_data.sets)) {
            previousSets = match.prediction_data.sets;
            maxSets = Math.max(maxSets, previousSets.length);
        }
        // Store tiebreak values if present in previousSets
        let previousTiebreaks = [];
        if (previousSets.length > 0) {
            previousTiebreaks = previousSets.map(set => set.tiebreak || '');
        }
        for (let setNum = 1; setNum <= maxSets; setNum++) {
            const setCard = document.createElement('div');
            setCard.className = 'set-card';
            setCard.innerHTML = `
                <h4>Set ${setNum}</h4>
                <div class="set-scores">
                    <input type="number" id="set${setNum}-player1" min="0" max="7" placeholder="0">
                    <span>-</span>
                    <input type="number" id="set${setNum}-player2" min="0" max="7" placeholder="0">
                </div>
                <div class="tiebreak-row" id="tiebreak-row-${setNum}" style="display:none; margin-top:0.5rem;">
                    <label style="color:#fff; font-size:0.95em;">Tiebreak: </label>
                    <input type="number" id="tiebreak${setNum}-player1" min="0" max="10" placeholder="0" style="width:40px; margin-left:0.5em;">
                    <span style="color:#fff;">-</span>
                    <input type="number" id="tiebreak${setNum}-player2" min="0" max="10" placeholder="0" style="width:40px;">
                </div>
            `;
            setsGrid.appendChild(setCard);
        }
        // If previous sets exist, fill them in (including tiebreaks)
        if (previousSets.length > 0) {
            previousSets.forEach((set, index) => {
                const setNum = index + 1;
                const player1Input = document.getElementById(`set${setNum}-player1`);
                const player2Input = document.getElementById(`set${setNum}-player2`);
                if (player1Input && player2Input) {
                    player1Input.value = set.player1 || '';
                    player2Input.value = set.player2 || '';
                }
                // If tiebreak exists, show tiebreak row and fill values
                if (set.tiebreak) {
                    const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
                    if (tiebreakRow) tiebreakRow.style.display = '';
                    const tb1 = document.getElementById(`tiebreak${setNum}-player1`);
                    const tb2 = document.getElementById(`tiebreak${setNum}-player2`);
                    if (tb1 && tb2) {
                        tb1.value = set.tiebreak.player1 || '';
                        tb2.value = set.tiebreak.player2 || '';
                    }
                }
            });
        }
        // Helper to show/hide tiebreak row for a set
        function updateTiebreakRow(setNum) {
            const p1 = document.getElementById(`set${setNum}-player1`);
            const p2 = document.getElementById(`set${setNum}-player2`);
            const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
            if (!p1 || !p2 || !tiebreakRow) return;
            const v1 = p1.value !== '' ? parseInt(p1.value) : null;
            const v2 = p2.value !== '' ? parseInt(p2.value) : null;
            if ((v1 === 7 && v2 === 6) || (v1 === 6 && v2 === 7)) {
                tiebreakRow.style.display = '';
            } else {
                tiebreakRow.style.display = 'none';
                // Optionally clear tiebreak values if hiding
                document.getElementById(`tiebreak${setNum}-player1`).value = '';
                document.getElementById(`tiebreak${setNum}-player2`).value = '';
            }
        }
        // Attach event listeners to set inputs for tiebreak logic
        for (let i = 1; i <= maxSets; i++) {
            const p1 = document.getElementById(`set${i}-player1`);
            const p2 = document.getElementById(`set${i}-player2`);
            if (p1 && p2) {
                p1.addEventListener('input', () => updateTiebreakRow(i));
                p2.addEventListener('input', () => updateTiebreakRow(i));
                // Initial tiebreak row state
                updateTiebreakRow(i);
            }
        }
        // Add logic to disable remaining sets if a player has already won
        function updateSetDisabling() {
            let setsToWin = match.match_format === 'best_of_5' ? 3 : 2;
            let player1Sets = 0, player2Sets = 0;
            let matchOverAt = null;
            for (let i = 1; i <= maxSets; i++) {
                const p1 = document.getElementById(`set${i}-player1`);
                const p2 = document.getElementById(`set${i}-player2`);
                if (!p1 || !p2) continue;
                const v1 = p1.value !== '' ? parseInt(p1.value) : null;
                const v2 = p2.value !== '' ? parseInt(p2.value) : null;
                if (v1 !== null && v2 !== null) {
                    if (v1 > v2) player1Sets++;
                    else if (v2 > v1) player2Sets++;
                }
                if ((player1Sets === setsToWin || player2Sets === setsToWin) && matchOverAt === null) {
                    matchOverAt = i;
                }
            }
            for (let i = 1; i <= maxSets; i++) {
                const p1 = document.getElementById(`set${i}-player1`);
                const p2 = document.getElementById(`set${i}-player2`);
                if (!p1 || !p2) continue;
                if (matchOverAt !== null && i > matchOverAt) {
                    p1.disabled = true;
                    p2.disabled = true;
                    p1.classList.add('disabled');
                    p2.classList.add('disabled');
                } else {
                    p1.disabled = false;
                    p2.disabled = false;
                    p1.classList.remove('disabled');
                    p2.classList.remove('disabled');
                }
            }
        }
        // Attach event listeners to all set inputs for disabling logic
        for (let i = 1; i <= maxSets; i++) {
            const p1 = document.getElementById(`set${i}-player1`);
            const p2 = document.getElementById(`set${i}-player2`);
            if (p1 && p2) {
                p1.addEventListener('input', updateSetDisabling);
                p2.addEventListener('input', updateSetDisabling);
            }
        }
        // Initial disabling
        updateSetDisabling();
    }

    function generateGameCards(match) {
        gamesGrid.innerHTML = '';
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const gameCard = document.createElement('div');
            gameCard.className = 'game-card';
            gameCard.id = `game-${gameNum}`;

            gameCard.innerHTML = `
                <h3>Point ${gameNum}</h3>
                <div class="game-inputs">
                    <div class="input-group">
                        <label>Winner:</label>
                        <select id="winner-${gameNum}" class="game-winner">
                            <option value="">Select Winner</option>
                            <option value="player1">${match.player1_name}</option>
                            <option value="player2">${match.player2_name}</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Score:</label>
                        <div class="score-input" style="width:100%;display:flex;align-items:center;gap:0.7rem;">
                            <select id="score1-${gameNum}" class="game-score" disabled style="width:60px;min-width:60px;">
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                            <span>-</span>
                            <select id="score2-${gameNum}" class="game-score" disabled style="width:60px;min-width:60px;">
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            // Only show the first game card initially
            if (gameNum !== 1) {
                gameCard.style.display = 'none';
            }
            gamesGrid.appendChild(gameCard);
        }

        // Add event listeners for winner selection to update scores and reveal next card
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winnerSelect = document.getElementById(`winner-${gameNum}`);
            const score1Select = document.getElementById(`score1-${gameNum}`);
            const score2Select = document.getElementById(`score2-${gameNum}`);

            winnerSelect.addEventListener('change', function() {
                // For first game, winner gets 15, loser gets 0
                if (gameNum === 1) {
                    if (winnerSelect.value === 'player1') {
                        score1Select.value = '15';
                        score2Select.value = '0';
                    } else if (winnerSelect.value === 'player2') {
                        score1Select.value = '0';
                        score2Select.value = '15';
                    } else {
                        score1Select.value = '';
                        score2Select.value = '';
                    }
                } else {
                    // For subsequent games, winner's score increases by tennis logic: 0 -> 15 -> 30 -> 40 -> GAME
                    const tennisOrder = ['0', '15', '30', '40', 'AD', 'game'];
                    let prevScore1 = document.getElementById(`score1-${gameNum-1}`).value;
                    let prevScore2 = document.getElementById(`score2-${gameNum-1}`).value;
                    prevScore1 = tennisOrder.includes(prevScore1) ? prevScore1 : '0';
                    prevScore2 = tennisOrder.includes(prevScore2) ? prevScore2 : '0';
                    function nextTennisScore(score, opponentScore) {
                        if (score === '0') return '15';
                        if (score === '15') return '30';
                        if (score === '30') return '40';
                        if (score === '40') {
                            if (opponentScore === '40') return 'AD';
                            if (opponentScore === 'AD') return '40';
                            return 'game';
                        }
                        if (score === 'AD') return 'game';
                        return score;
                    }
                    if (winnerSelect.value === 'player1') {
                        if (prevScore2 === 'AD') {
                            score1Select.value = '40';
                            score2Select.value = '40';
                        } else {
                            score1Select.value = nextTennisScore(prevScore1, prevScore2);
                            score2Select.value = prevScore2;
                        }
                    } else if (winnerSelect.value === 'player2') {
                        if (prevScore1 === 'AD') {
                            score1Select.value = '40';
                            score2Select.value = '40';
                        } else {
                            score1Select.value = prevScore1;
                            score2Select.value = nextTennisScore(prevScore2, prevScore1);
                        }
                    } else {
                        score1Select.value = '';
                        score2Select.value = '';
                    }
                }
                // Reveal the next game card if needed
                const currentGameCard = document.getElementById(`game-${gameNum}`);
                const nextGameCard = document.getElementById(`game-${gameNum+1}`);
                if (score1Select.value === 'game' || score2Select.value === 'game') {
                    // Stop revealing further cards
                } else if (nextGameCard) {
                    nextGameCard.style.display = '';
                }
            });
        }
    }

    async function loadExistingPredictions() {
        try {
            // Load match predictions - Fix: use actual user ID, not matchId
            const matchResponse = await fetch(`../api/predictions.php?match_id=${matchId}&user_id=${user_id}`);
            const matchData = await matchResponse.json();
            
            console.log('Match prediction API response:', matchData);
            
            if (matchData.success && matchData.prediction) {
                const predData = matchData.prediction.prediction_data;
                console.log('Prediction data:', predData);
                
                // Set winner
                if (predData.winner) {
                    document.getElementById(`winner-${predData.winner}`).checked = true;
                }
                
                // Set set scores
                if (predData.sets) {
                    predData.sets.forEach((set, index) => {
                        const setNum = index + 1;
                        const player1Input = document.getElementById(`set${setNum}-player1`);
                        const player2Input = document.getElementById(`set${setNum}-player2`);
                        
                        if (player1Input && player2Input) {
                            player1Input.value = set.player1 || '';
                            player2Input.value = set.player2 || '';
                        }
                    });
                }
                
                // Disable save button since prediction exists
                saveMatchBtn.disabled = true;
                saveMatchBtn.textContent = 'Prediction Submitted';
            } else {
                console.log('No match prediction found or API error');
                // Enable save button since no prediction exists
                saveMatchBtn.disabled = false;
                saveMatchBtn.textContent = 'Save Match Prediction';
            }
            
            // Load game predictions
            const gameResponse = await fetch(`../api/game_predictions.php?match_id=${matchId}&user_predictions=1`);
            const gameData = await gameResponse.json();
            
            console.log('Game prediction API response:', gameData);
            
            if (gameData.success && gameData.predictions && gameData.predictions.length > 0) {
                gameData.predictions.forEach(prediction => {
                    const winnerSelect = document.getElementById(`winner-${prediction.game_number}`);
                    const score1Select = document.getElementById(`score1-${prediction.game_number}`);
                    const score2Select = document.getElementById(`score2-${prediction.game_number}`);
                    
                    if (winnerSelect) winnerSelect.value = prediction.predicted_winner;
                    
                    const scores = prediction.predicted_score.split('-');
                    if (score1Select && scores[0]) score1Select.value = scores[0];
                    if (score2Select && scores[1]) score2Select.value = scores[1];
                });
                
                // Disable save button since predictions exist
                saveGameBtn.disabled = true;
                saveGameBtn.textContent = 'Game Predictions Submitted';
            } else {
                console.log('No game predictions found or API error');
                // Enable save button since no predictions exist
                saveGameBtn.disabled = false;
                saveGameBtn.textContent = 'Save Game Predictions';
            }
        } catch (error) {
            console.error('Error loading predictions:', error);
        }
    }

    // Helper function to format score display
    function formatScoreDisplay(score) {
        return score.replace(/game/g, 'GAME');
    }

    async function loadPredictionSummary() {
        try {
            const [matchResponse, gameResponse] = await Promise.all([
                fetch(`../api/predictions.php?match_id=${matchId}&user_id=${user_id}`),
                fetch(`../api/game_predictions.php?match_id=${matchId}&user_predictions=1`)
            ]);
            
            const matchData = await matchResponse.json();
            const gameData = await gameResponse.json();
            
            let summaryHTML = '<h3>Your Current Predictions</h3>';
            
            // Match prediction summary
            if (matchData.success && matchData.prediction) {
                const predData = matchData.prediction.prediction_data;
                summaryHTML += `
                    <div style="margin-bottom: 1rem;">
                        <strong>Match Winner:</strong> ${predData.winner === 'player1' ? match.player1_name : match.player2_name}<br>
                        <strong>Set Scores:</strong> ${predData.sets.map((set, i) => {
                            let score = `Set ${i+1}: ${set.player1}-${set.player2}`;
                            if (set.tiebreak && (typeof set.tiebreak.player1 !== 'undefined' || typeof set.tiebreak.player2 !== 'undefined')) {
                                const tb1 = set.tiebreak.player1 !== undefined && set.tiebreak.player1 !== '' ? set.tiebreak.player1 : '';
                                const tb2 = set.tiebreak.player2 !== undefined && set.tiebreak.player2 !== '' ? set.tiebreak.player2 : '';
                                if (tb1 !== '' || tb2 !== '') {
                                    score += ` <span style='color:#ffd54f;'>(TB: ${tb1}-${tb2})</span>`;
                                }
                            }
                            return score;
                        }).join(', ')}
                    </div>
                `;
            } else {
                summaryHTML += '<div style="margin-bottom: 1rem;"><em>No match prediction submitted yet.</em></div>';
            }
            
            // Game prediction summary
            if (gameData.success && gameData.predictions && gameData.predictions.length > 0) {
                // Sort predictions by game number
                const sortedPredictions = gameData.predictions.sort((a, b) => a.game_number - b.game_number);
                
                summaryHTML += `
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong>Game Predictions (Set 1):</strong>
                        </div>
                        <div class="game-predictions-sequence">
                `;
                
                sortedPredictions.forEach(prediction => {
                    const winnerName = prediction.predicted_winner === 'player1' ? match.player1_name : match.player2_name;
                    summaryHTML += `
                        <div class="game-prediction-item">
                            <div class="game-number">Point ${prediction.game_number}</div>
                            <div class="game-score">${formatScoreDisplay(prediction.predicted_score)}</div>
                            <div class="game-winner">${winnerName}</div>
                        </div>
                    `;
                });
                
                summaryHTML += `
                        </div>
                    </div>
                `;
            } else {
                summaryHTML += '<div><em>No game predictions submitted yet.</em></div>';
            }
            
            predictionSummary.innerHTML = summaryHTML;
        } catch (error) {
            console.error('Error loading prediction summary:', error);
        }
    }

    // Match prediction handlers
    saveMatchBtn.addEventListener('click', async function() {
        const winner = document.querySelector('input[name="winner"]:checked')?.value;
        const sets = [];
        // Collect set scores and tiebreaks
        const setCards = document.querySelectorAll('.set-card');
        for (let i = 0; i < setCards.length; i++) {
            const setNum = i + 1;
            const player1Input = document.getElementById(`set${setNum}-player1`);
            const player2Input = document.getElementById(`set${setNum}-player2`);
            if (player1Input && player2Input && player1Input.value !== '' && player2Input.value !== '') {
                const setObj = {
                    player1: parseInt(player1Input.value),
                    player2: parseInt(player2Input.value)
                };
                // If tiebreak row is visible and has values, add tiebreak property
                const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
                if (tiebreakRow && tiebreakRow.style.display !== 'none') {
                    const tb1 = document.getElementById(`tiebreak${setNum}-player1`);
                    const tb2 = document.getElementById(`tiebreak${setNum}-player2`);
                    if (tb1 && tb2 && (tb1.value !== '' || tb2.value !== '')) {
                        setObj.tiebreak = {
                            player1: tb1.value !== '' ? parseInt(tb1.value) : '',
                            player2: tb2.value !== '' ? parseInt(tb2.value) : ''
                        };
                    }
                }
                sets.push(setObj);
            }
        }
        // Dynamically determine how many sets are needed to win
        let setsToWin = 2;
        if (sets.length > 0 && sets.length > 3) setsToWin = 3;
        if (typeof match !== 'undefined' && match && match.match_format === 'best_of_5') setsToWin = 3;

        // Count sets won by each player
        let player1Sets = 0, player2Sets = 0;
        sets.forEach(set => {
            if (set.player1 > set.player2) player1Sets++;
            else if (set.player2 > set.player1) player2Sets++;
        });

        // Only allow submission if a player has won the required number of sets
        if (!winner) {
            showMessage('Please select a match winner.', 'error');
            return;
        }
        if (sets.length === 0) {
            showMessage('Please predict at least one set score.', 'error');
            return;
        }
        if ((winner === 'player1' && player1Sets < setsToWin) || (winner === 'player2' && player2Sets < setsToWin)) {
            showMessage(`The selected winner must win at least ${setsToWin} sets.`, 'error');
            return;
        }
        if (player1Sets === player2Sets) {
            showMessage('There cannot be a tie in sets. Please adjust your set scores.', 'error');
            return;
        }
        try {
            const response = await fetch('../api/predictions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: matchId,
                    winner: winner,
                    sets: sets
                })
            });
            
            const result = await response.json();
            console.log('Save match prediction result:', result);
            
            if (result.success) {
                showMessage(result.message, 'success');
                saveMatchBtn.disabled = true;
                saveMatchBtn.textContent = 'Prediction Submitted';
                await loadExistingPredictions();
                await loadPredictionSummary();
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving match prediction:', error);
            showMessage('Error saving prediction. Please try again.', 'error');
        }
    });

    clearMatchBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to clear your match prediction? This will permanently delete it from the database.')) {
            try {
                const response = await fetch(`../api/predictions.php?match_id=${matchId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                console.log('Delete match prediction result:', result);
                
                if (result.success) {
                    // Clear form fields
                    document.querySelectorAll('input[name="winner"]').forEach(radio => radio.checked = false);
                    document.querySelectorAll('.set-card input').forEach(input => input.value = '');
                    // Re-enable save button
                    saveMatchBtn.disabled = false;
                    saveMatchBtn.textContent = 'Save Match Prediction';
                    showMessage(result.message, 'success');
                    await loadPredictionSummary();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting match prediction:', error);
                showMessage('Error deleting prediction. Please try again.', 'error');
            }
        }
    });

    // Game prediction handlers
    saveGameBtn.addEventListener('click', async function() {
        const predictions = [];
        let hasErrors = false;
        
        // Collect all predictions
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winner = document.getElementById(`winner-${gameNum}`).value;
            const score1 = document.getElementById(`score1-${gameNum}`).value;
            const score2 = document.getElementById(`score2-${gameNum}`).value;
            
            if (winner && score1 && score2) {
                predictions.push({
                    match_id: matchId,
                    game_number: gameNum,
                    predicted_winner: winner,
                    predicted_score: `${score1}-${score2}`
                });
            }
        }
        
        if (predictions.length === 0) {
            showMessage('Please make at least one game prediction before saving.', 'error');
            return;
        }
        
        // Save predictions
        let savedCount = 0;
        for (const prediction of predictions) {
            try {
                const response = await fetch('../api/game_predictions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(prediction)
                });
                
                const result = await response.json();
                console.log('Save game prediction result:', result);
                if (result.success) {
                    savedCount++;
                }
            } catch (error) {
                console.error('Error saving prediction:', error);
            }
        }
        
        if (savedCount === predictions.length) {
            showMessage(`Successfully saved ${savedCount} game predictions!`, 'success');
            saveGameBtn.disabled = true;
            saveGameBtn.textContent = 'Game Predictions Submitted';
            await loadExistingPredictions();
            await loadPredictionSummary();
        } else {
            showMessage(`Saved ${savedCount} out of ${predictions.length} predictions.`, 'warning');
        }
    });

    clearGameBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to clear all game predictions? This will permanently delete them from the database.')) {
            try {
                const response = await fetch(`../api/game_predictions.php?match_id=${matchId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                console.log('Delete game predictions result:', result);
                
                if (result.success) {
                    // Clear form fields
                    for (let gameNum = 1; gameNum <= 12; gameNum++) {
                        document.getElementById(`winner-${gameNum}`).value = '';
                        document.getElementById(`score1-${gameNum}`).value = '';
                        document.getElementById(`score2-${gameNum}`).value = '';
                    }
                    // Re-enable save button
                    saveGameBtn.disabled = false;
                    saveGameBtn.textContent = 'Save Game Predictions';
                    showMessage(result.message, 'success');
                    await loadPredictionSummary();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting game predictions:', error);
                showMessage('Error deleting predictions. Please try again.', 'error');
            }
        }
    });

    function showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message
        const messageEl = document.createElement('div');
        messageEl.className = `message ${type}`;
        messageEl.textContent = message;
        
        // Insert after the first prediction section
        const firstSection = document.querySelector('.prediction-section');
        firstSection.parentNode.insertBefore(messageEl, firstSection.nextSibling);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>