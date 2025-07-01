<?php require_once 'includes/header.php'; ?>
<?php $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixtures & Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">
</head>
<div class="container container--full mt-4">
    <h2>Fixtures & Results</h2>
    <div class="mb-3" style="margin-left: 0.5em;">
        <label for="tournamentFilter" class="form-label">Filter by Tournament:</label>
        <select id="tournamentFilter" class="styled-select"></select><br>
    </div><br>
    <div id="fixtures-list">
        <div class="loading">Loading fixtures...</div>
    </div>
</div>
<script>
const USER_ID = <?php echo $user_id; ?>;
const IS_ADMIN = <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 'true' : 'false'; ?>;

function getFlagCode(code) {
    // Map 3-letter to 2-letter codes
    const map = {
        'USA': 'us', 'GBR': 'gb', 'ESP': 'es', 'FRA': 'fr', 'GER': 'de', 'ITA': 'it', 'AUS': 'au', 'RUS': 'ru', 'SRB': 'rs', 'ARG': 'ar', 'SUI': 'ch', 'CRO': 'hr', 'CAN': 'ca', 'JPN': 'jp', 'UK': 'gb', 'ENG': 'gb', 'FR': 'fr', 'DE': 'de', 'IT': 'it', 'AU': 'au', 'RU': 'ru', 'RS': 'rs', 'AR': 'ar', 'CH': 'ch', 'HR': 'hr', 'CA': 'ca', 'JP': 'jp', 'Other': 'un'
    };
    if (!code) return 'un';
    code = code.toUpperCase();
    return map[code] || code.toLowerCase();
}

async function fetchTournaments() {
    try {
        const res = await fetch('../api/tournaments.php');
        const data = await res.json();
        const select = document.getElementById('tournamentFilter');
        select.innerHTML = '<option value="">All Tournaments</option>';
        data.forEach(t => {
            select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
        });
    } catch (error) {
        console.error('Error fetching tournaments:', error);
    }
}

async function fetchPrediction(matchId) {
    if (!USER_ID) return null;
    try {
        const res = await fetch(`../api/predictions.php?user_id=${USER_ID}&match_id=${matchId}`);
        const data = await res.json();
        return data && data.length ? data[0] : null;
    } catch (error) {
        console.error('Error fetching prediction:', error);
        return null;
    }
}

async function fetchFixtures() {
    try {
        const tid = document.getElementById('tournamentFilter').value;
        let url = '../api/matches.php?grouped=1';
        if (tid) url += '&tournament_id=' + tid;
        const res = await fetch(url);
        const data = await res.json();
        await renderFixtures(data);
    } catch (error) {
        console.error('Error fetching fixtures:', error);
        document.getElementById('fixtures-list').innerHTML = '<p class="error">Error loading fixtures. Please try again.</p>';
    }
}

