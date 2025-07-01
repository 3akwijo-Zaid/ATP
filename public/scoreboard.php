<?php require_once 'includes/header.php'; ?>
<?php $current_username = $_SESSION['username'] ?? null; ?>

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

<style>
.scoreboard-table {
  width: 100%;
  border-collapse: collapse;
  margin: 2em 0 1em 0;
  background: #232b33;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 24px 0 #0006;
}
.scoreboard-table th, .scoreboard-table td {
  padding: 1em 0.7em;
  text-align: left;
  font-size: 1.08em;
}
.scoreboard-table th {
  background: #16243a;
  color: #ffd54f;
  font-weight: 800;
  letter-spacing: 0.04em;
  border-bottom: 2px solid #23394d;
}
.scoreboard-table tr {
  border-bottom: 1px solid #23394d;
  transition: background 0.18s;
}
.scoreboard-table tr.current-user {
  background: #2e3a4d !important;
  font-weight: 700;
  color: #ffd54f;
}
.scoreboard-table tr:hover {
  background: #263143;
}
.scoreboard-table td.rank-badge {
  font-size: 1.2em;
  font-weight: 900;
  text-align: center;
}
.scoreboard-table .medal-icon {
  width: 28px;
  height: 28px;
  vertical-align: middle;
  margin-right: 0.2em;
}
.scoreboard-table .medal-gold { fill: #ffd700; }
.scoreboard-table .medal-silver { fill: #b0bec5; }
.scoreboard-table .medal-bronze { fill: #ff9800; }
.scoreboard-table .scoreboard-btn {
  background: #ffd54f;
  color: #16243a;
  border: none;
  border-radius: 6px;
  padding: 0.4em 1em;
  font-size: 0.98em;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.18s, color 0.18s;
}
.scoreboard-table .scoreboard-btn:hover {
  background: #fffde7;
  color: #ffd54f;
}
.scoreboard-table td, .scoreboard-table th {
  color: #fff;
}
.scoreboard-table tr.current-user td {
  color: #ffd54f;
}
.scoreboard-table th.rank-header, .scoreboard-table td.rank-badge {
  text-align: center;
  vertical-align: middle;
  width: 60px;
  padding-left: 0;
  padding-right: 0;
}
.scoreboard-table td.rank-badge svg {
  display: block;
  margin: 0 auto;
}
@media (max-width: 600px) {
  .scoreboard-table th, .scoreboard-table td { padding: 0.6em 0.3em; font-size: 0.98em; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreboardList = document.getElementById('scoreboard-list');
    const modal = document.getElementById('predictions-modal');
    const closeModal = document.getElementById('close-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const currentUsername = <?php echo json_encode($current_username); ?>;

    // SVG medal icons
    const medalSVG = {
      gold: `<svg class="medal-icon medal-gold" viewBox="0 0 32 32"><circle cx="16" cy="16" r="13" stroke="#fff8e1" stroke-width="2"/><circle cx="16" cy="16" r="10"/></svg>`,
      silver: `<svg class="medal-icon medal-silver" viewBox="0 0 32 32"><circle cx="16" cy="16" r="13" stroke="#fff8e1" stroke-width="2"/><circle cx="16" cy="16" r="10"/></svg>`,
      bronze: `<svg class="medal-icon medal-bronze" viewBox="0 0 32 32"><circle cx="16" cy="16" r="13" stroke="#fff8e1" stroke-width="2"/><circle cx="16" cy="16" r="10"/></svg>`
    };

    async function fetchScoreboard() {
        try {
            const response = await fetch('../api/users.php?action=scoreboard');
            const users = await response.json();

            if (users.length > 0) {
                // Filter out the 'admin' user
                const filteredUsers = users.filter(u => u.username && u.username.toLowerCase() !== 'admin');
                if (filteredUsers.length > 0) {
                    let html = '<table class="scoreboard-table">';
                    html += '<thead><tr><th>Rank</th><th>User</th><th>Points</th><th></th></tr></thead><tbody>';
                    filteredUsers.forEach((user, index) => {
                        let badge = '';
                        if (index === 0) badge = medalSVG.gold;
                        else if (index === 1) badge = medalSVG.silver;
                        else if (index === 2) badge = medalSVG.bronze;
                        const isCurrent = currentUsername && user.username === currentUsername;
                        html += `<tr class="${isCurrent ? 'current-user' : ''}">
                            <td class="rank-badge">${badge || (index+1)}</td>
                            <td>${user.username}${isCurrent ? ' <span style=\'color:#ffd54f;font-weight:700;\'>(You)</span>' : ''}</td>
                            <td>${user.points}</td>
                            <td><button class="scoreboard-btn" onclick="viewPredictions(${user.id}, '${user.username.replace(/'/g, "&#39;")}')">View Predictions</button></td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    scoreboardList.innerHTML = html;
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
                    return `<div style="margin-bottom:1.2em;padding:1em;background:#263143;border-radius:8px;color:#ffd54f;">
                        <strong>${matchName}</strong><br>
                        Predicted winner: <b style="color:#ffd54f;">${winner}</b>
                        ${sets}
                    </div>`;
                } catch (e) {
                    console.error('Error parsing prediction:', e);
                    return `<div style="margin-bottom:1.2em;padding:1em;background:#263143;border-radius:8px;color:#ffd54f;">
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