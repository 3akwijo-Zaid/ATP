<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Statistics</h1>
    <p>View match statistics and make predictions for aces and double faults.</p>
</div>

<div class="content-card">
    <div class="form-group">
        <label for="match-select">Select Match:</label>
        <select id="match-select" class="form-control">
            <option value="">Choose a match...</option>
        </select>
    </div>
</div>

<div id="statistics-container" style="display: none;">
    <div class="content-card">
        <div class="results-header">
            <h3 id="match-title"></h3>
            <div class="match-info" id="match-info"></div>
        </div>
    </div>

    <!-- Statistics Results Section -->
    <div class="content-card" id="statistics-results-section" style="display: none;">
        <h3>Match Statistics</h3>
        <div class="statistics-results-grid" id="statistics-results">
            <!-- Statistics results will be loaded here -->
        </div>
    </div>

    <!-- Statistics Predictions Section -->
    <div class="content-card" id="statistics-predictions-section">
        <h3>Make Statistics Predictions</h3>
        <p class="prediction-info">Predict the number of aces and double faults for each player. You can only make one prediction per player per match.</p>
        
        <div class="statistics-predictions-grid" id="statistics-predictions-grid">
            <!-- Statistics prediction inputs will be generated here -->
        </div>

        <div class="prediction-controls">
            <button type="button" class="btn btn-primary" id="submit-predictions">Submit Predictions</button>
            <button type="button" class="btn btn-secondary" id="clear-predictions">Clear Predictions</button>
        </div>

        <div class="message-container">
            <p id="message"></p>
        </div>
    </div>

    <!-- User's Previous Predictions -->
    <div class="content-card" id="user-predictions-section" style="display: none;">
        <h3>Your Statistics Predictions</h3>
        <div id="user-predictions-list">
            <!-- User's predictions will be loaded here -->
        </div>
    </div>
</div>

<style>
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

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    color: #333;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
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

.prediction-info {
    color: #666;
    font-style: italic;
    margin-bottom: 1.5rem;
}

.statistics-results-grid,
.statistics-predictions-grid {
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

.statistics-results {
    display: grid;
    gap: 1rem;
}

.stat-result {
    background: rgba(255,255,255,0.9);
    padding: 1rem;
    border-radius: 8px;
    color: #333;
    text-align: center;
}

.stat-result h5 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #333;
}

