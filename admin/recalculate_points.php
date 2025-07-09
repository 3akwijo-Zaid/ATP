<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Recalculate Points</h1>
    <p>Recalculate prediction points for finished matches.</p>
</div>

<div class="content-card">
    <h2>Finished Matches</h2>
    <div id="finished-matches-list"></div>
</div>

<script>
async function fetchFinishedMatches() {
    const res = await fetch('../api/matches.php?grouped=0');
    const matches = await res.json();
    let html = '<table class="modern-table"><thead><tr><th>ID</th><th>Round</th><th>Player 1</th><th>Player 2</th><th>Start Time</th><th>Status</th><th>Recalculate</th></tr></thead><tbody>';
    matches.filter(m => m.status === 'finished' || m.status === 'completed' || m.status === 'retired_player1' || m.status === 'retired_player2').forEach(m => {
        html += `<tr>
            <td>${m.id}</td>
            <td>${m.round}</td>
            <td>${m.player1_name || ''}</td>
            <td>${m.player2_name || ''}</td>
            <td><span class='match-date' data-utc1='${m.start_time}'></span></td>
            <td>${m.status}</td>
            <td><button class='btn btn-sm btn-warning' onclick='recalcPoints(${m.id}, this)'>Recalculate</button></td>
        </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('finished-matches-list').innerHTML = html;
}
window.recalcPoints = async function(matchId, btn) {
    btn.disabled = true;
    btn.textContent = 'Recalculating...';
    const response = await fetch('../api/admin.php?action=recalculate_points', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ match_id: matchId })
    });
    const result = await response.json();
    if (result.success) {
        btn.textContent = 'Done!';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-success');
    } else {
        btn.textContent = 'Error';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-danger');
        alert(result.message || 'Failed to recalculate points.');
    }
    setTimeout(() => {
        btn.textContent = 'Recalculate';
        btn.classList.remove('btn-success', 'btn-danger');
        btn.classList.add('btn-warning');
        btn.disabled = false;
    }, 2000);
};
document.addEventListener('DOMContentLoaded', fetchFinishedMatches);
</script>
<?php require_once 'includes/footer.php'; ?> 