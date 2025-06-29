<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please <a href='login.php'>login</a> to make game predictions.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

if (!isset($_GET['match_id'])) {
    echo "<div class='container'><p>No match selected. Go to the <a href='index.php'>homepage</a> to pick a match.</p></div>";
    require_once 'includes/footer.php';
    exit();
}
?>

<div class="container container--full">
    <div id="match-details" class="match-header"></div>
    
    <div class="game-prediction-container">
        <div class="prediction-header">
            <h2>Set 1 - Game Predictions</h2>
            <p>Predict each game of Set 1 with exact scores</p>
        </div>
        
        <div class="games-grid" id="games-grid">
            <!-- Games will be dynamically generated here -->
        </div>
        
        <div class="prediction-controls">
            <button type="button" class="btn btn-primary" id="save-predictions">Save All Predictions</button>
            <button type="button" class="btn btn-secondary" id="clear-predictions">Clear All</button>
        </div>
        
        <div class="message-container">
            <p id="message"></p>
        </div>
    </div>
    
    <div id="lock-message" class="lock-message" style="display:none;">
        <div class="lock-icon">🔒</div>
        <p>Predictions are locked for this match</p>
    </div>
</div>

<style>
.match-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.match-header h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.game-prediction-container {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.prediction-header {
    text-align: center;
    margin-bottom: 2rem;
}

.prediction-header h2 {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
}

.prediction-header p {
    color: #666;
    font-size: 1.1rem;
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.game-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.game-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.game-card.completed {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.game-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.game-inputs {
    display: grid;
    gap: 1rem;
}

.input-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.input-group label {
    font-weight: 600;
    min-width: 80px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.input-group select,
.input-group input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.9);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.input-group select:focus,
.input-group input:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.score-input {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.score-input input {
    width: 60px;
    text-align: center;
    font-weight: bold;
}

.score-input span {
    font-weight: bold;
    font-size: 1.2rem;
}

.prediction-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 1rem;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.btn-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.message-container {
    text-align: center;
    padding: 1rem;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

#message {
    margin: 0;
    font-weight: 600;
}

.lock-message {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.lock-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.progress-line {
    position: absolute;
    background: rgba(255,255,255,0.3);
    transition: all 0.5s ease;
}

.progress-line.horizontal {
    height: 2px;
    width: 0;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
}

.progress-line.vertical {
    width: 2px;
    height: 0;
    left: 50%;
    top: 0;
    transform: translateX(-50%);
}

@media (max-width: 768px) {
    .games-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .game-card {
        padding: 1rem;
    }
    
    .prediction-controls {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .input-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .input-group label {
        min-width: auto;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const matchId = new URLSearchParams(window.location.search).get('match_id');
    const matchDetails = document.getElementById('match-details');
    const gamesGrid = document.getElementById('games-grid');
    const saveBtn = document.getElementById('save-predictions');
    const clearBtn = document.getElementById('clear-predictions');
    const messageEl = document.getElementById('message');
    const lockMessage = document.getElementById('lock-message');

    // Fetch match details
    const response = await fetch(`../api/matches.php?id=${matchId}`);
    const match = await response.json();

    if (match) {
        matchDetails.innerHTML = `<h1>${match.player1_name} vs ${match.player2_name}</h1>`;
        
        // Check if predictions are locked
        const startTime = new Date(match.start_time);
        const now = new Date();
        const timeDiff = (startTime - now) / 1000; // seconds
        
        if (timeDiff <= 3600) {
            lockMessage.style.display = 'block';
            gamesGrid.style.display = 'none';
            saveBtn.style.display = 'none';
            clearBtn.style.display = 'none';
            return;
        }
        
        // Generate game cards
        generateGameCards(match);
        
        // Load existing predictions
        await loadExistingPredictions();
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
                        <div class="score-input">
                            <select id="score1-${gameNum}" class="game-score">
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                            <span>-</span>
                            <select id="score2-${gameNum}" class="game-score">
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
                <div class="progress-line horizontal"></div>
                <div class="progress-line vertical"></div>
            `;
            
            gamesGrid.appendChild(gameCard);
        }
    }

    async function loadExistingPredictions() {
        try {
            const response = await fetch(`../api/game_predictions.php?match_id=${matchId}`);
            const data = await response.json();
            
            if (data.success && data.predictions && data.predictions.length > 0) {
                data.predictions.forEach(prediction => {
                    const winnerSelect = document.getElementById(`winner-${prediction.game_number}`);
                    const score1Select = document.getElementById(`score1-${prediction.game_number}`);
                    const score2Select = document.getElementById(`score2-${prediction.game_number}`);
                    
                    if (winnerSelect) winnerSelect.value = prediction.predicted_winner;
                    
                    const scores = prediction.predicted_score.split('-');
                    if (score1Select && scores[0]) score1Select.value = scores[0];
                    if (score2Select && scores[1]) score2Select.value = scores[1];
                });
            }
        } catch (error) {
            console.error('Error loading predictions:', error);
        }
    }

    saveBtn.addEventListener('click', async function() {
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
            messageEl.textContent = 'Please make at least one prediction before saving.';
            messageEl.style.color = '#f44336';
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
                if (result.success) {
                    savedCount++;
                }
            } catch (error) {
                console.error('Error saving prediction:', error);
            }
        }
        
        if (savedCount === predictions.length) {
            messageEl.textContent = `Successfully saved ${savedCount} game predictions!`;
            messageEl.style.color = '#4CAF50';
            
            // Add progress lines
            addProgressLines();
        } else {
            messageEl.textContent = `Saved ${savedCount} out of ${predictions.length} predictions.`;
            messageEl.style.color = '#ff9800';
        }
    });

    clearBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all predictions?')) {
            for (let gameNum = 1; gameNum <= 12; gameNum++) {
                document.getElementById(`winner-${gameNum}`).value = '';
                document.getElementById(`score1-${gameNum}`).value = '';
                document.getElementById(`score2-${gameNum}`).value = '';
            }
            messageEl.textContent = 'All predictions cleared.';
            messageEl.style.color = '#2196F3';
        }
    });

    function addProgressLines() {
        const cards = document.querySelectorAll('.game-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                const horizontalLine = card.querySelector('.progress-line.horizontal');
                const verticalLine = card.querySelector('.progress-line.vertical');
                
                if (horizontalLine) {
                    horizontalLine.style.width = '100%';
                }
                if (verticalLine) {
                    verticalLine.style.height = '100%';
                }
            }, index * 100);
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 