<?php require_once 'includes/header.php'; ?>

<h2>Update Match Result</h2>
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
    <p id="message"></p>
</form>

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
            const option = new Option(`${m.player1} vs ${m.player2}`, m.id);
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
        formHeader.textContent = `Result for: ${match.player1} vs ${match.player2}`;
        matchIdInput.value = match.id;
        p1NameInput.value = match.player1;
        p2NameInput.value = match.player2;
        document.getElementById('match_format').value = match.match_format;

        // Populate winner dropdown
        winnerSelect.innerHTML = `
            <option value="${match.player1}">${match.player1}</option>
            <option value="${match.player2}">${match.player2}</option>
        `;

        // Generate set inputs
        setsInputs.innerHTML = '';
        const maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        for (let i = 1; i <= maxSets; i++) {
            setsInputs.innerHTML += `
                <fieldset style="margin-bottom:1rem; padding:1rem; border-radius:5px; border:1px solid #ddd">
                    <legend>Set ${i} (Fill in if this set was played)</legend>
                    <div style="display:flex; gap:1rem;">
                        <input type="number" id="set${i}_p1" placeholder="${match.player1} Games" min="0" style="width:50%">
                        <input type="number" id="set${i}_p2" placeholder="${match.player2} Games" min="0" style="width:50%">
                    </div>
                     <div style="display:flex; gap:1rem; margin-top:0.5rem">
                        <input type="number" id="set${i}_p1_tb" placeholder="${match.player1} Tiebreak" min="0" style="width:50%">
                        <input type="number" id="set${i}_p2_tb" placeholder="${match.player2} Tiebreak" min="0" style="width:50%">
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