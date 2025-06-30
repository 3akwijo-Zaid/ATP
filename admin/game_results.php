<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Game Results Management</h1>
    <p>Add game results for Set 1, track completion, and view user predictions.</p>
</div>

<div class="content-card">
    <div class="form-group">
        <label for="match-select" style="color: white;">Select Match:</label>
        <select id="match-select" class="form-control">
            <option value="">Choose a match...</option>
        </select>
    </div>
</div>

<div id="game-results-container" style="display: none;">
    <div class="content-card">
        <div class="results-header">
            <h3 id="match-title"></h3>
            <div class="match-info" id="match-info"></div>
        </div>
    </div>

    <div class="content-card">
        <div class="game-results-grid" id="game-results-grid">
            <!-- Game result inputs will be generated here -->
        </div>

        <div class="results-controls">
            <button type="button" class="btn btn-primary" id="save-results">Save All Results</button>
            <button type="button" class="btn btn-secondary" id="calculate-points">Calculate Points</button>
        </div>

        <div class="message-container">
            <p id="message"></p>
        </div>
    </div>

    <div class="content-card">
        <div class="predictions-header">
            <h3>User Predictions</h3>
            <button type="button" class="btn btn-secondary" id="refresh-predictions">Refresh Predictions</button>
        </div>
        <div id="predictions-list">
            <!-- Predictions will be loaded here -->
        </div>
    </div>
</div>

<style>
.match-selector {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.match-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    background: rgba(255,255,255,0.9);
    color: #333;
}

.form-control:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    color: #333;
}

.input-group select option {
    color: #333;
    background: white;
}

.score-input {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    color: white;
}

.score-input input {
    width: 60px;
    text-align: center;
    font-weight: bold;
    color: #333;
    background: rgba(255,255,255,0.9);
}

.score-input span {
    font-weight: bold;
    font-size: 1.2rem;
    color: white;
}

.results-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
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
    margin-bottom: 2rem;
}

#message {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.predictions-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.predictions-section h3 {
    color: #333;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
}

.predictions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.predictions-header h3 {
    color: #333;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.predictions-header .btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    color: #333;
}

.prediction-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
}

.prediction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.prediction-user {
    font-weight: 600;
    color: #333;
}

