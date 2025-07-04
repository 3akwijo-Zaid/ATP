<?php require_once 'includes/header.php'; ?>
<?php $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixtures & Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">
    <style>
        .fixtures-section-title-wrapper {
            width: 100%;
            text-align: center;
        }
        .fixtures-section-title {
            font-size: 2.1rem;
            font-weight: 900;
            margin-top: 2.2rem;
            margin-bottom: 1.2rem;
            letter-spacing: 0.02em;
            color: #fff;
            text-align: center;
            text-shadow: 0 2px 8px #0006, 0 1px 4px #ffd54f44;
            display: inline-block;
            padding-bottom: 0.2rem;
        }
        @media (max-width: 700px) {
            .fixtures-section-title {
                font-size: 1.4rem;
                margin-top: 1.2rem;
                margin-bottom: 0.7rem;
                text-align: center;
                padding-bottom: 0.2rem;
            }
        }
    </style>
</head>
<div class="container container--full mt-4">
    <h2>Fixtures & Results</h2>
    <div class="mb-3" style="margin-left: 0.5em;">
        <label for="tournamentFilter" class="form-label">Filter by Tournament:</label>
        <select id="tournamentFilter" class="styled-select"></select><br>
    </div><br>
    <div id="upcoming-matches-list"></div>
    <div id="match-results-list"></div>
</div>
<script>
const USER_ID = <?php echo $user_id; ?>;
const IS_ADMIN = <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 'true' : 'false'; ?>;

function formatCountdown(seconds) {
    if (seconds < 0) seconds = 0;
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return `${h}h ${m.toString().padStart(2, '0')}m ${s.toString().padStart(2, '0')}s`;
}

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

function getFriendlyDateLabel(dateStr) {
    const today = new Date();
    const date = new Date(dateStr);
    // Normalize to midnight for comparison
    today.setHours(0,0,0,0);
    date.setHours(0,0,0,0);
    const diffDays = Math.round((date - today) / (1000 * 60 * 60 * 24));
    if (diffDays === 0) return 'Today';
    if (diffDays === -1) return 'Yesterday';
    if (diffDays === 1) return 'Tomorrow';
    // Otherwise, return the date in a readable format
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
}

function getMatchStatus(match) {
    const startTime = new Date(match.start_time);
    const now = new Date();
    const secondsToStart = Math.floor((startTime - now) / 1000);
    const secondsToLock = secondsToStart - 300; // 5 minutes before start

    if (match.status === 'finished') {
        return { status: 'finished', text: 'Finished', color: '#43a047', class: 'finished' };
    } else if (match.status === 'in_progress') {
        return { status: 'in_progress', text: 'In Progress', color: '#2196f3', class: 'in_progress' };
    } else if (secondsToLock > 0) {
        return { status: 'open', text: 'Prediction open', color: '#43a047', class: 'open' };
    } else if (secondsToStart > 0) {
        return { status: 'locked', text: 'Locked', color: '#ff9800', class: 'locked' };
    } else {
        return { status: 'started', text: 'Match started', color: '#757575', class: 'started' };
    }
}

