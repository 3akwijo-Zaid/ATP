<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Match Results & Statistics</h1>
    <p>Update match results, manage statistics, and view user predictions. For detailed game-by-game results, use the Game Results page.</p>
</div>

<div class="content-card">
    <div class="form-group">
        <label for="match-select">Select a Match to Update</label>
        <select id="match-select">
            <option value="">-- Select Match --</option>
        </select>
    </div>

    <form id="update-result-form" style="display:none;">
        <h3 id="form-header"></h3>
        <input type="hidden" id="match_id">
        <input type="hidden" id="player1_name">
        <input type="hidden" id="player2_name">
        <input type="hidden" id="match_format">
        
        <div class="form-group">
            <label for="winner">Winner</label>
            <select id="winner" class="form-control"></select>
        </div>

        <div id="sets-inputs"></div>

        <button type="submit" class="btn">Update Result & Calculate Points</button>
        <div class="list-item">
            <p id="message"></p>
        </div>
    </form>
</div>

<!-- User Predictions Section -->
<div id="predictions-section" class="content-card" style="display: none;">
    <div class="predictions-header">
        <h3>User Predictions for This Match</h3>
        <button type="button" class="btn btn-secondary" id="refresh-predictions">Refresh Predictions</button>
    </div>
    <div id="predictions-list">
        <!-- Predictions will be loaded here -->
    </div>
</div>

<!-- Statistics Management Section -->
<div id="statistics-section" class="content-card" style="display: none;">
    <div class="statistics-header">
        <h3>Statistics Management</h3>
        <div class="statistics-controls">
            <button type="button" class="btn btn-primary" id="save-statistics">Save Statistics</button>
            <button type="button" class="btn btn-secondary" id="calculate-statistics-points">Calculate Points</button>
            <button type="button" class="btn btn-secondary" id="refresh-statistics">Refresh</button>
        </div>
    </div>
    
    <div class="statistics-form">
        <div class="statistics-grid" id="statistics-grid">
            <!-- Statistics inputs will be generated here -->
        </div>
    </div>
    
    <div class="statistics-message">
        <p id="statistics-message"></p>
    </div>
</div>

<!-- Statistics Predictions Section -->
<div id="statistics-predictions-section" class="content-card" style="display: none;">
    <div class="predictions-header">
        <h3>User Statistics Predictions</h3>
        <button type="button" class="btn btn-secondary" id="refresh-statistics-predictions">Refresh</button>
    </div>
    <div id="statistics-predictions-list">
        <!-- Statistics predictions will be loaded here -->
    </div>
</div>

<!-- Game Results Section -->
<div class="content-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
    <h3 style="margin-top: 0; margin-bottom: 1rem; color: white;">Detailed Game Management</h3>
    <p style="margin-bottom: 1.5rem; color: rgba(255,255,255,0.9);">For detailed game-by-game results, prediction accuracy, and Set 1 completion tracking:</p>
    <a href="game_results.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s ease;">Go to Game Results</a>
</div>

