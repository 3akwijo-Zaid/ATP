<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Match Management</h1>
    <p>Add new matches and manage existing match schedules.</p>
</div>

<div class="content-card">
    <h2>Add New Match</h2>
    <form id="add-match-form">
        <div class="form-group">
            <label for="tournament_id">Tournament</label>
            <select id="tournament_id" required></select>
        </div>
        <div class="form-group">
            <label for="round">Round</label>
            <input type="text" id="round" required placeholder="e.g. Quarterfinals">
        </div>
        <div class="form-group">
            <label for="player1_id">Player 1</label>
            <select id="player1_id" required></select>
        </div>
        <div class="form-group">
            <label for="player2_id">Player 2</label>
            <select id="player2_id" required></select>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="datetime-local" id="start_time" required>
        </div>
        <div class="form-group">
            <label for="match_format">Match Format</label>
            <select id="match_format" required>
                <option value="best_of_5">Best of 5 Sets</option>
                <option value="best_of_3">Best of 3 Sets</option>
            </select>
        </div>
        <div class="form-group">
            <label><input type="checkbox" id="featured"> Featured Match</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" id="game_predictions_enabled"> Enable Game Result Predictions</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" id="statistics_predictions_enabled"> Enable Statistics Predictions</label>
        </div>
        <button type="submit" class="btn">Add Match</button>
        <div class="list-item">
            <p id="add-message"></p>
        </div>
    </form>
</div>

<div class="content-card">
    <h2>All Matches</h2>
    <div class="container--table">
        <div class="table-container">
            <div id="matches-list"></div>
        </div>
    </div>
</div>

<script>
async function fetchTournaments() {
    const res = await fetch('../api/tournaments.php');
    const data = await res.json();
    const select = document.getElementById('tournament_id');
    select.innerHTML = '<option value="">Select Tournament</option>';
    data.forEach(t => {
        select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
    });
}
async function fetchPlayers() {
    const res = await fetch('../api/users.php?action=get_players');
    const data = await res.json();
    const p1 = document.getElementById('player1_id');
    const p2 = document.getElementById('player2_id');
    p1.innerHTML = '<option value="">Select Player 1</option>';
    p2.innerHTML = '<option value="">Select Player 2</option>';
    data.forEach(pl => {
        p1.innerHTML += `<option value="${pl.id}">${pl.name}</option>`;
        p2.innerHTML += `<option value="${pl.id}">${pl.name}</option>`;
    });
}
document.addEventListener('DOMContentLoaded', function() {
    fetchTournaments();
    fetchPlayers();
    const matchesList = document.getElementById('matches-list');
    const addMatchForm = document.getElementById('add-match-form');
    const addMessage = document.getElementById('add-message');
    async function fetchMatches() {
        const res = await fetch('../api/matches.php?grouped=0');
        const matches = await res.json();
        let html = '<table class="modern-table"><thead><tr><th>ID</th><th>Round</th><th>Player 1</th><th>Player 2</th><th>Start Time</th><th>Status</th><th>Featured</th><th>Predictions</th><th>Actions</th></tr></thead><tbody>';
        matches.forEach(m => {
            const gamePred = m.game_predictions_enabled ? '<span class="badge badge-success">Game</span>' : '<span class="badge badge-secondary">Game</span>';
            const statsPred = m.statistics_predictions_enabled ? '<span class="badge badge-success">Stats</span>' : '<span class="badge badge-secondary">Stats</span>';
            html += `<tr>
                <td>${m.id}</td>
                <td>${m.round}</td>
                <td>${m.player1_name || ''}</td>
                <td>${m.player2_name || ''}</td>
                <td>${m.start_time}</td>
                <td>${m.status || 'upcoming'}</td>
                <td><button onclick='toggleFeatured(${m.id}, ${m.featured || 0})' class='btn btn-sm ${m.featured ? 'btn-success' : 'btn-secondary'}'>${m.featured ? 'Featured' : 'Not Featured'}</button></td>
                <td>${gamePred} ${statsPred}</td>
                <td><a href='results.php?match_id=${m.id}' class='btn btn-sm btn-primary'>Update Result</a></td>
            </tr>`;
        });
        html += '</tbody></table>';
        matchesList.innerHTML = html;
    }
    addMatchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const tournament_id = document.getElementById('tournament_id').value;
        const round = document.getElementById('round').value;
        const player1_id = document.getElementById('player1_id').value;
        const player2_id = document.getElementById('player2_id').value;
        const start_time = document.getElementById('start_time').value;
        const match_format = document.getElementById('match_format').value;
        const featured = document.getElementById('featured').checked ? 1 : 0;
        const game_predictions_enabled = document.getElementById('game_predictions_enabled').checked ? 1 : 0;
        const statistics_predictions_enabled = document.getElementById('statistics_predictions_enabled').checked ? 1 : 0;
        const response = await fetch('../api/admin.php?action=add_match', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                tournament_id, round, player1_id, player2_id, start_time, match_format, featured, game_predictions_enabled, statistics_predictions_enabled
            })
        });
        const result = await response.json();
        addMessage.textContent = result.message;
        if (response.ok && result.success !== false) {
            setTimeout(() => {
                location.reload(); // Wait 1 second before reloading
            }, 1000);
        }
    });
    window.toggleFeatured = async function(matchId, current) {
        const response = await fetch('../api/admin.php?action=toggle_featured', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ match_id: matchId, featured: current == 1 ? 0 : 1 })
        });
        const result = await response.json();
        alert(result.message);
        fetchMatches();
    };
    fetchMatches();
});
</script>
<style>
input,textarea,select,option {
    color: #111 !important;
}
input::placeholder,
textarea::placeholder {
    color: #111 !important;
    opacity: 1;
}
<?php require_once 'includes/footer.php'; ?>