async function renderFixtures(data) {
    let html = '';
    if (!data || !data.length) {
        html = '<p>No matches found.</p>';
    } else {
        for (const day of data) {
            html += `<div class='fixture-day'><h4>${day.date}</h4><div class='fixture-list'>`;
            for (const m of day.matches) {
                let predictionHtml = '';
                if (USER_ID) {
                    const prediction = await fetchPrediction(m.id);
                    if (m.status === 'upcoming') {
                        if (prediction) {
                            predictionHtml = `<button class='btn btn-outline-success btn-sm' onclick='window.location="predictions.php?match_id=${m.id}"'>View/Edit Prediction</button>`;
                        } else {
                            predictionHtml = `<button class='btn btn-primary btn-sm' onclick='window.location="predictions.php?match_id=${m.id}"'>Predict</button>`;
                        }
                    } else if (prediction) {
                        predictionHtml = `<button class='btn btn-info btn-sm' onclick='window.location="predictions.php?match_id=${m.id}"'>View Your Prediction</button>`;
                    }
                } else {
                    predictionHtml = `<a href='login.php' class='btn btn-secondary btn-sm'>Login to Predict</a>`;
                }

                // Admin featured button
                let featuredHtml = '';
                if (IS_ADMIN) {
                    featuredHtml = `<button class='btn btn-featured ${m.featured == 1 ? 'btn-featured-on' : 'btn-featured-off'}' data-match-id='${m.id}' data-featured='${m.featured || 0}'>${m.featured == 1 ? '★ Featured' : '☆ Make Featured'}</button>`;
                }

                html += `<div class='fixture-card'>
                <div class='fixture-tournament'>
                    ${m.tournament_logo ? `<img src='${m.tournament_logo}' alt='${m.tournament_name}' class='fixture-tournament-logo'>` : ''}
                    <span>${m.tournament_name} (${m.round})</span>
                </div>
                    <div class='fixture-time'>${m.start_time.substr(11,5)}</div>
                    <div class='fixture-players'>
                        <span class='fixture-player'>
                            ${m.player1_image ? `<img src='${m.player1_image}' alt='${m.player1_name}' class='fixture-player-img'>` : ''}
                            <b>${m.player1_name}</b> <span class='fi fi-${getFlagCode(m.player1_country)} flag-icon'></span>
                        </span>
                        <span class='fixture-vs'>vs</span>
                        <span class='fixture-player'>
                            ${m.player2_image ? `<img src='${m.player2_image}' alt='${m.player2_name}' class='fixture-player-img'>` : ''}
                            <b>${m.player2_name}</b> <span class='fi fi-${getFlagCode(m.player2_country)} flag-icon'></span>
                        </span>
                    </div>
                    <div class='fixture-prediction-types'>
                        ${m.game_predictions_enabled ? '<span class="prediction-badge game-badge">Game</span>' : ''}
                        ${m.statistics_predictions_enabled ? '<span class="prediction-badge stats-badge">Stats</span>' : ''}
                    </div>
                    <div class='fixture-status ${m.status}'>${m.status.replace('_',' ')}</div>
                    <div class='fixture-result'>${m.result_summary ? 'Result: ' + m.result_summary : ''}</div>
                    <div class='fixture-prediction-action'>
                        ${predictionHtml}
                        ${IS_ADMIN ? `<div style='margin-top:0.7em;'>${featuredHtml}</div>` : ''}
                    </div>
                </div>`;
            }
            html += '</div></div>';
        }
    }
    document.getElementById('fixtures-list').innerHTML = html;
}

// Event listeners
document.getElementById('tournamentFilter').addEventListener('change', fetchFixtures);

// Admin: toggle featured (event delegation)
document.addEventListener('click', async function(e) {
    if (e.target && e.target.classList.contains('btn-featured')) {
        const matchId = e.target.getAttribute('data-match-id');
        const current = e.target.getAttribute('data-featured');
        e.target.disabled = true;
        try {
            const response = await fetch('../api/admin.php?action=toggle_featured', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ match_id: matchId, featured: current == '1' ? 0 : 1 })
            });
            let result;
            try {
                result = await response.json();
            } catch (jsonErr) {
                alert('Error: Invalid server response.');
                e.target.disabled = false;
                return;
            }
            if (result && result.message) {
                alert(result.message);
            } else {
                alert('Error: No message from server.');
            }
        } catch (err) {
            alert('Network or server error. Please try again.');
        }
        e.target.disabled = false;
        fetchFixtures();
    }
});

// Initialize
fetchTournaments().then(fetchFixtures);
</script>
<style>
.loading {
    text-align: center;
    padding: 2rem;
    color: #4fc3f7;
    font-size: 1.1em;
}

.error {
    text-align: center;
    padding: 2rem;
    color: #f44336;
    font-size: 1.1em;
}
.fixture-day { 
    margin-bottom: 2rem; 
}

.fixture-list { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 1rem; 
}

.fixture-card {
    background: rgba(34,52,58,0.95); /* dark, semi-transparent */
    border-radius: 12px;
    box-shadow: 0 4px 16px #0003;
    padding: 1.2rem;
    /* Key change: use flex-basis to control width and ensure 4 per row max */
    flex: 1 1 calc(25% - 0.75rem);
    min-width: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.5rem;
    border: 1.5px solid rgba(255,255,255,0.07);
    color: #e0e0e0;
    transition: box-shadow 0.2s, transform 0.2s;
}

.fixture-card:hover {
    box-shadow: 0 8px 32px #0005;
    transform: translateY(-2px) scale(1.015);
}

/* Media queries for responsive behavior */
@media (max-width: 1200px) {
    .fixture-card {
        flex: 1 1 calc(33.333% - 0.67rem); /* 3 per row on medium screens */
    }
}

