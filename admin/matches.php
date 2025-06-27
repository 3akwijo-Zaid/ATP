<?php require_once 'includes/header.php'; ?>

<h2>Add New Match</h2>
<form id="add-match-form">
    <div class="form-group">
        <label for="competition_name">Competition Name</label>
        <input type="text" id="competition_name" required>
    </div>
    <div class="form-group">
        <label for="player1">Player 1</label>
        <input type="text" id="player1" required>
    </div>
    <div class="form-group">
        <label for="player2">Player 2</label>
        <input type="text" id="player2" required>
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
    <button type="submit" class="btn">Add Match</button>
    <p id="add-message"></p>
</form>

<hr style="margin: 2rem 0;">

<h2>Existing Matches</h2>
<div id="matches-list"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const matchesList = document.getElementById('matches-list');
    const addMatchForm = document.getElementById('add-match-form');
    const addMessage = document.getElementById('add-message');

    // Fetch and display existing matches
    async function fetchMatches() {
        const response = await fetch('../api/matches.php');
        const matches = await response.json();
        matchesList.innerHTML = '';
        if (matches.length > 0) {
            const table = document.createElement('table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Competition</th>
                        <th>Player 1</th>
                        <th>Player 2</th>
                        <th>Start Time</th>
                        <th>Format</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${matches.map(m => `
                        <tr>
                            <td>${m.competition_name}</td>
                            <td>${m.player1}</td>
                            <td>${m.player2}</td>
                            <td>${new Date(m.start_time).toLocaleString()}</td>
                            <td>${m.match_format === 'best_of_3' ? 'Best of 3' : 'Best of 5'}</td>
                            <td>${m.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            `;
            matchesList.appendChild(table);
        } else {
            matchesList.innerHTML = '<p>No matches found.</p>';
        }
    }

    // Handle add match form submission
    addMatchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const competitionName = document.getElementById('competition_name').value;
        const player1 = document.getElementById('player1').value;
        const player2 = document.getElementById('player2').value;
        const startTime = document.getElementById('start_time').value;
        const matchFormat = document.getElementById('match_format').value;

        const response = await fetch('../api/admin.php?action=add_match', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                competition_name: competitionName,
                player1, 
                player2, 
                start_time: startTime, 
                match_format: matchFormat 
            })
        });
        const result = await response.json();
        addMessage.textContent = result.message;
        if (response.ok) {
            fetchMatches(); // Refresh the list
            addMatchForm.reset();
        }
    });

    fetchMatches();
});
</script>

<?php require_once 'includes/footer.php'; ?> 