.stat-result p {
    margin: 0;
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.prediction-controls {
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

.user-prediction-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
}

.user-prediction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.user-prediction-player {
    font-weight: 600;
    color: #333;
}

.user-prediction-accuracy {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.user-prediction-stats {
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
    .statistics-results-grid,
    .statistics-predictions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .statistics-card {
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
    
    .user-prediction-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const matchSelect = document.getElementById('match-select');
    const statisticsContainer = document.getElementById('statistics-container');
    const matchTitle = document.getElementById('match-title');
    const matchInfo = document.getElementById('match-info');
    const statisticsResultsSection = document.getElementById('statistics-results-section');
    const statisticsPredictionsGrid = document.getElementById('statistics-predictions-grid');
    const submitPredictionsBtn = document.getElementById('submit-predictions');
    const clearPredictionsBtn = document.getElementById('clear-predictions');
    const messageEl = document.getElementById('message');
    const userPredictionsSection = document.getElementById('user-predictions-section');
    const userPredictionsList = document.getElementById('user-predictions-list');

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

    // Generate statistics prediction inputs
    function generateStatisticsPredictions(match) {
        statisticsPredictionsGrid.innerHTML = '';
        
        // Player 1 predictions card
        const player1Card = document.createElement('div');
        player1Card.className = 'statistics-card';
        player1Card.innerHTML = `
            <h4>${match.player1_name} Predictions</h4>
            <div class="statistics-inputs">
                <div class="input-group">
                    <label>Aces:</label>
                    <input type="number" id="aces-predicted-player1" min="0" placeholder="Predict aces count">
                </div>
                <div class="input-group">
                    <label>Double Faults:</label>
                    <input type="number" id="double-faults-predicted-player1" min="0" placeholder="Predict double faults count">
                </div>
            </div>
        `;
        statisticsPredictionsGrid.appendChild(player1Card);

        // Player 2 predictions card
        const player2Card = document.createElement('div');
        player2Card.className = 'statistics-card';
        player2Card.innerHTML = `
            <h4>${match.player2_name} Predictions</h4>
            <div class="statistics-inputs">
                <div class="input-group">
                    <label>Aces:</label>
                    <input type="number" id="aces-predicted-player2" min="0" placeholder="Predict aces count">
                </div>
                <div class="input-group">
                    <label>Double Faults:</label>
                    <input type="number" id="double-faults-predicted-player2" min="0" placeholder="Predict double faults count">
                </div>
            </div>
        `;
        statisticsPredictionsGrid.appendChild(player2Card);
    }

    // Load statistics results
    async function loadStatisticsResults(matchId) {
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}&results=1`);
            const data = await response.json();
            
            if (data.success && data.results && data.results.length > 0) {
                const resultsGrid = document.getElementById('statistics-results');
                resultsGrid.innerHTML = '';
                
                data.results.forEach(result => {
                    const resultCard = document.createElement('div');
                    resultCard.className = 'statistics-card';
                    resultCard.innerHTML = `
                        <h4>${result.player_type === 'player1' ? currentMatch.player1_name : currentMatch.player2_name} Statistics</h4>
                        <div class="statistics-results">
                            <div class="stat-result">
                                <h5>Aces</h5>
                                <p>${result.aces_actual}</p>
                            </div>
                            <div class="stat-result">
                                <h5>Double Faults</h5>
                                <p>${result.double_faults_actual}</p>
                            </div>
                        </div>
                    `;
                    resultsGrid.appendChild(resultCard);
                });
                
                statisticsResultsSection.style.display = 'block';
            } else {
                statisticsResultsSection.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading statistics results:', error);
            statisticsResultsSection.style.display = 'none';
        }
    }

    // Load user predictions
    async function loadUserPredictions(matchId) {
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}&user_predictions=1`);
            const data = await response.json();
            
            if (data.success && data.predictions && data.predictions.length > 0) {
                userPredictionsList.innerHTML = '';
                
                data.predictions.forEach(pred => {
                    const predictionItem = document.createElement('div');
                    predictionItem.className = 'user-prediction-item';
                    
                    const accuracy = pred.correct ? 'Correct' : 'Incorrect';
                    const accuracyClass = pred.correct ? 'correct' : 'incorrect';
                    
                    predictionItem.innerHTML = `
                        <div class="user-prediction-header">
                            <span class="user-prediction-player">${pred.player_type === 'player1' ? currentMatch.player1_name : currentMatch.player2_name}</span>
                            <span class="user-prediction-accuracy">${accuracy}</span>
                        </div>
                        <div class="user-prediction-stats">
                            <div class="stat-prediction ${accuracyClass}">
                                Aces: ${pred.aces_predicted}
                            </div>
                            <div class="stat-prediction ${accuracyClass}">
                                Double Faults: ${pred.double_faults_predicted}
                            </div>
                        </div>
                    `;
                    
                    userPredictionsList.appendChild(predictionItem);
                });
                
                userPredictionsSection.style.display = 'block';
            } else {
                userPredictionsSection.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading user predictions:', error);
            userPredictionsSection.style.display = 'none';
        }
    }

    // Handle match selection
    matchSelect.addEventListener('change', async function() {
        const matchId = this.value;
        
        if (!matchId) {
            statisticsContainer.style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`../api/matches.php?id=${matchId}`);
            currentMatch = await response.json();
            
            if (currentMatch) {
                matchTitle.textContent = `${currentMatch.player1_name} vs ${currentMatch.player2_name}`;
                matchInfo.textContent = `${currentMatch.round} - ${new Date(currentMatch.start_time).toLocaleDateString()}`;
                
                generateStatisticsPredictions(currentMatch);
                await loadStatisticsResults(matchId);
                await loadUserPredictions(matchId);
                
                statisticsContainer.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading match:', error);
        }
    });

    // Submit predictions
    submitPredictionsBtn.addEventListener('click', async function() {
        if (!currentMatch) return;
        
        const player1Aces = document.getElementById('aces-predicted-player1').value;
        const player1DoubleFaults = document.getElementById('double-faults-predicted-player1').value;
        const player2Aces = document.getElementById('aces-predicted-player2').value;
        const player2DoubleFaults = document.getElementById('double-faults-predicted-player2').value;
        
        if (!player1Aces || !player1DoubleFaults || !player2Aces || !player2DoubleFaults) {
            messageEl.textContent = 'Please fill in all prediction fields.';
            messageEl.style.color = '#f44336';
            return;
        }
        
        // Submit player 1 predictions
        try {
            const response1 = await fetch('../api/statistics_predictions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: currentMatch.id,
                    player_type: 'player1',
                    aces_predicted: parseInt(player1Aces),
                    double_faults_predicted: parseInt(player1DoubleFaults)
                })
            });
            
            const response2 = await fetch('../api/statistics_predictions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: currentMatch.id,
                    player_type: 'player2',
                    aces_predicted: parseInt(player2Aces),
                    double_faults_predicted: parseInt(player2DoubleFaults)
                })
            });
            
            const data1 = await response1.json();
            const data2 = await response2.json();
            
            if (data1.success && data2.success) {
                messageEl.textContent = 'Statistics predictions submitted successfully!';
                messageEl.style.color = '#4CAF50';
                
                // Reload user predictions
                await loadUserPredictions(currentMatch.id);
            } else {
                const error = data1.message || data2.message || 'Failed to submit some predictions.';
                messageEl.textContent = error;
                messageEl.style.color = '#ff9800';
            }
        } catch (error) {
            console.error('Error submitting predictions:', error);
            messageEl.textContent = 'Error submitting predictions.';
            messageEl.style.color = '#f44336';
        }
    });

    // Clear predictions
    clearPredictionsBtn.addEventListener('click', async function() {
        if (!currentMatch) return;
        
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${currentMatch.id}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            if (data.success) {
                messageEl.textContent = 'Predictions cleared successfully!';
                messageEl.style.color = '#4CAF50';
                
                // Clear form inputs
                document.getElementById('aces-predicted-player1').value = '';
                document.getElementById('double-faults-predicted-player1').value = '';
                document.getElementById('aces-predicted-player2').value = '';
                document.getElementById('double-faults-predicted-player2').value = '';
                
                // Hide user predictions section
                userPredictionsSection.style.display = 'none';
            } else {
                messageEl.textContent = data.message || 'Failed to clear predictions.';
                messageEl.style.color = '#f44336';
            }
        } catch (error) {
            console.error('Error clearing predictions:', error);
            messageEl.textContent = 'Error clearing predictions.';
            messageEl.style.color = '#f44336';
        }
    });

    // Initialize
    loadMatches();
});
</script>

<?php require_once 'includes/footer.php'; ?> 