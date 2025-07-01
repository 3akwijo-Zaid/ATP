<?php require_once 'includes/header.php'; ?>

<div class="container container--full">
    <h1>Welcome to Tennis Predictions</h1>
    <p class="text-center mb-lg">Predict match outcomes and compete on the scoreboard!</p>

    <h2>Featured Matches</h2>
    <div id="featured-matches" class="featured-matches"></div>

    <h2>Fixtures</h2>
    <div id="fixtures-preview" class="grid gap-lg"></div>
    <div class="text-center mt-md">
        <a href="fixtures.php" class="btn btn--outline">View All Fixtures</a>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const featuredMatches = document.getElementById('featured-matches');

    function formatCountdown(seconds) {
        if (seconds < 0) seconds = 0;
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        return `${h}h ${m.toString().padStart(2, '0')}m ${s.toString().padStart(2, '0')}s`;
    }

    async function fetchFeaturedMatches() {
        try {
            const response = await fetch('../api/matches.php?featured=1');
            const result = await response.json();
            featuredMatches.innerHTML = '';
            if (result.success && result.matches && result.matches.length > 0) {
                result.matches.forEach((match, index) => {
                    const start = new Date(match.start_time);
                    const id = `featured-countdown-${index}`;
                    const card = document.createElement('div');
                    card.className = 'match-card';
                    card.setAttribute('data-start', match.start_time);
                    card.setAttribute('data-idx', index);
                    card.setAttribute('data-match-id', match.id);

                    card.innerHTML = `
                        <div class="match-badge">FEATURED</div>
                        <div class="match-tournament">${match.competition_name || 'Tournament'}</div>
                        <div class="match-round">${match.round || ''}</div>

                        <div class="match-players">
                            <div class="match-player">
                                ${match.player1_image ? `<img src=\"${match.player1_image}\" class=\"match-player-img\">` : ''}
                                <div class="match-player-name">
                                    <div class="player-first-name">${match.player1_name.split(' ')[0] || match.player1_name}</div>
                                    <div class="player-last-name">${match.player1_name.split(' ').slice(1).join(' ') || ''}</div>
                                </div>
                            </div>
                            <div class="match-vs">vs</div>
                            <div class="match-player">
                                ${match.player2_image ? `<img src=\"${match.player2_image}\" class=\"match-player-img\">` : ''}
                                <div class="match-player-name">
                                    <div class="player-first-name">${match.player2_name.split(' ')[0] || match.player2_name}</div>
                                    <div class="player-last-name">${match.player2_name.split(' ').slice(1).join(' ') || ''}</div>
                                </div>
                            </div>
                        </div>

                        <div class="match-time">${start.toLocaleString()}</div>
                        <a id="${id}" class="match-predict-btn disabled">Loading...</a>
                    `;
                    featuredMatches.appendChild(card);
                });
                startFeaturedCountdowns();
            } else if (result.success && (!result.matches || result.matches.length === 0)) {
                featuredMatches.innerHTML = '<p style="color:#fff;">No featured matches available.</p>';
            } else {
                featuredMatches.innerHTML = `<p style=\"color:#fff;\">${result.message || 'Could not load featured matches. Please try again.'}</p>`;
            }
        } catch (error) {
            console.error('Error fetching featured matches:', error);
            featuredMatches.innerHTML = '<p style="color:#fff;">Could not load featured matches. Please try again.</p>';
        }
    }

    function startFeaturedCountdowns() {
        setInterval(() => {
            document.querySelectorAll('.match-card').forEach(card => {
                const start = new Date(card.getAttribute('data-start'));
                const now = new Date();
                const idx = card.getAttribute('data-idx');
                const matchId = card.getAttribute('data-match-id');
                const countdownEl = document.getElementById(`featured-countdown-${idx}`);
                const secondsToStart = Math.floor((start - now) / 1000);
                const secondsToLock = secondsToStart - 3600;

                if (secondsToLock > 0) {
                    countdownEl.textContent = `Predict Now (closes in ${formatCountdown(secondsToLock)})`;
                    countdownEl.classList.remove('disabled');
                    countdownEl.href = `predictions.php?match_id=${matchId}`;
                } else {
                    countdownEl.textContent = 'Predictions closed';
                    countdownEl.classList.add('disabled');
                    countdownEl.removeAttribute('href');
                }
            });
        }, 1000);
    }

    fetchFeaturedMatches();
    fetchFixturesPreview();
});