function getCountdownText(match) {
    const startTime = new Date(match.start_time);
    const now = new Date();
    const secondsToStart = Math.floor((startTime - now) / 1000);
    const secondsToLock = secondsToStart - 300;

    if (match.status === 'finished') {
        return '';
    } else if (match.status === 'in_progress') {
        return 'Live';
    } else if (secondsToLock > 0) {
        return formatCountdown(secondsToLock) + ' left to predict';
    } else if (secondsToStart > 0) {
        return 'Locked (' + formatCountdown(secondsToStart) + ' to start)';
    } else {
        return '';
    }
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
    let upcomingHtml = '<div class="fixtures-section-title-wrapper"><div class="fixtures-section-title">Upcoming Matches</div></div>';
    let resultsHtml = '<div class="fixtures-section-title-wrapper"><div class="fixtures-section-title">Match Results</div></div>';
    let hasUpcoming = false;
    let hasResults = false;
    let finishedMatchesFlat = [];
    if (!data || !data.length) {
        upcomingHtml += '<p>No upcoming matches found.</p>';
        resultsHtml += '<p>No match results found.</p>';
    } else {
        for (const day of data) {
            const friendlyDate = getFriendlyDateLabel(day.date);
            // Group upcoming matches
            const upcomingMatches = day.matches.filter(m => m.status === 'upcoming');
            if (upcomingMatches.length) {
                hasUpcoming = true;
                upcomingHtml += `<div class='fixture-day'><h4>${friendlyDate}</h4><div class='fixture-list'>`;
                for (const m of upcomingMatches) {
                    let predictionHtml = '';
                    if (USER_ID) {
                        const prediction = await fetchPrediction(m.id);
                        const matchStatus = getMatchStatus(m);
                        let isLocked = false;
                        
                        if (matchStatus.status === 'locked' || matchStatus.status === 'started') {
                            isLocked = true;
                        }
                        
                        if (isLocked) {
                            predictionHtml = `<button class='btn btn-locked btn-sm' disabled>Locked</button>`;
                        } else if (prediction) {
                            predictionHtml = `<button class='btn btn-outline-success btn-sm' onclick='window.location=\"predictions.php?match_id=${m.id}\"'>View/Edit Prediction</button>`;
                        } else {
                            predictionHtml = `<button class='btn btn-primary btn-sm' onclick='window.location=\"predictions.php?match_id=${m.id}\"'>Predict</button>`;
                        }
                    } else {
                        predictionHtml = `<a href='login.php?redirect=fixtures.php' class='btn btn-secondary btn-sm'>Login to Predict</a>`;
                    }
                    let featuredHtml = '';
                    if (IS_ADMIN) {
                        featuredHtml = `<button class='btn btn-featured ${m.featured == 1 ? 'btn-featured-on' : 'btn-featured-off'}' data-match-id='${m.id}' data-featured='${m.featured || 0}'>${m.featured == 1 ? '★ Featured' : '☆ Make Featured'}</button>`;
                    }
                    
                    const matchStatus = getMatchStatus(m);
                    upcomingHtml += `<div class='fixture-card' data-start='${m.start_time}' data-match-id='${m.id}'>
                        <div class='fixture-tournament'>
                            ${m.tournament_logo ? `<img src='${m.tournament_logo}' alt='${m.tournament_name}' class='fixture-tournament-logo'>` : ''}
                            <span>${m.tournament_name} (${m.round})</span>
                        </div>
                        <div class='fixture-time'><span class='match-date time-only' data-utc1='${m.start_time}'></span></div>
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
                        <div class='fixture-status ${matchStatus.class}' style='background:${matchStatus.color};color:#fff;'>${matchStatus.text}</div>
                        <div class='fixture-countdown' id='fixture-countdown-${m.id}'></div>
                        <div class='fixture-result'></div>
                        <div class='fixture-prediction-action'>
                            ${predictionHtml}
                            ${IS_ADMIN ? `<div style='margin-top:0.7em;'>${featuredHtml}</div>` : ''}
                        </div>
                    </div>`;
                }
                upcomingHtml += `</div></div>`;
            }
            // Collect finished matches for flat pagination
            const finishedMatches = day.matches.filter(m => m.status === 'finished');
            if (finishedMatches.length) {
                hasResults = true;
                finishedMatches.forEach(m => finishedMatchesFlat.push({ ...m, date: day.date }));
            }
        }
        // Sort finished matches by date descending, then by start_time descending
        finishedMatchesFlat.sort((a, b) => {
            if (a.date !== b.date) return b.date.localeCompare(a.date);
            return (b.start_time || '').localeCompare(a.start_time || '');
        });
        // Pagination logic
        let showCount = 5;
        let shown = 0;
        function renderResultsBatch() {
            let html = '';
            let lastDate = null;
            let count = 0;
            for (let i = 0; i < finishedMatchesFlat.length && count < showCount; i++) {
                const m = finishedMatchesFlat[i];
                if (m.date !== lastDate) {
                    html += `<div class='fixture-day'><h4>${getFriendlyDateLabel(m.date)}</h4><div class='fixture-list'>`;
                    lastDate = m.date;
                }
                let predictionHtml = '';
                if (USER_ID) {
                    const prediction = m._userPrediction; // Pre-fetched if needed
                    if (prediction) {
                        predictionHtml = `<button class='btn btn-info btn-sm' onclick='window.location.href=\"predictions.php?match_id=${m.id}\"'>View Your Prediction</button>`;
                    }
                }
                let featuredHtml = '';
                if (IS_ADMIN) {
                    featuredHtml = `<button class='btn btn-featured ${m.featured == 1 ? 'btn-featured-on' : 'btn-featured-off'}' data-match-id='${m.id}' data-featured='${m.featured || 0}'>${m.featured == 1 ? '★ Featured' : '☆ Make Featured'}</button>`;
                }
                html += `<div class='fixture-card finished-card'>
                    <div class='fixture-tournament'>
                        ${m.tournament_logo ? `<img src='${m.tournament_logo}' alt='${m.tournament_name}' class='fixture-tournament-logo'>` : ''}
                        <span>${m.tournament_name} (${m.round})</span>
                    </div>
                    <div class='fixture-time'><span class='match-date time-only' data-utc1='${m.start_time}'></span></div>
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
                    <div class='fixture-result finished-result'>${m.result_summary ? 'Result: ' + m.result_summary : ''}</div>
                    <div class='fixture-prediction-action'>
                        ${predictionHtml}
                        ${IS_ADMIN ? `<div style='margin-top:0.7em;'>${featuredHtml}</div>` : ''}
                    </div>
                </div>`;
                count++;
                shown++;
                // Close day div if next match is a different day or last in batch
                if (i + 1 === finishedMatchesFlat.length || finishedMatchesFlat[i + 1].date !== lastDate || count === showCount) {
                    html += `</div></div>`;
                }
            }
            document.getElementById('match-results-list').innerHTML = resultsHtml + html;
            // Show More button
            let showMoreBtn = document.getElementById('show-more-results-btn');
            if (shown < finishedMatchesFlat.length) {
                if (!showMoreBtn) {
                    showMoreBtn = document.createElement('button');
                    showMoreBtn.id = 'show-more-results-btn';
                    showMoreBtn.className = 'btn btn--outline';
                    showMoreBtn.textContent = 'Show More Results';
                    showMoreBtn.style.display = 'block';
                    showMoreBtn.style.margin = '2em auto';
                    showMoreBtn.style.textAlign = 'center';
                    showMoreBtn.onclick = function() {
                        showCount += 5;
                        renderResultsBatch();
                    };
                    document.getElementById('match-results-list').appendChild(showMoreBtn);
                }
            } else if (showMoreBtn) {
                showMoreBtn.remove();
            }
        }
        renderResultsBatch();
    }
    document.getElementById('upcoming-matches-list').innerHTML = upcomingHtml;
    if (typeof updateMatchDates === 'function') updateMatchDates();
    startFixturesCountdowns();
}