@media (max-width: 900px) {
    .fixture-card {
        flex: 1 1 calc(50% - 0.5rem); /* 2 per row on smaller screens */
    }
}

@media (max-width: 600px) {
    .fixture-card {
        flex: 1 1 100%; /* 1 per row on mobile */
    }
    .fixture-player-img {
        width: 28px;
        height: 28px;
    }
    .fixture-players {
        flex-direction: column;
        gap: 0.2em;
    }
    .fixture-vs {
        margin: 0.2em 0;
    }
}
@media (max-width: 425px) {
    .fixture-list {
        gap: 0.5rem; /* Reduce gap on mobile */
        padding-left: 1rem; /* Add left padding to compensate for right gap */
    }
    .fixture-card {
        flex: 1 1 100%; /* 1 per row on mobile */
        min-width: unset; /* Remove min-width constraint on mobile */
        margin: 0; /* Remove any margins */
        width: 100%; /* Force full width */
    }
    .fixture-player-img {
        width: 22px;
        height: 22px;
    }
}
@media (max-width: 375px) {
    .fixture-list {
        padding-left: 0;
        margin-left: 0;
    }
}
.fixture-time { font-weight: bold; color: #4fc3f7; font-size: 1.1em; }
.fixture-players { display: flex; align-items: center; gap: 0.7em; margin-bottom: 0.2em; justify-content: center; text-align: center; flex-wrap: wrap; }
.fixture-player { display: flex; align-items: center; gap: 0.3em; font-size: 1.08em; font-weight: 500; justify-content: center; text-align: center; }
.fixture-player-img {
    width: 36px;
    height: 36px;
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #4fc3f7;
    background: #1a2327;
    margin-right: 0.2em;
    box-shadow: 0 2px 8px #0002;
}
.fixture-tournament { 
    color: #ffd54f; 
    font-weight: 500; 
    margin-bottom: 0.1em; 
    display: flex;
    align-items: center;
    gap: 0.5em;
    justify-content: center;
}
.fixture-tournament-logo {
    width: 20px;
    height: 20px;
    margin-right: 0.5em;
    border-radius: 50%;
}
.fixture-prediction-types {
    margin: 0.5rem 0;
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}
.prediction-badge {
    display: inline-block;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: bold;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.game-badge {
    background-color: #4caf50;
    color: white;
}
.stats-badge {
    background-color: #2196f3;
    color: white;
}
.fixture-status.upcoming { color: #4fc3f7; }
.fixture-status.in_progress { color: #ff9800; }
.fixture-status.finished { color: #43a047; }
.fixture-result { color: #b0bec5; font-size: 0.98em; }
.fixture-prediction-action { margin-top: 0.7rem; }
.btn-featured {
    margin-left: 0.2em;
    padding: 0.35em 1.1em;
    font-size: 1em;
    font-weight: 600;
    border-radius: 6px;
    border: 2px solid #ffd54f;
    background: #232b2f;
    color: #ffd54f;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border 0.2s;
    box-shadow: 0 2px 8px #0002;
    margin-top: 0.2em;
    margin-bottom: 0.2em;
    display: inline-block;
}
.btn-featured-on {
    background: #ffd54f;
    color: #232b2f;
    border-color: #ffd54f;
}
.btn-featured-off {
    background: #232b2f;
    color: #ffd54f;
    border-color: #ffd54f;
}
.btn-featured:hover {
    background: #fffde7;
    color: #bfa100;
    border-color: #ffe082;
}
.flag-icon { width: 32px; height: 24px; display: inline-block; margin-left: 0.18em; vertical-align: middle; box-shadow: 0 2px 8px #0002; border: 1.5px solid #e0e0e0; }
.styled-select {
    background: rgba(34,52,58,0.93);
    color: #ffd54f;
    border: 2px solid #ffd54f;
    border-radius: 8px;
    padding: 0.5em 1.2em;
    font-size: 1.1em;
    font-weight: 600;
    outline: none;
    box-shadow: 0 2px 8px #0002;
    transition: border 0.2s, box-shadow 0.2s;
}
.styled-select:focus {
    border: 2px solid #4fc3f7;
    box-shadow: 0 4px 16px #4fc3f755;
}
.fixture-vs {
    margin: 0 0.5em;
    font-weight: bold;
    align-self: center;
    font-size: 1.1em;
}
</style>
<?php require_once 'includes/footer.php'; ?> 