async function fetchFixturesPreview() {
    try {
        const res = await fetch('../api/matches.php?grouped=1');
        const data = await res.json();
        const preview = document.getElementById('fixtures-preview');
        let html = '';
        let daysShown = 0;
        let fixtureIndex = 0;

        for (const day of data) {
            if (daysShown >= 2) break;
            html += `<div class='fixtures-preview-day'><h4>${day.date}</h4><div class='fixtures-preview-list'>`;

            let matchesShown = 0;
            for (const m of day.matches) {
                if (matchesShown >= 3) break;
                const startTime = new Date(m.start_time);
                const now = new Date();
                const secondsToStart = Math.floor((startTime - now) / 1000);
                const secondsToLock = secondsToStart - 3600;

                let badge = '', badgeColor = '', badgeClass = '';

                if (m.status === 'finished') {
                    badge = 'Finished';
                    badgeColor = '#43a047';
                    badgeClass = 'finished';
                } else if (m.status === 'in_progress') {
                    badge = 'In Progress';
                    badgeColor = '#2196f3';
                    badgeClass = 'in_progress';
                } else if (secondsToLock > 0) {
                    badge = 'Prediction open';
                    badgeColor = '#43a047';
                    badgeClass = 'open';
                } else if (secondsToStart > 0) {
                    badge = 'Locked';
                    badgeColor = '#ff9800';
                    badgeClass = 'locked';
                } else {
                    badge = 'Match started';
                    badgeColor = '#757575';
                    badgeClass = 'started';
                }

                html += `<div class='fixtures-preview-card' data-start='${m.start_time}' data-fixture-idx='${fixtureIndex}'>
                    <div class='fixtures-preview-tournament'>${m.tournament_name} (${m.round})</div>
                    <div class='fixtures-preview-time'>${m.start_time.substr(11,5)}</div>
                    <div class='fixtures-preview-players'>
                        <span class='fixtures-preview-player'>
                            ${m.player1_image ? `<img src='${m.player1_image}' alt='${m.player1_name}' class='fixtures-preview-player-img'>` : ''}
                            <b>${m.player1_name}</b>
                        </span>
                        <span style='margin: 0 0.4em; font-weight: bold;'>vs</span>
                        <span class='fixtures-preview-player'>
                            ${m.player2_image ? `<img src='${m.player2_image}' alt='${m.player2_name}' class='fixtures-preview-player-img'>` : ''}
                            <b>${m.player2_name}</b>
                        </span>
                    </div>
                    <span class='fixtures-preview-badge ${badgeClass}' style='background:${badgeColor};color:#fff;padding:0.25em 0.9em;border-radius:1em;font-size:0.97em;'>${badge}</span>
                    <span class='fixtures-preview-countdown' id='fixture-countdown-${fixtureIndex}'></span>
                </div>`;

                fixtureIndex++;
                matchesShown++;
            }

            html += '</div></div>';
            daysShown++;
        }

        if (!html) html = '<p style="color:#b0bec5;">No fixtures found.</p>';
        preview.innerHTML = html;
        startFixturesCountdowns();
    } catch (error) {
        console.error('Error fetching fixtures:', error);
        document.getElementById('fixtures-preview').innerHTML = '<p style="color:#fff;">Could not load fixtures. Please try again.</p>';
    }
}

function startFixturesCountdowns() {
    setInterval(() => {
        document.querySelectorAll('.fixtures-preview-card').forEach(card => {
            const start = new Date(card.getAttribute('data-start'));
            const now = new Date();
            const secondsToStart = Math.floor((start - now) / 1000);
            const secondsToLock = secondsToStart - 3600;
            let countdownText = '';

            const badgeClass = card.querySelector('.fixtures-preview-badge').classList;

            if (badgeClass.contains('finished')) {
                countdownText = '';
            } else if (badgeClass.contains('in_progress')) {
                countdownText = 'Live';
            } else if (secondsToLock > 0) {
                countdownText = formatCountdown(secondsToLock) + ' left to predict';
            } else if (secondsToStart > 0) {
                countdownText = 'Locked (' + formatCountdown(secondsToStart) + ' to start)';
            } else {
                countdownText = 'Match started';
            }

            const idx = card.getAttribute('data-fixture-idx');
            const el = document.getElementById('fixture-countdown-' + idx);
            if (el) el.textContent = countdownText;
        });
    }, 1000);
}

</script>

<style>
#featured-matches {
    display: flex;
    flex-wrap: wrap;
    gap: 2em;
    margin-top: 2em;
    justify-content: center;
    padding: 1em 2em;
}

.match-card {
    background: #2e3b4e;
    border-radius: 20px;
    box-shadow: 0 6px 32px rgba(0, 0, 0, 0.4);
    padding: 2.2em 2em;
    width: 100%;
    max-width: 480px;
    color: #fff;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 1.4em;
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.match-card:hover {
    transform: scale(1.015);
    box-shadow: 0 10px 48px rgba(0, 0, 0, 0.6);
}

.match-badge {
    background: #ffeb3b;
    color: #222;
    padding: 0.4em 1.2em;
    font-weight: bold;
    border-radius: 1.2em;
    font-size: 0.95em;
    position: absolute;
    top: 1.2em;
    left: 1.2em;
}

.match-tournament {
    font-size: 1.5em;
    font-weight: 700;
    color: #ffd54f;
    margin-top: 2.5em;
}

.match-round {
    font-size: 1.2em;
    color: #ccc;
}

.match-players {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3em;
    margin-bottom: 1.2em;
}

.match-player {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-weight: bold;
    font-size: 1.05em;
}

.match-player-img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ffd54f;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.6);
}

