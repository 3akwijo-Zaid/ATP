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
        'AFG': 'af', 'ALB': 'al', 'DZA': 'dz', 'AND': 'ad', 'AGO': 'ao', 'ARG': 'ar', 'ARM': 'am', 'AUS': 'au', 'AUT': 'at', 'AZE': 'az',
        'BHS': 'bs', 'BHR': 'bh', 'BGD': 'bd', 'BRB': 'bb', 'BLR': 'by', 'BEL': 'be', 'BLZ': 'bz', 'BEN': 'bj', 'BTN': 'bt', 'BOL': 'bo',
        'BIH': 'ba', 'BWA': 'bw', 'BRA': 'br', 'BRN': 'bn', 'BGR': 'bg', 'BFA': 'bf', 'BDI': 'bi', 'KHM': 'kh', 'CMR': 'cm', 'CAN': 'ca',
        'CPV': 'cv', 'CAF': 'cf', 'TCD': 'td', 'CHL': 'cl', 'CHN': 'cn', 'COL': 'co', 'COM': 'km', 'COG': 'cg', 'COD': 'cd', 'CRI': 'cr',
        'CIV': 'ci', 'HRV': 'hr', 'CUB': 'cu', 'CYP': 'cy', 'CZE': 'cz', 'DNK': 'dk', 'DJI': 'dj', 'DMA': 'dm', 'DOM': 'do', 'ECU': 'ec',
        'EGY': 'eg', 'SLV': 'sv', 'GNQ': 'gq', 'ERI': 'er', 'EST': 'ee', 'SWZ': 'sz', 'ETH': 'et', 'FJI': 'fj', 'FIN': 'fi', 'FRA': 'fr',
        'GAB': 'ga', 'GMB': 'gm', 'GEO': 'ge', 'DEU': 'de', 'GHA': 'gh', 'GRC': 'gr', 'GRD': 'gd', 'GTM': 'gt', 'GIN': 'gn', 'GNB': 'gw',
        'GUY': 'gy', 'HTI': 'ht', 'HND': 'hn', 'HUN': 'hu', 'ISL': 'is', 'IND': 'in', 'IDN': 'id', 'IRN': 'ir', 'IRQ': 'iq',
        // 'ISR': 'il', // Excluded
        'ITA': 'it', 'JAM': 'jm', 'JPN': 'jp', 'JOR': 'jo', 'KAZ': 'kz', 'KEN': 'ke', 'KIR': 'ki', 'PRK': 'kp', 'KOR': 'kr',
        'KWT': 'kw', 'KGZ': 'kg', 'LAO': 'la', 'LVA': 'lv', 'LBN': 'lb', 'LSO': 'ls', 'LBR': 'lr', 'LBY': 'ly', 'LIE': 'li', 'LTU': 'lt',
        'LUX': 'lu', 'MDG': 'mg', 'MWI': 'mw', 'MYS': 'my', 'MDV': 'mv', 'MLI': 'ml', 'MLT': 'mt', 'MHL': 'mh', 'MRT': 'mr', 'MUS': 'mu',
        'MEX': 'mx', 'FSM': 'fm', 'MDA': 'md', 'MCO': 'mc', 'MNG': 'mn', 'MNE': 'me', 'MAR': 'ma', 'MOZ': 'mz', 'MMR': 'mm', 'NAM': 'na',
        'NRU': 'nr', 'NPL': 'np', 'NLD': 'nl', 'NZL': 'nz', 'NIC': 'ni', 'NER': 'ne', 'NGA': 'ng', 'MKD': 'mk', 'NOR': 'no', 'OMN': 'om',
        'PAK': 'pk', 'PLW': 'pw', 'PSE': 'ps', 'PAN': 'pa', 'PNG': 'pg', 'PRY': 'py', 'PER': 'pe', 'PHL': 'ph', 'POL': 'pl', 'PRT': 'pt',
        'QAT': 'qa', 'ROU': 'ro', 'RUS': 'ru', 'RWA': 'rw', 'KNA': 'kn', 'LCA': 'lc', 'VCT': 'vc', 'WSM': 'ws', 'SMR': 'sm', 'STP': 'st',
        'SAU': 'sa', 'SEN': 'sn', 'SRB': 'rs', 'SYC': 'sc', 'SLE': 'sl', 'SGP': 'sg', 'SVK': 'sk', 'SVN': 'si', 'SLB': 'sb', 'SOM': 'so',
        'ZAF': 'za', 'SSD': 'ss', 'ESP': 'es', 'LKA': 'lk', 'SDN': 'sd', 'SUR': 'sr', 'SWE': 'se', 'CHE': 'ch', 'SYR': 'sy', 'TWN': 'tw',
        'TJK': 'tj', 'TZA': 'tz', 'THA': 'th', 'TLS': 'tl', 'TGO': 'tg', 'TON': 'to', 'TTO': 'tt', 'TUN': 'tn', 'TUR': 'tr', 'TKM': 'tm',
        'TUV': 'tv', 'UGA': 'ug', 'UKR': 'ua', 'ARE': 'ae', 'GBR': 'gb', 'USA': 'us', 'URY': 'uy', 'UZB': 'uz', 'VUT': 'vu', 'VEN': 've',
        'VNM': 'vn', 'YEM': 'ye', 'ZMB': 'zm', 'ZWE': 'zw', 'Other': 'un',
        // Also support 2-letter codes for flexibility
        'AF': 'af', 'AL': 'al', 'DZ': 'dz', 'AD': 'ad', 'AO': 'ao', 'AR': 'ar', 'AM': 'am', 'AU': 'au', 'AT': 'at', 'AZ': 'az',
        'BS': 'bs', 'BH': 'bh', 'BD': 'bd', 'BB': 'bb', 'BY': 'by', 'BE': 'be', 'BZ': 'bz', 'BJ': 'bj', 'BT': 'bt', 'BO': 'bo',
        'BA': 'ba', 'BW': 'bw', 'BR': 'br', 'BN': 'bn', 'BG': 'bg', 'BF': 'bf', 'BI': 'bi', 'KH': 'kh', 'CM': 'cm', 'CA': 'ca',
        'CV': 'cv', 'CF': 'cf', 'TD': 'td', 'CL': 'cl', 'CN': 'cn', 'CO': 'co', 'KM': 'km', 'CG': 'cg', 'CD': 'cd', 'CR': 'cr',
        'CI': 'ci', 'HR': 'hr', 'CU': 'cu', 'CY': 'cy', 'CZ': 'cz', 'DK': 'dk', 'DJ': 'dj', 'DM': 'dm', 'DO': 'do', 'EC': 'ec',
        'EG': 'eg', 'SV': 'sv', 'GQ': 'gq', 'ER': 'er', 'EE': 'ee', 'SZ': 'sz', 'ET': 'et', 'FJ': 'fj', 'FI': 'fi', 'FR': 'fr',
        'GA': 'ga', 'GM': 'gm', 'GE': 'ge', 'DE': 'de', 'GH': 'gh', 'GR': 'gr', 'GD': 'gd', 'GT': 'gt', 'GN': 'gn', 'GW': 'gw',
        'GY': 'gy', 'HT': 'ht', 'HN': 'hn', 'HU': 'hu', 'IS': 'is', 'IN': 'in', 'ID': 'id', 'IR': 'ir', 'IQ': 'iq',
        // 'IL': 'il', // Excluded
        'IT': 'it', 'JM': 'jm', 'JP': 'jp', 'JO': 'jo', 'KZ': 'kz', 'KE': 'ke', 'KI': 'ki', 'KP': 'kp', 'KR': 'kr',
        'KW': 'kw', 'KG': 'kg', 'LA': 'la', 'LV': 'lv', 'LB': 'lb', 'LS': 'ls', 'LR': 'lr', 'LY': 'ly', 'LI': 'li', 'LT': 'lt',
        'LU': 'lu', 'MG': 'mg', 'MW': 'mw', 'MY': 'my', 'MV': 'mv', 'ML': 'ml', 'MT': 'mt', 'MH': 'mh', 'MR': 'mr', 'MU': 'mu',
        'MX': 'mx', 'FM': 'fm', 'MD': 'md', 'MC': 'mc', 'MN': 'mn', 'ME': 'me', 'MA': 'ma', 'MZ': 'mz', 'MM': 'mm', 'NA': 'na',
        'NR': 'nr', 'NP': 'np', 'NL': 'nl', 'NZ': 'nz', 'NI': 'ni', 'NE': 'ne', 'NG': 'ng', 'MK': 'mk', 'NO': 'no', 'OM': 'om',
        'PK': 'pk', 'PW': 'pw', 'PS': 'ps', 'PA': 'pa', 'PG': 'pg', 'PY': 'py', 'PE': 'pe', 'PH': 'ph', 'PL': 'pl', 'PT': 'pt',
        'QA': 'qa', 'RO': 'ro', 'RU': 'ru', 'RW': 'rw', 'KN': 'kn', 'LC': 'lc', 'VC': 'vc', 'WS': 'ws', 'SM': 'sm', 'ST': 'st',
        'SA': 'sa', 'SN': 'sn', 'RS': 'rs', 'SC': 'sc', 'SL': 'sl', 'SG': 'sg', 'SK': 'sk', 'SI': 'si', 'SB': 'sb', 'SO': 'so',
        'ZA': 'za', 'SS': 'ss', 'ES': 'es', 'LK': 'lk', 'SD': 'sd', 'SR': 'sr', 'SE': 'se', 'CH': 'ch', 'SY': 'sy', 'TW': 'tw',
        'TJ': 'tj', 'TZ': 'tz', 'TH': 'th', 'TL': 'tl', 'TG': 'tg', 'TO': 'to', 'TT': 'tt', 'TN': 'tn', 'TR': 'tr', 'TM': 'tm',
        'TV': 'tv', 'UG': 'ug', 'UA': 'ua', 'AE': 'ae', 'GB': 'gb', 'US': 'us', 'UY': 'uy', 'UZ': 'uz', 'VU': 'vu', 'VE': 've',
        'VN': 'vn', 'YE': 'ye', 'ZM': 'zm', 'ZW': 'zw'
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