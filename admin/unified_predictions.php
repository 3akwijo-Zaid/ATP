
<?php
// Add to all pages
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>Unified Predictions Management</h1>
    <p>Manage all types of predictions for any match from a single interface.</p>
</div>

<div class="content-card">
    <label for="match-select">Select a Match:</label>
    <select id="match-select" class="form-control">
        <option value="">-- Select Match --</option>
        <!-- Options will be loaded dynamically -->
    </select>
    <div id="match-status" style="margin-top: 0.5rem; color: #666;"></div>
</div>

<div class="content-card">
    <div class="tabs">
        <button class="tab-btn active" data-tab="match-result">Match Result</button>
        <button class="tab-btn" data-tab="game-by-game">Game-by-Game</button>
        <button class="tab-btn" data-tab="statistics">Statistics</button>
    </div>
    <div class="tab-content" id="tab-match-result">
        <!-- Match Result Prediction Form -->
        <h3>Match Result Prediction</h3>
        <div id="match-result-form-area">
            <!-- Form will be loaded here -->
        </div>
        <div class="feedback-message" id="match-result-message"></div>
        <div id="match-result-predictions-list">
            <!-- User predictions will be shown here -->
        </div>
    </div>
    <div class="tab-content" id="tab-game-by-game" style="display:none;">
        <!-- Game-by-Game Prediction Form -->
        <h3>Game-by-Game Prediction</h3>
        <div id="game-by-game-form-area">
            <!-- Form will be loaded here -->
        </div>
        <div class="feedback-message" id="game-by-game-message"></div>
        <div id="game-by-game-predictions-list">
            <!-- User predictions will be shown here -->
        </div>
    </div>
    <div class="tab-content" id="tab-statistics" style="display:none;">
        <!-- Statistics Prediction Form -->
        <h3>Statistics Prediction</h3>
        <div id="statistics-form-area">
            <!-- Form will be loaded here -->
        </div>
        <div class="feedback-message" id="statistics-message"></div>
        <div id="statistics-predictions-list">
            <!-- User predictions will be shown here -->
        </div>
    </div>
</div>