.prediction-accuracy {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.prediction-games {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.game-prediction {
    background: white;
    padding: 0.5rem;
    border-radius: 5px;
    text-align: center;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
}

.game-prediction.correct {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.game-prediction.incorrect {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.game-prediction.no-predictions {
    background: #e2e3e5;
    border-color: #d6d8db;
    color: #6c757d;
    font-style: italic;
}

.game-prediction.error {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    font-style: italic;
}

.predictions-empty {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    font-style: italic;
}

.predictions-error {
    text-align: center;
    padding: 2rem;
    color: #721c24;
    font-style: italic;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .game-results-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .game-result-card {
        padding: 1rem;
    }
    
    .results-controls {
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
    
    .prediction-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .predictions-header {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }
    
    .predictions-header .btn {
        width: 100%;
        max-width: 200px;
    }
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.page-header h1 {
    margin: 0 0 1rem 0;
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.page-header p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
}

.content-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.results-header {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
}

.results-header h3 {
    margin: 0 0 1rem 0;
    color: #333;
    font-size: 1.8rem;
    font-weight: 700;
}

.match-info {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

.game-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.game-result-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.game-result-card h4 {
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    color: white;
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
    color: white;
}

.input-group select,
.input-group input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.9);
    font-size: 1rem;
    color: #333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const matchSelect = document.getElementById('match-select');
    const gameResultsContainer = document.getElementById('game-results-container');
    const matchTitle = document.getElementById('match-title');
    const matchInfo = document.getElementById('match-info');
    const gameResultsGrid = document.getElementById('game-results-grid');
    const saveResultsBtn = document.getElementById('save-results');
    const calculatePointsBtn = document.getElementById('calculate-points');
    const messageEl = document.getElementById('message');
    const predictionsList = document.getElementById('predictions-list');
    const refreshPredictionsBtn = document.getElementById('refresh-predictions');

    let currentMatch = null;

    // Load matches
    async function loadMatches() {
        try {
            const response = await fetch('../api/matches.php');
            const matches = await response.json();
            
            matches.forEach(match => {
                const option = document.createElement('option');
                option.value = match.id;
                option.textContent = `${match.player1_name} vs ${match.player2_name} (${match.round})`;
                matchSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading matches:', error);
        }
    }

    // Generate game result inputs
    function generateGameResults(match) {
        gameResultsGrid.innerHTML = '';
        let player1Score = 0;
        let player2Score = 0;

        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const gameCard = document.createElement('div');
            gameCard.className = 'game-result-card';
            gameCard.id = `game-result-${gameNum}`;

            gameCard.innerHTML = `
                <h4>Game ${gameNum}</h4>
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
                            <select id="score1-${gameNum}" class="game-score" disabled>
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                            <span>-</span>
                            <select id="score2-${gameNum}" class="game-score" disabled>
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

            gameResultsGrid.appendChild(gameCard);
        }

        // Add event listeners for winner selection to update scores accordingly
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
                // Only reveal if neither player has 'game' in the current game
                const currentGameCard = document.getElementById(`game-result-${gameNum}`);
                const nextGameCard = document.getElementById(`game-result-${gameNum+1}`);
                // If either player has 'game', stop revealing further cards
                if (score1Select.value === 'game' || score2Select.value === 'game') {
                    // Optionally, you could disable all further winner selects here
                    // for (let i = gameNum+1; i <= 12; i++) {
                    //   const w = document.getElementById(`winner-${i}`);
                    //   if (w) w.disabled = true;
                    // }
                } else if (nextGameCard) {
                    nextGameCard.style.display = '';
                }
            });
        }
    }

    // Load existing game results
    async function loadGameResults(matchId) {
        try {
            const response = await fetch(`../api/game_predictions.php?match_id=${matchId}&set_completion=1`);
            const data = await response.json();
            
            if (data.success && data.set_completion) {
                // Load game results from a separate endpoint or use the game_results table
                const resultsResponse = await fetch(`../api/game_predictions.php?match_id=${matchId}&results=1`);
                const resultsData = await resultsResponse.json();
                
                if (resultsData.success && resultsData.results) {
                    resultsData.results.forEach(result => {
                        const winnerSelect = document.getElementById(`winner-${result.game_number}`);
                        const score1Select = document.getElementById(`score1-${result.game_number}`);
                        const score2Select = document.getElementById(`score2-${result.game_number}`);
                        
                        if (winnerSelect) winnerSelect.value = result.winner;
                        
                        const scores = result.final_score.split('-');
                        if (score1Select && scores[0]) score1Select.value = scores[0];
                        if (score2Select && scores[1]) score2Select.value = scores[1];
                    });
                }
            }
        } catch (error) {
            console.error('Error loading game results:', error);
        }
    }

    // Load predictions
    async function loadPredictions(matchId) {
        // Show loading state
        predictionsList.innerHTML = `
            <div class="prediction-item">
                <div class="prediction-header">
                    <span class="prediction-user">Loading...</span>
                </div>
                <div class="prediction-games">
                    <div class="game-prediction no-predictions">
                        Loading predictions...
                    </div>
                </div>
            </div>
        `;
        
        try {
            const response = await fetch(`../api/game_predictions.php?match_id=${matchId}`);
            const data = await response.json();
            
            if (data.success && data.predictions && data.predictions.length > 0) {
                // Group predictions by user
                const userPredictions = {};
                data.predictions.forEach(pred => {
                    if (!userPredictions[pred.username]) {
                        userPredictions[pred.username] = [];
                    }
                    userPredictions[pred.username].push(pred);
                });
                
                predictionsList.innerHTML = '';
                
                Object.entries(userPredictions).forEach(([username, predictions]) => {
                    const predictionItem = document.createElement('div');
                    predictionItem.className = 'prediction-item';
                    
                    const correctCount = predictions.filter(p => p.correct).length;
                    const accuracy = predictions.length > 0 ? Math.round((correctCount / predictions.length) * 100) : 0;
                    
                    predictionItem.innerHTML = `
                        <div class="prediction-header">
                            <span class="prediction-user">${username}</span>
                            <span class="prediction-accuracy">${accuracy}% (${correctCount}/${predictions.length})</span>
                        </div>
                        <div class="prediction-games">
                            ${predictions.map(pred => `
                                <div class="game-prediction ${pred.correct ? 'correct' : 'incorrect'}">
                                    Game ${pred.game_number}: ${formatScoreDisplay(pred.predicted_score)}
                                </div>
                            `).join('')}
                        </div>
                    `;
                    
                    predictionsList.appendChild(predictionItem);
                });
            } else {
                // Show message when no predictions exist
                predictionsList.innerHTML = `
                    <div class="prediction-item">
                        <div class="prediction-header">
                            <span class="prediction-user">No Predictions</span>
                        </div>
                        <div class="prediction-games">
                            <div class="game-prediction no-predictions">
                                No users have made predictions for this match yet.
                            </div>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading predictions:', error);
            predictionsList.innerHTML = `
                <div class="prediction-item">
                    <div class="prediction-header">
                        <span class="prediction-user">Error</span>
                    </div>
                    <div class="prediction-games">
                        <div class="game-prediction error">
                            Failed to load predictions. Please try again.
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Handle match selection
    matchSelect.addEventListener('change', async function() {
        const matchId = this.value;
        
        if (!matchId) {
            gameResultsContainer.style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`../api/matches.php?id=${matchId}`);
            currentMatch = await response.json();
            
            if (currentMatch) {
                matchTitle.textContent = `${currentMatch.player1_name} vs ${currentMatch.player2_name}`;
                matchInfo.textContent = `${currentMatch.round} - ${new Date(currentMatch.start_time).toLocaleDateString()}`;
                
                generateGameResults(currentMatch);
                await loadGameResults(matchId);
                await loadPredictions(matchId);
                
                gameResultsContainer.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading match:', error);
        }
    });

    // Save results
    saveResultsBtn.addEventListener('click', async function() {
        if (!currentMatch) return;
        
        const results = [];
        
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winner = document.getElementById(`winner-${gameNum}`).value;
            const score1 = document.getElementById(`score1-${gameNum}`).value;
            const score2 = document.getElementById(`score2-${gameNum}`).value;
            const duration = document.getElementById(`duration-${gameNum}`).value;
            
            if (winner && score1 && score2) {
                results.push({
                    match_id: currentMatch.id,
                    game_number: gameNum,
                    winner: winner,
                    final_score: `${score1}-${score2}`,
                    game_duration: duration || null
                });
            }
        }
        
        if (results.length === 0) {
            messageEl.textContent = 'Please fill in at least one game result.';
            messageEl.style.color = '#f44336';
            return;
        }
        
        // Save results
        let savedCount = 0;
        for (const result of results) {
            try {
                const response = await fetch('../api/game_predictions.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(result)
                });
                
                const data = await response.json();
                if (data.success) {
                    savedCount++;
                }
            } catch (error) {
                console.error('Error saving result:', error);
            }
        }
        
        if (savedCount === results.length) {
            messageEl.textContent = `Successfully saved ${savedCount} game results!`;
            messageEl.style.color = '#4CAF50';
            
            // Reload predictions to show updated accuracy
            await loadPredictions(currentMatch.id);
        } else {
            messageEl.textContent = `Saved ${savedCount} out of ${results.length} results.`;
            messageEl.style.color = '#ff9800';
        }
    });

    // Calculate points
    calculatePointsBtn.addEventListener('click', async function() {
        if (!currentMatch) return;
        
        try {
            const response = await fetch('../api/game_predictions.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'calculate_points',
                    match_id: currentMatch.id
                })
            });
            
            const data = await response.json();
            if (data.success) {
                messageEl.textContent = 'Points calculated successfully!';
                messageEl.style.color = '#4CAF50';
                
                // Reload predictions to show updated accuracy
                await loadPredictions(currentMatch.id);
            } else {
                messageEl.textContent = 'Failed to calculate points.';
                messageEl.style.color = '#f44336';
            }
        } catch (error) {
            console.error('Error calculating points:', error);
            messageEl.textContent = 'Error calculating points.';
            messageEl.style.color = '#f44336';
        }
    });

    // Refresh predictions
    refreshPredictionsBtn.addEventListener('click', async function() {
        if (!currentMatch) return;
        
        await loadPredictions(currentMatch.id);
    });

    // Helper function to format score display
    function formatScoreDisplay(score) {
        return score.replace(/game/g, 'GAME');
    }

    // Initialize
    loadMatches();
});
</script>

<?php require_once 'includes/footer.php'; ?> 