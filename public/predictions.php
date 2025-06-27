<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please <a href='login.php'>login</a> to make predictions.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

if (!isset($_GET['match_id'])) {
    echo "<div class='container'><p>No match selected. Go to the <a href='index.php'>homepage</a> to pick a match.</p></div>";
    require_once 'includes/footer.php';
    exit();
}
?>

<div class="container">
    <div id="match-details"></div>
    <form id="prediction-form">
        <h2>Your Prediction</h2>
        <div id="sets-prediction"></div>
        <button type="submit" class="btn">Submit Prediction</button>
        <p id="message"></p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const matchId = new URLSearchParams(window.location.search).get('match_id');
    const matchDetails = document.getElementById('match-details');
    const setsPrediction = document.getElementById('sets-prediction');
    const predictionForm = document.getElementById('prediction-form');
    const messageEl = document.getElementById('message');

    // Fetch match details
    const response = await fetch(`../api/matches.php?id=${matchId}`);
    const match = await response.json();

    if (match) {
        matchDetails.innerHTML = `<h1>${match.player1} vs ${match.player2}</h1>`;
        
        // Create winner selection
        const maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        let winnerHtml = `
            <div class="form-group">
                <label for="winner">Match Winner</label>
                <select id="winner" name="winner" required class="form-group" style="width:100%; padding:10px;">
                    <option value="">Select Winner</option>
                    <option value="${match.player1}">${match.player1}</option>
                    <option value="${match.player2}">${match.player2}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sets_count">Match will end in how many sets?</label>
                <select id="sets_count" required class="form-group" style="width:100%; padding:10px;">
                    <option value="">Select number of sets</option>
                    <option value="2">2 sets (2-0)</option>
                    <option value="3">3 sets (2-1 or 1-2)</option>
                    ${maxSets > 3 ? '<option value="4">4 sets (3-1 or 1-3)</option>' : ''}
                    ${maxSets > 3 ? '<option value="5">5 sets (3-2 or 2-3)</option>' : ''}
                </select>
            </div>
        `;
        setsPrediction.innerHTML += winnerHtml;

        // Create set prediction inputs
        for (let i = 1; i <= maxSets; i++) {
            let setHtml = `
                <fieldset style="margin-bottom:1rem; padding:1rem; border-radius:5px; border:1px solid #555" id="set${i}_container">
                    <legend>Set ${i}</legend>
                    <div class="form-group" style="display:flex; gap:1rem;">
                        <input type="number" id="set${i}_p1" placeholder="${match.player1} Points" min="0" max="7" required style="width:50%">
                        <input type="number" id="set${i}_p2" placeholder="${match.player2} Points" min="0" max="7" required style="width:50%">
                    </div>
                    <div class="form-group" style="display:flex; gap:1rem; margin-top:0.5rem;">
                        <input type="number" id="set${i}_p1_tb" placeholder="${match.player1} Tiebreak Points" min="0" max="20" style="width:50%">
                        <input type="number" id="set${i}_p2_tb" placeholder="${match.player2} Tiebreak Points" min="0" max="20" style="width:50%">
                    </div>
                </fieldset>
            `;
            setsPrediction.innerHTML += setHtml;
        }

        // Add event listener to show/hide sets based on selection
        document.getElementById('sets_count').addEventListener('change', function() {
            const selectedSets = parseInt(this.value);
            for (let i = 1; i <= maxSets; i++) {
                const container = document.getElementById(`set${i}_container`);
                if (i <= selectedSets) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            }
        });
    } else {
        matchDetails.innerHTML = '<h1>Match not found</h1>';
        predictionForm.style.display = 'none';
    }

    // Validation function
    function validatePrediction() {
        const winner = document.getElementById('winner').value;
        const setsCount = document.getElementById('sets_count').value;
        
        if (!winner) {
            alert('Please select a match winner');
            return false;
        }

        if (!setsCount) {
            alert('Please select how many sets the match will end in');
            return false;
        }

        const maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        let p1Sets = 0;
        let p2Sets = 0;

        // Validate that all required sets are filled
        for (let i = 1; i <= parseInt(setsCount); i++) {
            const p1_games = parseInt(document.getElementById(`set${i}_p1`).value) || 0;
            const p2_games = parseInt(document.getElementById(`set${i}_p2`).value) || 0;
            
            if (p1_games === 0 && p2_games === 0) {
                alert(`Please fill in the score for Set ${i}`);
                return false;
            }
            
            if (p1_games > p2_games) p1Sets++;
            else if (p2_games > p1_games) p2Sets++;
        }

        // Check if the predicted winner actually wins based on set scores
        const predictedWinner = winner;
        const actualWinner = p1Sets > p2Sets ? match.player1 : match.player2;
        
        if (predictedWinner !== actualWinner) {
            alert('Your set predictions do not match your winner prediction. Please check your scores.');
            return false;
        }

        return true;
    }

    // Handle form submission
    predictionForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validatePrediction()) {
            return;
        }

        const predictionData = {
            winner: document.getElementById('winner').value,
            sets: []
        };

        const setsCount = parseInt(document.getElementById('sets_count').value);
        for (let i = 1; i <= setsCount; i++) {
            predictionData.sets.push({
                player1_games: document.getElementById(`set${i}_p1`).value,
                player2_games: document.getElementById(`set${i}_p2`).value,
                player1_tiebreak: document.getElementById(`set${i}_p1_tb`).value || null,
                player2_tiebreak: document.getElementById(`set${i}_p2_tb`).value || null
            });
        }

        const submitResponse = await fetch('../api/predictions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                match_id: matchId,
                prediction_data: JSON.stringify(predictionData)
            })
        });

        const result = await submitResponse.json();
        messageEl.textContent = result.message;
        if (submitResponse.ok) {
            messageEl.style.color = 'lightgreen';
            setTimeout(() => { window.location.href = 'index.php'; }, 2000);
        } else {
            messageEl.style.color = 'salmon';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 