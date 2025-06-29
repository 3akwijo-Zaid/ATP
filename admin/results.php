<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Match Results</h1>
    <p>Update match results and set scores. For detailed game-by-game results, use the Game Results page.</p>
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

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.list-item {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 5px;
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
    matchSelect.addEventListener('change', function() {
        const matchId = this.value;
        if (!matchId) {
            resultForm.style.display = 'none';
            return;
        }

        const match = matches.find(m => m.id == matchId);
        formHeader.textContent = `Result for: ${match.player1_name} vs ${match.player2_name}`;
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
                <fieldset style="margin-bottom:1rem; padding:1rem; border-radius:5px; border:1px solid #ddd">
                    <legend>Set ${i} (Fill in if this set was played)</legend>
                    <div style="display:flex; gap:1rem;">
                        <input type="number" id="set${i}_p1" placeholder="${match.player1_name} Games" min="0" style="width:50%">
                        <input type="number" id="set${i}_p2" placeholder="${match.player2_name} Games" min="0" style="width:50%">
                    </div>
                     <div style="display:flex; gap:1rem; margin-top:0.5rem">
                        <input type="number" id="set${i}_p1_tb" placeholder="${match.player1_name} Tiebreak" min="0" style="width:50%">
                        <input type="number" id="set${i}_p2_tb" placeholder="${match.player2_name} Tiebreak" min="0" style="width:50%">
                    </div>
                </fieldset>
            `;
        }
        resultForm.style.display = 'block';
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
                    player1_tiebreak: document.getElementById(`set${i}_p1_tb`).value || null,
                    player2_tiebreak: document.getElementById(`set${i}_p2_tb`).value || null,
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
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 