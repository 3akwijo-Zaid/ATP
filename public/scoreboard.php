<?php require_once 'includes/header.php'; ?>

<div class="container container--full">
    <h1>Scoreboard</h1>
    <div id="scoreboard-list">
        <!-- Scoreboard will be loaded here -->
    </div>
</div>

<!-- Modal for viewing predictions -->
<div id="predictions-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:3000;align-items:center;justify-content:center;">
    <div style="background:#fff;color:#222;padding:2em 2em 1em 2em;border-radius:10px;max-width:500px;width:95vw;max-height:90vh;overflow-y:auto;position:relative;">
        <button id="close-modal" style="position:absolute;top:0.5em;right:0.5em;font-size:1.3em;background:none;border:none;cursor:pointer;">&times;</button>
        <h3 id="modal-title">Predictions</h3>
        <div id="modal-content"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreboardList = document.getElementById('scoreboard-list');
    const modal = document.getElementById('predictions-modal');
    const closeModal = document.getElementById('close-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');

    async function fetchScoreboard() {
        try {
            const response = await fetch('../api/users.php?action=scoreboard');
            const users = await response.json();

            if (users.length > 0) {
                // Filter out the 'admin' user
                const filteredUsers = users.filter(u => u.username && u.username.toLowerCase() !== 'admin');
                if (filteredUsers.length > 0) {
                    scoreboardList.innerHTML = '';
                    filteredUsers.forEach((user, index) => {
                        const userElement = document.createElement('div');
                        userElement.className = 'list-item';
                        userElement.innerHTML = `
                            <span><strong>${index + 1}. ${user.username}</strong></span>
                            <span>${user.points} Points</span>
                            <button class="btn" style="width:auto;padding:0.4em 1em;font-size:0.9em;" onclick="viewPredictions(${user.id}, '${user.username}')">View Predictions</button>
                        `;
                        scoreboardList.appendChild(userElement);
                    });
                } else {
                    scoreboardList.innerHTML = '<p>No users on the scoreboard yet.</p>';
                }
            } else {
                scoreboardList.innerHTML = '<p>No users on the scoreboard yet.</p>';
            }
        } catch (error) {
            console.error('Error fetching scoreboard:', error);
            scoreboardList.innerHTML = '<p>Could not load scoreboard.</p>';
        }
    }

    window.viewPredictions = async function(userId, username) {
        modal.style.display = 'flex';
        modalTitle.textContent = `Predictions for ${username}`;
        modalContent.innerHTML = '<p>Loading...</p>';
        
        try {
            // Fetch all predictions for this user
            const response = await fetch(`../api/predictions.php?user_id=${userId}`);
            const data = await response.json();
            
            if (!data.success || !data.predictions || data.predictions.length === 0) {
                modalContent.innerHTML = '<p>No predictions found.</p>';
                return;
            }
            
            const predictions = data.predictions;
            
            // Fetch all matches to get match info and lock status
            const matchesResp = await fetch('../api/matches.php');
            const matches = await matchesResp.json();
            
            // Only show predictions for locked/finished matches
            const now = new Date();
            const lockedPreds = predictions.filter(pred => {
                const match = matches.find(m => m.id == pred.match_id);
                if (!match) return false;
                const start = new Date(match.start_time);
                return (start - now) / 1000 <= 3600 || match.status === 'finished';
            });
            
            if (lockedPreds.length === 0) {
                modalContent.innerHTML = '<p>No public predictions yet (only shown after match lock).</p>';
                return;
            }
            
            modalContent.innerHTML = lockedPreds.map(pred => {
                const match = matches.find(m => m.id == pred.match_id);
                let sets = '';
                let winner = '';
                
                try {
                    // Support both stringified and already-parsed prediction_data
                    const predData = typeof pred.prediction_data === 'string' ? JSON.parse(pred.prediction_data) : pred.prediction_data;
                    
                    // Get winner
                    if (predData.winner === 'player1' && match) {
                        winner = match.player1_name || match.player1;
                    } else if (predData.winner === 'player2' && match) {
                        winner = match.player2_name || match.player2;
                    } else {
                        winner = predData.winner || '';
                    }
                    
                    // Get sets
                    if (predData.sets && Array.isArray(predData.sets)) {
                        sets = predData.sets.map((s, i) => {
                            const p1 = s.player1_games !== undefined ? s.player1_games : (s.player1 !== undefined ? s.player1 : '');
                            const p2 = s.player2_games !== undefined ? s.player2_games : (s.player2 !== undefined ? s.player2 : '');
                            return `<li>Set ${i+1}: ${p1}-${p2}</li>`;
                        }).join('');
                        sets = sets ? `<ul style="margin:0.5em 0 0 1em;">${sets}</ul>` : '';
                    }
                    
                    const matchName = match ? `${match.player1_name || match.player1} vs ${match.player2_name || match.player2}` : 'Match';
                    return `<div style="margin-bottom:1.2em;padding:1em;background:#f5f5f5;border-radius:8px;">
                        <strong>${matchName}</strong><br>
                        Predicted winner: <b style="color:#2563eb;">${winner}</b>
                        ${sets}
                    </div>`;
                } catch (e) {
                    console.error('Error parsing prediction:', e);
                    return `<div style="margin-bottom:1.2em;padding:1em;background:#f5f5f5;border-radius:8px;">
                        <strong>${match ? `${match.player1_name || match.player1} vs ${match.player2_name || match.player2}` : 'Match'}</strong><br>
                        <em>Prediction data unavailable</em>
                    </div>`;
                }
            }).join('');
            
        } catch (error) {
            console.error('Error fetching predictions:', error);
            modalContent.innerHTML = '<p>Error loading predictions. Please try again.</p>';
        }
    };

    closeModal.onclick = () => { modal.style.display = 'none'; };
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

    fetchScoreboard();
});
</script>

<?php require_once 'includes/footer.php'; ?> 