function startFixturesCountdowns() {
    setInterval(() => {
        document.querySelectorAll('.fixture-card[data-start]').forEach(card => {
            const startTime = new Date(card.getAttribute('data-start'));
            const matchId = card.getAttribute('data-match-id');
            const countdownEl = document.getElementById(`fixture-countdown-${matchId}`);
            
            if (countdownEl) {
                const match = {
                    start_time: card.getAttribute('data-start'),
                    status: card.querySelector('.fixture-status').classList.contains('finished') ? 'finished' : 
                           card.querySelector('.fixture-status').classList.contains('in_progress') ? 'in_progress' : 'upcoming'
                };
                
                const countdownText = getCountdownText(match);
                
                // Remove countdown div if text is empty
                if (countdownText === '') {
                    countdownEl.remove();
                } else {
                    countdownEl.textContent = countdownText;
                }
                
                // Update status if needed
                const statusEl = card.querySelector('.fixture-status');
                if (statusEl && !statusEl.classList.contains('finished') && !statusEl.classList.contains('in_progress')) {
                    const newStatus = getMatchStatus(match);
                    statusEl.className = `fixture-status ${newStatus.class}`;
                    statusEl.style.background = newStatus.color;
                    statusEl.style.color = '#fff';
                    statusEl.textContent = newStatus.text;
                }
            }
        });
    }, 1000);
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

.btn-locked {
    background: #f44336 !important;
    border-color: #f44336 !important;
    color: #fff !important;
    cursor: not-allowed !important;
    font-weight: 600;
    opacity: 1;
}
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
    #show-more-results-btn.btn--outline {
        padding: 0.5em 1.2em !important;
        font-size: 0.97em !important;
        min-width: 120px;
        max-width: 90vw;
        transform: translateX(10px);
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
.fixture-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5em;
    padding: 0.22em 1.2em;
    font-size: 1em;
    font-weight: 700;
    border-radius: var(--radius-xl);
    margin: 0.2em 0;
    letter-spacing: 0.5px;
    text-transform: capitalize;
    background: var(--surface-medium);
    border: 2px solid transparent;
    box-shadow: var(--shadow-md);
    transition: background 0.2s, color 0.2s, border 0.2s;
    min-width: 110px;
    justify-content: center;
}
.fixture-status.upcoming {
    color: #4fc3f7;
    background: rgba(33, 150, 243, 0.10);
    border-color: #4fc3f7;
}
.fixture-status.open {
    color: #43a047;
    background: rgba(67, 160, 71, 0.10);
    border-color: #43a047;
}
.fixture-status.locked {
    color: #ff9800;
    background: rgba(255, 152, 0, 0.10);
    border-color: #ff9800;
}
.fixture-status.started {
    color: #757575;
    background: rgba(117, 117, 117, 0.10);
    border-color: #757575;
}
.fixture-status.in_progress {
    color: var(--primary-teal);
    background: rgba(23, 162, 184, 0.13);
    border-color: var(--primary-teal);
}
.fixture-status.finished {
    color: #43a047;
    background: none;
    border: none;
    font-weight: 500;
    box-shadow: none;
    padding-left: 0;
    text-align: center;
    width: 100%;
    justify-content: center;
    display: flex;
    align-items: center;
}
.fixture-countdown {
    font-size: 0.97em;
    color: #b0bec5;
    margin-top: 0.1em;
    text-align: center;
    min-height: 1.2em;
}
.status-icon {
    font-size: 1.1em;
    color: var(--primary-gold-darker);
    margin-right: 0.2em;
    vertical-align: middle;
}
.fixture-result {
    color: var(--text-secondary);
    font-size: 1.05em;
    margin-top: 0.2em;
    margin-bottom: 0.2em;
    padding: 0.3em 1.1em;
    border-radius: var(--radius-lg);
    background: none;
    border: none;
    display: inline-block;
    min-width: 90px;
    font-weight: 500;
    box-shadow: none;
    border-bottom: 1.5px solid var(--surface-medium);
}
.fixture-result.finished-result {
    color:rgb(255, 255, 255);
    background: none;
    border: none;
    font-weight: 500;
    border-bottom: 2px solid rgb(255, 255, 255);
    box-shadow: none;
    padding-left: 0;
    text-align: center;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
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

.btn-locked {
    background: #f44336 !important;
    border-color: #f44336 !important;
    color: #fff !important;
    cursor: not-allowed !important;
    font-weight: 600;
    opacity: 1;
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
.fixture-card.finished-card {
    border-left: 6px solid #43a047; /* Green for finished */
    box-shadow: 0 4px 24px 0 rgba(67, 160, 71, 0.10);
    background: rgba(34,52,58,0.98);
}
</style>
<?php require_once 'includes/footer.php'; ?> 