<style>
.btn {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.btn:hover {
    background: #0056b3;
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

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

#form-header {
    color: #333;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    color: #333;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

select.form-control option {
    color: #333;
    background: white;
}

select.form-control {
    color: #333;
    background: white;
}

.list-item {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 5px;
    color: #333;
}

.list-item p {
    margin: 0;
    color: #333;
    font-weight: 500;
}

/* Predictions Section Styles */
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
}

.predictions-header .btn {
    padding: 8px 16px;
    font-size: 0.9rem;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.prediction-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #007bff;
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

/* Statistics Section Styles */
.statistics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.statistics-header h3 {
    color: #333;
    margin: 0;
    font-size: 1.5rem;
}

.statistics-controls {
    display: flex;
    gap: 0.5rem;
}

.statistics-controls .btn {
    padding: 8px 16px;
    font-size: 0.9rem;
}

.statistics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.statistics-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.statistics-card h4 {
    margin: 0 0 1.5rem 0;
    font-size: 1.5rem;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    color: white;
}

.statistics-inputs {
    display: grid;
    gap: 1.5rem;
}

.input-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.input-group label {
    font-weight: 600;
    min-width: 120px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    color: white;
}

.input-group input {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.9);
    font-size: 1rem;
    color: #333;
    text-align: center;
}

.input-group input:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.statistics-message {
    text-align: center;
    padding: 1rem;
    border-radius: 10px;
    background: rgba(0,123,255,0.1);
    margin-top: 1rem;
}

.statistics-message p {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.statistics-prediction-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #4facfe;
}

.statistics-prediction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.statistics-prediction-user {
    font-weight: 600;
    color: #333;
}

.statistics-prediction-accuracy {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    font-size: 0.9rem;
}

.statistics-prediction-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.stat-prediction {
    background: white;
    padding: 0.5rem;
    border-radius: 5px;
    text-align: center;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
}

.stat-prediction.correct {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.stat-prediction.incorrect {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

@media (max-width: 768px) {
    .predictions-header,
    .statistics-header {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }
    
    .predictions-header .btn,
    .statistics-controls {
        width: 100%;
        max-width: 200px;
        flex-direction: column;
    }
    
    .prediction-header,
    .statistics-prediction-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .prediction-games,
    .statistics-prediction-stats {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.25rem;
    }
    
    .game-prediction,
    .stat-prediction {
        font-size: 0.8rem;
        padding: 0.25rem;
    }
    
    .statistics-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .statistics-card {
        padding: 1rem;
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
    const matchSelect = document.getElementById('match-select');
    const resultForm = document.getElementById('update-result-form');
    const formHeader = document.getElementById('form-header');
    const winnerSelect = document.getElementById('winner');
    const setsInputs = document.getElementById('sets-inputs');
    const matchIdInput = document.getElementById('match_id');
    const p1NameInput = document.getElementById('player1_name');
    const p2NameInput = document.getElementById('player2_name');
    const predictionsSection = document.getElementById('predictions-section');
    const predictionsList = document.getElementById('predictions-list');
    const refreshPredictionsBtn = document.getElementById('refresh-predictions');
    
    // Statistics elements
    const statisticsSection = document.getElementById('statistics-section');
    const statisticsGrid = document.getElementById('statistics-grid');
    const saveStatisticsBtn = document.getElementById('save-statistics');
    const calculateStatisticsPointsBtn = document.getElementById('calculate-statistics-points');
    const refreshStatisticsBtn = document.getElementById('refresh-statistics');
    const statisticsMessage = document.getElementById('statistics-message');
    const statisticsPredictionsSection = document.getElementById('statistics-predictions-section');
    const statisticsPredictionsList = document.getElementById('statistics-predictions-list');
    const refreshStatisticsPredictionsBtn = document.getElementById('refresh-statistics-predictions');

    // Fetch matches for the dropdown
    const response = await fetch('../api/matches.php');
    const matches = await response.json();
    matches.forEach(m => {
        if (m.status !== 'finished') {
            const option = new Option(`${m.player1_name} vs ${m.player2_name}`, m.id);
            matchSelect.add(option);
        }
    });

    // Show form when a match is selected
    matchSelect.addEventListener('change', async function() {
        const matchId = this.value;
        if (!matchId) {
            resultForm.style.display = 'none';
            predictionsSection.style.display = 'none';
            statisticsSection.style.display = 'none';
            statisticsPredictionsSection.style.display = 'none';
            return;
        }

        const match = matches.find(m => m.id == matchId);
        formHeader.textContent = `Result for: ${match.player1_name} vs ${match.player2_name}`;
        formHeader.style.color = '#333';
        formHeader.style.fontWeight = '700';
        matchIdInput.value = match.id;
        p1NameInput.value = match.player1_name;
        p2NameInput.value = match.player2_name;
        document.getElementById('match_format').value = match.match_format;

        // Populate winner dropdown
        winnerSelect.innerHTML = `
            <option value="${match.player1_name}">${match.player1_name}</option>
            <option value="${match.player2_name}">${match.player2_name}</option>
        `;

        // Generate set inputs
        setsInputs.innerHTML = '';
        const maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        for (let i = 1; i <= maxSets; i++) {
            setsInputs.innerHTML += `
                <fieldset style="margin-bottom:1rem; padding:1rem; border-radius:5px; border:1px solid #ddd; background: #f8f9fa;">
                    <legend style="color: #333; font-weight: 600; padding: 0 0.5rem;">Set ${i} (Fill in if this set was played)</legend>
                    <div style="display:flex; gap:1rem;">
                        <input type="number" id="set${i}_p1" placeholder="${match.player1_name} Games" min="0" style="width:50%; color: #333; background: white;" class="form-control">
                        <input type="number" id="set${i}_p2" placeholder="${match.player2_name} Games" min="0" style="width:50%; color: #333; background: white;" class="form-control">
                    </div>
                     <div style="display:flex; gap:1rem; margin-top:0.5rem">
                        <input type="number" id="set${i}_p1_tb" placeholder="${match.player1_name} Tiebreak" min="0" style="width:50%; color: #333; background: white;" class="form-control">
                        <input type="number" id="set${i}_p2_tb" placeholder="${match.player2_name} Tiebreak" min="0" style="width:50%; color: #333; background: white;" class="form-control">
                    </div>
                </fieldset>
            `;
        }
        resultForm.style.display = 'block';
        
        // Load predictions for this match
        await loadPredictions(matchId);
        predictionsSection.style.display = 'block';
        
        // Load statistics for this match
        await loadStatistics(matchId);
        statisticsSection.style.display = 'block';
        
        // Load statistics predictions for this match
        await loadStatisticsPredictions(matchId);
        statisticsPredictionsSection.style.display = 'block';
    });

    // Handle form submission
    resultForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const messageEl = document.getElementById('message');
        const matchId = matchIdInput.value;
        const winner = winnerSelect.value;
        const sets = [];
        let p1Sets = 0;
        let p2Sets = 0;
        let setsPlayed = 0;

        const maxSets = document.getElementById('match_format').value === 'best_of_3' ? 3 : 5;
        for (let i = 1; i <= maxSets; i++) {
            const p1_games = document.getElementById(`set${i}_p1`).value;
            const p2_games = document.getElementById(`set${i}_p2`).value;
            
            // Only process sets that were actually played
            if (p1_games !== '' && p2_games !== '') {
                setsPlayed++;
                if (p1_games > p2_games) p1Sets++;
                else p2Sets++;

                sets.push({
                    match_id: matchId,
                    set_number: i,
                    player1_games: p1_games,
                    player2_games: p2_games,
                    player1_tiebreak: document.getElementById(`set${i}_p1_tb`).value,
                    player2_tiebreak: document.getElementById(`set${i}_p2_tb`).value
                });
            }
        }

        // Validate that we have at least some sets played
        if (setsPlayed === 0) {
            messageEl.textContent = 'Please enter at least one set score.';
            return;
        }

        // Validate that the winner actually won based on set scores
        const actualWinner = p1Sets > p2Sets ? p1NameInput.value : p2NameInput.value;
        if (winner !== actualWinner) {
            messageEl.textContent = 'The winner does not match the set scores. Please check your data.';
            return;
        }

        const payload = {
            match_result: {
                id: matchId,
                status: 'finished',
                winner: winner,
                result_summary: `${p1Sets}-${p2Sets}`
            },
            sets: sets
        };

        const response = await fetch('../api/admin.php?action=update_result', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        messageEl.textContent = result.message;
        
        // Reload predictions after updating result
        if (result.success) {
            await loadPredictions(matchId);
        }
    });

    // Refresh predictions
    refreshPredictionsBtn.addEventListener('click', async function() {
        const matchId = matchIdInput.value;
        if (matchId) {
            await loadPredictions(matchId);
        }
    });

    // Save statistics
    saveStatisticsBtn.addEventListener('click', async function() {
        const matchId = matchIdInput.value;
        if (!matchId) return;
        
        const player1Aces = document.getElementById('aces-actual-player1').value;
        const player1DoubleFaults = document.getElementById('double-faults-actual-player1').value;
        const player2Aces = document.getElementById('aces-actual-player2').value;
        const player2DoubleFaults = document.getElementById('double-faults-actual-player2').value;
        
        if (!player1Aces || !player1DoubleFaults || !player2Aces || !player2DoubleFaults) {
            statisticsMessage.textContent = 'Please fill in all statistics fields.';
            statisticsMessage.style.color = '#f44336';
            return;
        }
        
        try {
            // Save player 1 statistics
            const response1 = await fetch('../api/statistics_predictions.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: matchId,
                    player_type: 'player1',
                    aces_actual: parseInt(player1Aces),
                    double_faults_actual: parseInt(player1DoubleFaults)
                })
            });
            
            // Save player 2 statistics
            const response2 = await fetch('../api/statistics_predictions.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: matchId,
                    player_type: 'player2',
                    aces_actual: parseInt(player2Aces),
                    double_faults_actual: parseInt(player2DoubleFaults)
                })
            });
            
            const data1 = await response1.json();
            const data2 = await response2.json();
            
            if (data1.success && data2.success) {
                statisticsMessage.textContent = 'Statistics saved successfully! Points will be calculated automatically.';
                statisticsMessage.style.color = '#4CAF50';
                
                // Reload statistics predictions
                await loadStatisticsPredictions(matchId);
            } else {
                const error = data1.message || data2.message || 'Failed to save some statistics.';
                statisticsMessage.textContent = error;
                statisticsMessage.style.color = '#ff9800';
            }
        } catch (error) {
            console.error('Error saving statistics:', error);
            statisticsMessage.textContent = 'Error saving statistics.';
            statisticsMessage.style.color = '#f44336';
        }
    });

    // Calculate statistics points
    calculateStatisticsPointsBtn.addEventListener('click', async function() {
        const matchId = matchIdInput.value;
        if (!matchId) return;
        
        try {
            const response = await fetch('../api/statistics_predictions.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'calculate_points',
                    match_id: matchId
                })
            });
            
            const data = await response.json();
            if (data.success) {
                statisticsMessage.textContent = 'Statistics points calculated successfully!';
                statisticsMessage.style.color = '#4CAF50';
                
                // Reload statistics predictions
                await loadStatisticsPredictions(matchId);
            } else {
                statisticsMessage.textContent = data.message || 'Failed to calculate points.';
                statisticsMessage.style.color = '#f44336';
            }
        } catch (error) {
            console.error('Error calculating points:', error);
            statisticsMessage.textContent = 'Error calculating points.';
            statisticsMessage.style.color = '#f44336';
        }
    });

    // Refresh statistics
    refreshStatisticsBtn.addEventListener('click', async function() {
        const matchId = matchIdInput.value;
        if (matchId) {
            await loadStatistics(matchId);
        }
    });

    // Refresh statistics predictions
    refreshStatisticsPredictionsBtn.addEventListener('click', async function() {
        const matchId = matchIdInput.value;
        if (matchId) {
            await loadStatisticsPredictions(matchId);
        }
    });

    // Load predictions function
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

    // Load statistics function
    async function loadStatistics(matchId) {
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}&results=1`);
            const data = await response.json();
            
            // Generate statistics inputs
            statisticsGrid.innerHTML = '';
            
            // Player 1 statistics card
            const player1Card = document.createElement('div');
            player1Card.className = 'statistics-card';
            player1Card.innerHTML = `
                <h4>${p1NameInput.value} Statistics</h4>
                <div class="statistics-inputs">
                    <div class="input-group">
                        <label>Aces:</label>
                        <input type="number" id="aces-actual-player1" min="0" placeholder="Enter actual aces count" value="${data.success && data.results ? data.results.find(r => r.player_type === 'player1')?.aces_actual || '' : ''}">
                    </div>
                    <div class="input-group">
                        <label>Double Faults:</label>
                        <input type="number" id="double-faults-actual-player1" min="0" placeholder="Enter actual double faults count" value="${data.success && data.results ? data.results.find(r => r.player_type === 'player1')?.double_faults_actual || '' : ''}">
                    </div>
                </div>
            `;
            statisticsGrid.appendChild(player1Card);

            // Player 2 statistics card
            const player2Card = document.createElement('div');
            player2Card.className = 'statistics-card';
            player2Card.innerHTML = `
                <h4>${p2NameInput.value} Statistics</h4>
                <div class="statistics-inputs">
                    <div class="input-group">
                        <label>Aces:</label>
                        <input type="number" id="aces-actual-player2" min="0" placeholder="Enter actual aces count" value="${data.success && data.results ? data.results.find(r => r.player_type === 'player2')?.aces_actual || '' : ''}">
                    </div>
                    <div class="input-group">
                        <label>Double Faults:</label>
                        <input type="number" id="double-faults-actual-player2" min="0" placeholder="Enter actual double faults count" value="${data.success && data.results ? data.results.find(r => r.player_type === 'player2')?.double_faults_actual || '' : ''}">
                    </div>
                </div>
            `;
            statisticsGrid.appendChild(player2Card);
            
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    // Load statistics predictions function
    async function loadStatisticsPredictions(matchId) {
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}`);
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
                
                statisticsPredictionsList.innerHTML = '';
                
                Object.entries(userPredictions).forEach(([username, predictions]) => {
                    const predictionItem = document.createElement('div');
                    predictionItem.className = 'statistics-prediction-item';
                    
                    const correctCount = predictions.filter(p => p.correct).length;
                    const accuracy = predictions.length > 0 ? Math.round((correctCount / predictions.length) * 100) : 0;
                    
                    predictionItem.innerHTML = `
                        <div class="statistics-prediction-header">
                            <span class="statistics-prediction-user">${username}</span>
                            <span class="statistics-prediction-accuracy">${accuracy}% (${correctCount}/${predictions.length})</span>
                        </div>
                        <div class="statistics-prediction-stats">
                            ${predictions.map(pred => `
                                <div class="stat-prediction ${pred.correct ? 'correct' : 'incorrect'}">
                                    ${pred.player_type === 'player1' ? p1NameInput.value : p2NameInput.value}: ${pred.aces_predicted} aces, ${pred.double_faults_predicted} double faults
                                </div>
                            `).join('')}
                        </div>
                    `;
                    
                    statisticsPredictionsList.appendChild(predictionItem);
                });
            } else {
                statisticsPredictionsList.innerHTML = `
                    <div class="statistics-prediction-item">
                        <div class="statistics-prediction-header">
                            <span class="statistics-prediction-user">No Statistics Predictions</span>
                        </div>
                        <div class="statistics-prediction-stats">
                            <div class="stat-prediction no-predictions">
                                No users have made statistics predictions for this match yet.
                            </div>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading statistics predictions:', error);
            statisticsPredictionsList.innerHTML = `
                <div class="statistics-prediction-item">
                    <div class="statistics-prediction-header">
                        <span class="statistics-prediction-user">Error</span>
                    </div>
                    <div class="statistics-prediction-stats">
                        <div class="stat-prediction error">
                            Failed to load statistics predictions. Please try again.
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Helper function to format score display
    function formatScoreDisplay(score) {
        return score.replace(/game/g, 'GAME');
    }
});
</script>