.match-vs {
    font-size: 2.2em;
    font-weight: bold;
    color: #ffd600;
    margin: 0 0.5em 0 0.5em;
    align-self: center;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    position: relative;
    top: -40px;
    z-index: 1;
}

.match-time {
    font-size: 1.05em;
    color: #b0bec5;
    margin-top: 0.5em;
}

.match-predict-btn {
    text-decoration: none;
    padding: 1em 2em;
    font-size: 1.05em;
    font-weight: 700;
    border-radius: 12px;
    background: #00bcd4;
    color: #fff;
    transition: background 0.2s ease, transform 0.2s ease;
    display: inline-block;
}

.match-predict-btn:hover {
    background: #26c6da;
    transform: translateY(-2px);
}

.match-predict-btn.disabled {
    background: #e53935 !important;
    pointer-events: none;
    cursor: not-allowed;
}


/* Mobile */
@media (max-width: 768px) {
    .match-card {
        max-width: 90%;
    }
}

@media (max-width: 600px) {
    .match-card .match-players {
        flex-direction: row;
        gap: 0.7em;
        margin-bottom: 0.8em;
        justify-content: center;
        align-items: center;
    }
    .match-card .match-player-img {
        width: 56px !important;
        height: 72px !important;
        border-width: 2px;
    }
    .match-card .match-vs {
        font-size: 1.1em;
        margin: 0;
        align-self: center;
        min-width: 2.5em;
        text-align: center;
        top: 0;
        position: static;
        transform: translateY(-12px);
    }
}

@media (max-width: 425px) {
    .fixtures-preview-list, a.btn--outline {
        transform: translateX(0.6em);
    }
}

@media (max-width: 375px) {
    .fixtures-preview-list, a.btn--outline { 
        margin: 0 !important;
        padding: 0 !important;
        transform: translateX(0);
    }
}

.fixtures-preview {
    margin-top: 2.5em;
    margin-bottom: 1.5em;
}
.fixtures-preview-day {
    margin-bottom: 1.7em;
}
.fixtures-preview-list {
    display: flex;
    flex-wrap: wrap;
    gap: 1.1em;
}
.fixtures-preview-card {
    background: rgba(34,52,58,0.93);
    border-radius: 10px;
    box-shadow: 0 2px 12px #0003;
    padding: 1.1em 1.2em;
    min-width: 210px;
    flex: 1 1 210px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.4em;
    border: 1.5px solid rgba(255,255,255,0.07);
    color: #e0e0e0;
    transition: box-shadow 0.2s, transform 0.2s;
}
.fixtures-preview-card:hover {
    box-shadow: 0 6px 24px #0005;
    transform: translateY(-2px) scale(1.01);
}
.fixtures-preview-time {
    font-weight: bold;
    color: #4fc3f7;
    font-size: 1.05em;
}
.fixtures-preview-players {
    display: flex;
    align-items: center;
    gap: 0.5em;
    margin-bottom: 0.1em;
}
.fixtures-preview-player {
    display: flex;
    align-items: center;
    gap: 0.2em;
    font-size: 1.04em;
    font-weight: 500;
}
.fixtures-preview-player-img {
    width: 28px;
    height: 28px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #4fc3f7;
    background: #1a2327;
    margin-right: 0.15em;
    box-shadow: 0 1px 4px #0002;
}
.fixtures-preview-tournament {
    color: #ffd54f;
    font-weight: 500;
    font-size: 0.98em;
    margin-bottom: 0.05em;
}
.fixtures-preview-countdown {
    font-size: 0.97em;
    color: #b0bec5;
    margin-top: 0.1em;
}
.fixtures-preview-badge {
    display: inline-block;
    padding: 0.25em 0.8em;
    border-radius: 1em;
    font-size: 0.95em;
    margin-top: 0.2em;
}
</style>
<style>
.match-card .match-player-img {
    width: 120px !important;
    height: 160px !important;
    border-radius: 18px !important;
    border: 4px solid #ffd600;
    box-shadow: 0 6px 24px rgba(0,0,0,0.22);
    margin-bottom: 0.5em;
    object-fit: cover;
    background: #fff;
    display: block;
    margin-left: auto;
    margin-right: auto;
    transition: transform 0.2s;
}
.match-card .match-player-img:hover {
    transform: scale(1.04) rotate(-2deg);
    box-shadow: 0 12px 32px rgba(0,0,0,0.28);
}
.match-card .match-player-name {
    font-size: 1.25em;
    font-weight: bold;
    margin-top: 0.4em;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.1em;
}

.match-card .player-first-name {
    font-size: 1.1em;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}

.match-card .player-last-name {
    font-size: 0.95em;
    font-weight: 600;
    color: #b0bec5;
    line-height: 1.1;
}
</style>

<?php require_once 'includes/footer.php'; ?>