<style>
body {
    background: #f4f6fb;
    color: #222;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}
.page-header h1 {
    margin: 0 0 1rem 0;
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
.page-header p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.95;
    font-weight: 500;
}
.content-card {
    background: #fff;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 24px rgba(102,126,234,0.07);
    color: #222;
}
.content-card, .content-card * {
    color: #222 !important;
}
label, legend {
    color: #333 !important;
    font-weight: 600;
}
.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #e0e3ea;
    border-radius: 8px;
    font-size: 1rem;
    background: #f8f9fb;
    color: #222;
    margin-bottom: 0.5rem;
    transition: border 0.2s;
}
.form-control:focus {
    outline: none;
    border-color: #667eea;
    background: #fff;
    color: #222;
}
.tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
.tab-btn {
    padding: 12px 32px;
    border: none;
    border-radius: 12px 12px 0 0;
    background: #f4f6fb;
    color: #667eea;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    box-shadow: 0 2px 8px rgba(102,126,234,0.04);
}
.tab-btn.active {
    background: #667eea;
    color: #fff;
}
.tab-content {
    padding: 2rem 1rem 1rem 1rem;
    background: #fff;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 4px 16px rgba(102,126,234,0.06);
    min-height: 200px;
    color: #222;
}
.feedback-message {
    margin: 1rem 0;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fb;
    color: #222;
    font-weight: 500;
    display: none;
    box-shadow: 0 2px 8px rgba(102,126,234,0.03);
}
.prediction-list-card {
    background: #f8f9fb;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    border-left: 5px solid #667eea;
    box-shadow: 0 2px 8px rgba(102,126,234,0.04);
    color: #222;
}
.prediction-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}
.prediction-list-user {
    font-weight: 700;
    color: #333;
}
.prediction-list-accuracy {
    background: #28a745;
    color: #fff;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(40,167,69,0.08);
}
fieldset {
    border: 1px solid #e0e3ea;
    border-radius: 10px;
    background: #f8f9fb;
    margin-bottom: 1.5rem;
    color: #222;
}
legend {
    color: #667eea;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0 0.5rem;
}
button, .btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff !important;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(102,126,234,0.08);
    margin-top: 0.5rem;
}
button:hover, .btn:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: #fff;
}
input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}
/* Fix inline styles for set/tiebreak inputs */
input[placeholder$='Games'], input[placeholder$='Tiebreak'] {
    background: #f8f9fb !important;
    color: #222 !important;
}
@media (max-width: 900px) {
    .content-card {
        padding: 1rem;
    }
    .tab-content {
        padding: 1rem 0.5rem 0.5rem 0.5rem;
    }
}
@media (max-width: 600px) {
    .tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    .tab-btn {
        width: 100%;
        border-radius: 8px;
    }
    .content-card {
        padding: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(tc => tc.style.display = 'none');
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).style.display = '';
        });
    });

    // --- Unified Match Fetching ---
    const matchSelect = document.getElementById('match-select');
    const matchStatus = document.getElementById('match-status');
    let matches = [];
    let selectedMatch = null;

    async function loadMatches() {
        try {
            const response = await fetch('../api/matches.php');
            matches = await response.json();
            matchSelect.innerHTML = '<option value="">-- Select Match --</option>';
            matches.forEach(m => {
                const statusLabel = m.status ? ` [${m.status}]` : '';
                const option = document.createElement('option');
                option.value = m.id;
                option.textContent = `${m.player1_name} vs ${m.player2_name}${statusLabel}`;
                matchSelect.appendChild(option);
            });
        } catch (e) {
            matchSelect.innerHTML = '<option value="">Failed to load matches</option>';
        }
    }

    matchSelect.addEventListener('change', function() {
        const matchId = this.value;
        selectedMatch = matches.find(m => m.id == matchId);

        matchStatus.textContent = selectedMatch ? `Status: ${selectedMatch.status}` : '';
        if (selectedMatch) {
            renderMatchResultForm(selectedMatch);
            loadMatchResultPredictions(selectedMatch.id);
            renderGameByGameForm(selectedMatch);
            loadGameByGamePredictions(selectedMatch.id);
            renderStatisticsForm(selectedMatch);
            loadStatisticsPredictions(selectedMatch.id);
        } else {
            document.getElementById('match-result-form-area').innerHTML = '';
            document.getElementById('match-result-predictions-list').innerHTML = '';
            document.getElementById('game-by-game-form-area').innerHTML = '';
            document.getElementById('game-by-game-predictions-list').innerHTML = '';
            document.getElementById('statistics-form-area').innerHTML = '';
            document.getElementById('statistics-predictions-list').innerHTML = '';
        }
    });

    // --- Match Result Prediction Form ---
    function renderMatchResultForm(match) {
        const formArea = document.getElementById('match-result-form-area');
        // Determine number of sets
        const maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        let setsHtml = '';
        for (let i = 1; i <= maxSets; i++) {
            setsHtml += `
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
        formArea.innerHTML = `
            <form id="match-result-form">
                <div class="form-group">
                    <label for="result-type-select">Result Type</label>
                    <select id="result-type-select" class="form-control" required>
                        <option value="normal">Normal</option>
                        <option value="retired_player1">Player 1 retired</option>
                        <option value="retired_player2">Player 2 retired</option>
                    </select>
                </div>
                <div class="form-group" id="winner-group">
                    <label for="winner-select">Winner</label>
                    <select id="winner-select" class="form-control" required>
                        <option value="">-- Select Winner --</option>
                        <option value="${match.player1_name}">${match.player1_name}</option>
                        <option value="${match.player2_name}">${match.player2_name}</option>
                    </select>
                </div>
                <div class="form-group" id="sets-group">
                    <label>Set Scores</label>
                    ${setsHtml}
                </div>
                <button type="submit" class="btn btn-primary">Save Result</button>
            </form>
        `;
        // Retirement logic: hide winner/sets if retired
        const resultTypeSelect = document.getElementById('result-type-select');
        const winnerGroup = document.getElementById('winner-group');
        const setsGroup = document.getElementById('sets-group');
        resultTypeSelect.addEventListener('change', function() {
            if (this.value === 'retired_player1' || this.value === 'retired_player2') {
                winnerGroup.style.display = '';
                setsGroup.style.display = '';
                document.getElementById('winner-select').setAttribute('required', 'required');
                for (let i = 1; i <= maxSets; i++) {
                    document.getElementById(`set${i}_p1`).removeAttribute('required');
                    document.getElementById(`set${i}_p2`).removeAttribute('required');
                    document.getElementById(`set${i}_p1_tb`).removeAttribute('required');
                    document.getElementById(`set${i}_p2_tb`).removeAttribute('required');
                }
            } else {
                winnerGroup.style.display = '';
                setsGroup.style.display = '';
                document.getElementById('winner-select').setAttribute('required', 'required');
                for (let i = 1; i <= maxSets; i++) {
                    document.getElementById(`set${i}_p1`).setAttribute('required', 'required');
                    document.getElementById(`set${i}_p2`).setAttribute('required', 'required');
                    document.getElementById(`set${i}_p1_tb`).setAttribute('required', 'required');
                    document.getElementById(`set${i}_p2_tb`).setAttribute('required', 'required');
                }
            }
        });
        const form = document.getElementById('match-result-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const resultType = document.getElementById('result-type-select').value;
            const winnerName = document.getElementById('winner-select').value;
            const messageEl = document.getElementById('match-result-message');
            messageEl.style.display = 'block';
            if (resultType === 'retired_player1' || resultType === 'retired_player2') {
                // Retirement: require winner, allow any number of sets
                let winnerId = null;
                if (winnerName === match.player1_name) winnerId = Number(match.player1_id);
                if (winnerName === match.player2_name) winnerId = Number(match.player2_id);
                if (!winnerId) {
                    messageEl.textContent = 'Please select the winner (the player who did NOT retire).';
                    messageEl.style.background = '#ffe0e0';
                    return;
                }
                // Collect sets played so far
                let sets = [];
                let p1Sets = 0;
                let p2Sets = 0;
                for (let i = 1; i <= maxSets; i++) {
                    const p1_games = document.getElementById(`set${i}_p1`).value;
                    const p2_games = document.getElementById(`set${i}_p2`).value;
                    const p1_tb = document.getElementById(`set${i}_p1_tb`).value;
                    const p2_tb = document.getElementById(`set${i}_p2_tb`).value;
                    if (p1_games !== '' && p2_games !== '') {
                        if (parseInt(p1_games) > parseInt(p2_games)) p1Sets++;
                        else if (parseInt(p2_games) > parseInt(p1_games)) p2Sets++;
                        sets.push({
                            match_id: match.id,
                            set_number: i,
                            player1_games: p1_games,
                            player2_games: p2_games,
                            player1_tiebreak: p1_tb,
                            player2_tiebreak: p2_tb
                        });
                    }
                }
                if (sets.length === 0) {
                    messageEl.textContent = 'Please enter at least one set score.';
                    messageEl.style.background = '#ffe0e0';
                    return;
                }
                // Build result summary from sets played
                const resultSummary = `${p1Sets}-${p2Sets} (retired)`;
                const payload = {
                    match_result: {
                        id: match.id,
                        status: resultType,
                        winner: winnerId,
                        result_summary: resultSummary
                    },
                    sets: sets
                };
                try {
                    const response = await fetch('../api/admin.php?action=update_result', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();
                    messageEl.textContent = result.message || (result.success ? 'Result saved!' : 'Failed to save result.');
                    messageEl.style.background = result.success ? '#e0ffe0' : '#ffe0e0';
                    if (result.success) {
                        loadMatchResultPredictions(match.id);
                    }
                } catch (err) {
                    messageEl.textContent = 'Error saving result.';
                    messageEl.style.background = '#ffe0e0';
                }
                return;
            }
            let sets = [];
            let p1Sets = 0;
            let p2Sets = 0;
            let setsPlayed = 0;
            for (let i = 1; i <= maxSets; i++) {
                const p1_games = document.getElementById(`set${i}_p1`).value;
                const p2_games = document.getElementById(`set${i}_p2`).value;
                const p1_tb = document.getElementById(`set${i}_p1_tb`).value;
                const p2_tb = document.getElementById(`set${i}_p2_tb`).value;
                if (p1_games !== '' && p2_games !== '') {
                    setsPlayed++;
                    if (parseInt(p1_games) > parseInt(p2_games)) p1Sets++;
                    else if (parseInt(p2_games) > parseInt(p1_games)) p2Sets++;
                    sets.push({
                        match_id: match.id,
                        set_number: i,
                        player1_games: p1_games,
                        player2_games: p2_games,
                        player1_tiebreak: p1_tb,
                        player2_tiebreak: p2_tb
                    });
                }
            }
            if (setsPlayed === 0) {
                messageEl.textContent = 'Please enter at least one set score.';
                messageEl.style.background = '#ffe0e0';
                return;
            }
            // Map winner name to player ID (as number)
            let winnerId = null;
            if (winnerName === match.player1_name) winnerId = Number(match.player1_id);
            if (winnerName === match.player2_name) winnerId = Number(match.player2_id);

            // Validate winner
            const actualWinner = p1Sets > p2Sets ? match.player1_name : match.player2_name;
            if (winnerName !== actualWinner) {
                messageEl.textContent = 'Winner does not match the set scores. Please check your data.';
                messageEl.style.background = '#ffe0e0';
                return;
            }
            const payload = {
                match_result: {
                    id: match.id,
                    status: 'finished',
                    winner: winnerId,
                    result_summary: `${p1Sets}-${p2Sets}`
                },
                sets: sets
            };
            try {
                const response = await fetch('../api/admin.php?action=update_result', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                messageEl.textContent = result.message || (result.success ? 'Result saved!' : 'Failed to save result.');
                messageEl.style.background = result.success ? '#e0ffe0' : '#ffe0e0';
                if (result.success) {
                    loadMatchResultPredictions(match.id);
                }
            } catch (err) {
                messageEl.textContent = 'Error saving result.';
                messageEl.style.background = '#ffe0e0';
            }
        });
    }

    // Helper to format set scores for match result predictions
    function formatSetScores(sets) {
        if (!Array.isArray(sets)) return '';
        return sets.map((set, i) => {
            if (set.player1 !== undefined && set.player2 !== undefined) {
                return `Set ${i+1}: ${set.player1}-${set.player2}`;
            } else if (set.player1_games !== undefined && set.player2_games !== undefined) {
                return `Set ${i+1}: ${set.player1_games}-${set.player2_games}`;
            }
            return '';
        }).join(' | ');
    }

    // Helper to get player name from 'player1'/'player2'
    function getPlayerNameFromKey(match, key) {
        if (!match) return key;
        if (key === 'player1') return match.player1_name;
        if (key === 'player2') return match.player2_name;
        return key;
    }

    // --- Unified Prediction List Display ---
    async function loadMatchResultPredictions(matchId) {
        const listArea = document.getElementById('match-result-predictions-list');
        listArea.innerHTML = '<div>Loading predictions...</div>';
        // Show user's own prediction above the list
        const formArea = document.getElementById('match-result-form-area');
        let userPredictionCard = null;
        try {
            // Fetch all predictions
            const response = await fetch(`../api/predictions.php?match_id=${matchId}`);
            const data = await response.json();
            // Fetch user's own prediction
            const userResponse = await fetch(`../api/predictions.php?match_id=${matchId}&user_id=me`);
            const userData = await userResponse.json();
            // Find the match object for player name mapping
            const match = (window.matches || []).find(m => m.id == matchId);
            // Render user's own prediction if available
            if (userData && userData.success && userData.prediction) {
                const pred = userData.prediction;
                const winnerName = getPlayerNameFromKey(match, pred.prediction_data?.winner);
                // Determine correctness if not provided
                let correctLabel = 'Incorrect';
                if (typeof pred.correct !== 'undefined') {
                    correctLabel = pred.correct ? 'Correct' : 'Incorrect';
                } else if (match && match.winner_id && pred.prediction_data?.winner) {
                    let predictedId = null;
                    if (pred.prediction_data.winner === 'player1') predictedId = match.player1_id;
                    if (pred.prediction_data.winner === 'player2') predictedId = match.player2_id;
                    correctLabel = (predictedId && predictedId == match.winner_id) ? 'Correct' : 'Incorrect';
                }
                userPredictionCard = document.createElement('div');
                userPredictionCard.className = 'prediction-list-card';
                userPredictionCard.style.borderLeft = '5px solid #28a745';
                userPredictionCard.innerHTML = `
                    <div class="prediction-list-header">
                        <span class="prediction-list-user">Your Prediction</span>
                        <span class="prediction-list-accuracy">${correctLabel}</span>
                    </div>
                    <div>Winner: <b>${winnerName || ''}</b> | Sets: <b>${formatSetScores(pred.prediction_data?.sets)}</b></div>
                `;
            }
            if (userPredictionCard) {
                listArea.innerHTML = '';
                listArea.appendChild(userPredictionCard);
            }
            // Render all predictions
            if (data.success && data.predictions && data.predictions.length > 0) {
                if (!userPredictionCard) listArea.innerHTML = '';
                data.predictions.forEach(pred => {
                    // Skip user's own prediction if already shown
                    if (userPredictionCard && pred.user_id === userData.prediction.user_id) return;
                    const card = document.createElement('div');
                    card.className = 'prediction-list-card';
                    const winnerName = getPlayerNameFromKey(match, pred.prediction_data?.winner);
                    // Calculate correctness if not provided
                    let correctLabel = 'Incorrect';
                    if (typeof pred.correct !== 'undefined') {
                        correctLabel = pred.correct ? 'Correct' : 'Incorrect';
                    } else if (match && match.winner_id && pred.prediction_data?.winner) {
                        let predictedId = null;
                        if (pred.prediction_data.winner === 'player1') predictedId = match.player1_id;
                        if (pred.prediction_data.winner === 'player2') predictedId = match.player2_id;
                        correctLabel = (predictedId && predictedId == match.winner_id) ? 'Correct' : 'Incorrect';
                    }
                    card.innerHTML = `
                        <div class="prediction-list-header">
                            <span class="prediction-list-user">${pred.username}</span>
                            <span class="prediction-list-accuracy">${correctLabel}</span>
                        </div>
                        <div>Winner: <b>${winnerName || ''}</b> | Sets: <b>${formatSetScores(pred.prediction_data?.sets)}</b></div>
                    `;
                    listArea.appendChild(card);
                });
            } else if (!userPredictionCard) {
                listArea.innerHTML = '<div>No predictions yet.</div>';
            }
        } catch (e) {
            listArea.innerHTML = '<div>Error loading predictions.</div>';
        }
    }

    // --- Game-by-Game Prediction Tab ---
    function renderGameByGameForm(match) {
        const formArea = document.getElementById('game-by-game-form-area');
        let html = '<form id="game-by-game-form">';
        html += '<div id="gbg-cards-container" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">';
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            html += `
            <div class="prediction-list-card" id="gbg-card-${gameNum}" style="${gameNum !== 1 ? 'display:none;' : ''}">
                <h4 style="margin:0 0 0.5rem 0;">Game ${gameNum}</h4>
                <div class="form-group">
                    <label>Winner</label>
                    <select id="gbg-winner-${gameNum}" class="form-control">
                        <option value="">Select Winner</option>
                        <option value="player1">${match.player1_name}</option>
                        <option value="player2">${match.player2_name}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Score</label>
                    <div id="gbg-score-display-${gameNum}" style="font-weight:bold;">-</div>
                </div>
            </div>`;
        }
        html += '</div>';
        html += '<button type="submit" class="btn btn-primary" style="margin-top:1.5rem;">Save All Results</button>';
        html += '</form>';
        formArea.innerHTML = html;

        // Tennis scoring logic
        function nextTennisScore(score, opponentScore) {
            const tennisOrder = ['0', '15', '30', '40', 'AD', 'game'];
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

        // Dynamic reveal and auto-score logic
        let prevScores = [{p1: '0', p2: '0'}];
        let stopAtGame = null;
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winnerSelect = document.getElementById(`gbg-winner-${gameNum}`);
            const scoreDisplay = document.getElementById(`gbg-score-display-${gameNum}`);
            if (!winnerSelect) continue;
            winnerSelect.addEventListener('change', function() {
                let score1 = '';
                let score2 = '';
                if (gameNum === 1) {
                    if (winnerSelect.value === 'player1') {
                        score1 = '15'; score2 = '0';
                    } else if (winnerSelect.value === 'player2') {
                        score1 = '0'; score2 = '15';
                    } else {
                        score1 = ''; score2 = '';
                    }
                } else {
                    let prevScore1 = prevScores[gameNum-1]?.p1 || '0';
                    let prevScore2 = prevScores[gameNum-1]?.p2 || '0';
                    if (winnerSelect.value === 'player1') {
                        if (prevScore2 === 'AD') {
                            score1 = '40'; score2 = '40';
                        } else {
                            score1 = nextTennisScore(prevScore1, prevScore2);
                            score2 = prevScore2;
                        }
                    } else if (winnerSelect.value === 'player2') {
                        if (prevScore1 === 'AD') {
                            score1 = '40'; score2 = '40';
                        } else {
                            score1 = prevScore1;
                            score2 = nextTennisScore(prevScore2, prevScore1);
                        }
                    } else {
                        score1 = ''; score2 = '';
                    }
                }
                scoreDisplay.textContent = score1 && score2 ? `${score1}-${score2}` : '-';
                prevScores[gameNum] = {p1: score1, p2: score2};
                // Stop at first game with a 'game' score
                if (score1 === 'game' || score2 === 'game') {
                    stopAtGame = gameNum;
                    // Hide/disable all further cards
                    for (let i = gameNum + 1; i <= 12; i++) {
                        const nextCard = document.getElementById(`gbg-card-${i}`);
                        if (nextCard) nextCard.style.display = 'none';
                    }
                } else if (winnerSelect.value) {
                    // Only reveal next card if not stopped
                    if (!stopAtGame || gameNum < stopAtGame) {
                        const nextCard = document.getElementById(`gbg-card-${gameNum+1}`);
                        if (nextCard) nextCard.style.display = '';
                    }
                }
            });
        }

        const form = document.getElementById('game-by-game-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const messageEl = document.getElementById('game-by-game-message');
            messageEl.style.display = 'block';
            let results = [];
            prevScores = [{p1: '0', p2: '0'}]; // recalc for submit
            for (let gameNum = 1; gameNum <= 12; gameNum++) {
                const winner = document.getElementById(`gbg-winner-${gameNum}`).value;
                let score1 = '';
                let score2 = '';
                if (gameNum === 1) {
                    if (winner === 'player1') {
                        score1 = '15'; score2 = '0';
                    } else if (winner === 'player2') {
                        score1 = '0'; score2 = '15';
                    } else {
                        score1 = ''; score2 = '';
                    }
                } else {
                    let prevScore1 = prevScores[gameNum-1]?.p1 || '0';
                    let prevScore2 = prevScores[gameNum-1]?.p2 || '0';
                    if (winner === 'player1') {
                        if (prevScore2 === 'AD') {
                            score1 = '40'; score2 = '40';
                        } else {
                            score1 = nextTennisScore(prevScore1, prevScore2);
                            score2 = prevScore2;
                        }
                    } else if (winner === 'player2') {
                        if (prevScore1 === 'AD') {
                            score1 = '40'; score2 = '40';
                        } else {
                            score1 = prevScore1;
                            score2 = nextTennisScore(prevScore2, prevScore1);
                        }
                    } else {
                        score1 = ''; score2 = '';
                    }
                }
                prevScores[gameNum] = {p1: score1, p2: score2};
                if (winner && score1 && score2) {
                    results.push({
                        match_id: match.id,
                        game_number: gameNum,
                        winner: winner,
                        final_score: `${score1}-${score2}`
                    });
                }
            }
            if (results.length === 0) {
                messageEl.textContent = 'Please fill in at least one game result.';
                messageEl.style.background = '#ffe0e0';
                return;
            }
            let savedCount = 0;
            for (const result of results) {
                try {
                    const response = await fetch('../api/game_predictions.php', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(result)
                    });
                    const data = await response.json();
                    if (data.success) savedCount++;
                } catch (err) {}
            }
            if (savedCount === results.length) {
                messageEl.textContent = `Successfully saved ${savedCount} game results!`;
                messageEl.style.background = '#e0ffe0';
                loadGameByGamePredictions(match.id);
            } else {
                messageEl.textContent = `Saved ${savedCount} out of ${results.length} results.`;
                messageEl.style.background = '#fffae0';
            }
        });
    }

    async function loadGameByGamePredictions(matchId) {
        const listArea = document.getElementById('game-by-game-predictions-list');
        listArea.innerHTML = '<div>Loading predictions...</div>';
        try {
            const response = await fetch(`../api/game_predictions.php?match_id=${matchId}`);
            const data = await response.json();
            if (data.success && data.predictions && data.predictions.length > 0) {
                // Group by user
                const userPredictions = {};
                data.predictions.forEach(pred => {
                    if (!userPredictions[pred.username]) userPredictions[pred.username] = [];
                    userPredictions[pred.username].push(pred);
                });
                listArea.innerHTML = '';
                Object.entries(userPredictions).forEach(([username, predictions]) => {
                    const card = document.createElement('div');
                    card.className = 'prediction-list-card';
                    const correctCount = predictions.filter(p => p.correct).length;
                    const accuracy = predictions.length > 0 ? Math.round((correctCount / predictions.length) * 100) : 0;
                    card.innerHTML = `
                        <div class="prediction-list-header">
                            <span class="prediction-list-user">${username}</span>
                            <span class="prediction-list-accuracy">${accuracy}% (${correctCount}/${predictions.length})</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:0.5rem;">${predictions.map(pred => `
                            <div style="background:#fff;padding:0.5rem;border-radius:5px;border:1px solid #dee2e6;text-align:center;font-size:0.9rem;${pred.correct ? 'background:#d4edda;border-color:#c3e6cb;color:#155724;' : ''}">
                                Game ${pred.game_number}: ${pred.predicted_score}
                            </div>
                        `).join('')}</div>
                    `;
                    listArea.appendChild(card);
                });
            } else {
                listArea.innerHTML = '<div>No predictions yet.</div>';
            }
        } catch (e) {
            listArea.innerHTML = '<div>Error loading predictions.</div>';
        }
    }

    // --- Statistics Prediction Tab ---
    function renderStatisticsForm(match) {
        const formArea = document.getElementById('statistics-form-area');
        formArea.innerHTML = `
            <form id="statistics-form">
                <div class="form-group">
                    <label>${match.player1_name} Aces</label>
                    <input type="number" id="stat-aces-player1" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label>${match.player1_name} Double Faults</label>
                    <input type="number" id="stat-df-player1" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label>${match.player2_name} Aces</label>
                    <input type="number" id="stat-aces-player2" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label>${match.player2_name} Double Faults</label>
                    <input type="number" id="stat-df-player2" class="form-control" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Statistics</button>
            </form>
        `;
        const form = document.getElementById('statistics-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const messageEl = document.getElementById('statistics-message');
            messageEl.style.display = 'block';
            const matchId = match.id;
            const player1Aces = document.getElementById('stat-aces-player1').value;
            const player1DF = document.getElementById('stat-df-player1').value;
            const player2Aces = document.getElementById('stat-aces-player2').value;
            const player2DF = document.getElementById('stat-df-player2').value;
            if (!player1Aces || !player1DF || !player2Aces || !player2DF) {
                messageEl.textContent = 'Please fill in all statistics fields.';
                messageEl.style.background = '#ffe0e0';
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
                        double_faults_actual: parseInt(player1DF)
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
                        double_faults_actual: parseInt(player2DF)
                    })
                });
                const data1 = await response1.json();
                const data2 = await response2.json();
                if (data1.success && data2.success) {
                    messageEl.textContent = 'Statistics saved successfully!';
                    messageEl.style.background = '#e0ffe0';
                    loadStatisticsPredictions(match.id);
                } else {
                    const error = data1.message || data2.message || 'Failed to save some statistics.';
                    messageEl.textContent = error;
                    messageEl.style.background = '#fffae0';
                }
            } catch (error) {
                messageEl.textContent = 'Error saving statistics.';
                messageEl.style.background = '#ffe0e0';
            }
        });
    }

    async function loadStatisticsPredictions(matchId) {
        const listArea = document.getElementById('statistics-predictions-list');
        listArea.innerHTML = '<div>Loading predictions...</div>';
        try {
            const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}`);
            const data = await response.json();
            if (data.success && data.predictions && data.predictions.length > 0) {
                // Group by user
                const userPredictions = {};
                data.predictions.forEach(pred => {
                    if (!userPredictions[pred.username]) userPredictions[pred.username] = [];
                    userPredictions[pred.username].push(pred);
                });
                listArea.innerHTML = '';
                Object.entries(userPredictions).forEach(([username, predictions]) => {
                    const card = document.createElement('div');
                    card.className = 'prediction-list-card';
                    card.innerHTML = `
                        <div class="prediction-list-header">
                            <span class="prediction-list-user">${username}</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:0.5rem;">${predictions.map(pred => `
                            <div style="background:#fff;padding:0.5rem;border-radius:5px;border:1px solid #dee2e6;text-align:center;font-size:0.9rem;">
                                Aces: ${pred.aces_predicted} | Double Faults: ${pred.double_faults_predicted}
                            </div>
                        `).join('')}</div>
                    `;
                    listArea.appendChild(card);
                });
            } else {
                listArea.innerHTML = '<div>No predictions yet.</div>';
            }
        } catch (e) {
            listArea.innerHTML = '<div>Error loading predictions.</div>';
        }
    }

    // Initial load
    loadMatches();
});
</script>

<?php require_once 'includes/footer.php'; ?>

