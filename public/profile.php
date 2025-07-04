<?php require_once 'includes/header.php'; ?>
<?php
require_once __DIR__ . '/../src/classes/User.php';
$avatar = 'assets/img/default-avatar.png';
if (isset($_SESSION['user_id'])) {
    $user = new User();
    $profile = $user->getProfile($_SESSION['user_id']);
    if (!empty($profile['avatar'])) {
        // If avatar path is absolute (starts with http or https), use as is
        if (preg_match('/^https?:\/\//', $profile['avatar'])) {
            $avatar = $profile['avatar'];
        } 
        // If avatar path starts with /, it's already a full path from root
        elseif (str_starts_with($profile['avatar'], '/')) {
            $avatar = $profile['avatar'];
        } 
        // Otherwise, it's a relative filename, prepend the correct path
        else {
            $avatar = 'assets/img/' . $profile['avatar'];
        }
    }
    $country = $profile['flag'] ?? '';
}
?>
<div class="container container--full profile-container">
    <div class="grid grid--cols-1 gap-lg bg-surface-light rounded-xl shadow-lg p-lg mb-xl">
        <div class="profile-avatar"><img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" id="profile-avatar-img"></div>
        <div class="profile-info">
            <h2 id="profile-username"><?php echo $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;?></h2>
            <div class="profile-meta">
                <span id="profile-join-date">Joined: 2024-01-01</span> |
                <span id="profile-rank">Rank: 1</span>
                <?php if ($country): ?> |
                    <span id="profile-flag"><?php echo htmlspecialchars($country); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="grid grid--responsive gap-lg">
        <div class="profile-stats">
            <h3>Stats</h3>
            <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b id="profile-points">0</b><br>Points</div>
                <div><b id="profile-accuracy">0%</b><br>Accuracy</div>
                <div><b id="profile-streak">0</b><br>Streak</div>
                <div><b id="profile-total">0</b><br>Predictions</div>
            </div>
            <div id="profile-badges" class="profile-badges">
                <span style="color: #b0bec5; font-style: italic;">Loading badges...</span>
            </div>
        </div>
        <div class="profile-activity">
            <h3>Recent Activity</h3>
            <ul id="profile-activity-list" class="list-none p-0 m-0"></ul>
        </div>
    </div>
</div>
<script>

async function fetchProfile() {
    try {
        const res = await fetch('../api/profile.php');
        const text = await res.text(); // Read the response as text first
        
        let data;
        try {
            data = JSON.parse(text); // Then try to parse it as JSON
        } catch (e) {
            console.error('Failed to parse JSON:', text);
            alert('Server error:\n' + text);
            return;
        }
        
        if (!data.success) {
            document.querySelector('.profile-container').innerHTML = `<p style='color:#ffd54f;font-size:1.2em;'>${data.error || 'Could not load profile.'}</p>`;
            return;
        }
        renderProfile(data);
    } catch (error) {
        console.error('Fetch error:', error);
        alert('Network error: ' + error.message);
    }
}
function renderProfile(data) {
    const p = data.profile;
    const s = data.stats;
    
    // Handle avatar path correctly
    let avatarPath = p.avatar;
    if (!avatarPath) {
        avatarPath = 'assets/img/default-avatar.png';
    } else if (!avatarPath.startsWith('http') && !avatarPath.startsWith('/')) {
        avatarPath = 'assets/img/' + avatarPath;
    }
    const avatarImg = document.getElementById('profile-avatar-img');
    if (avatarImg) avatarImg.src = avatarPath;
    
    const usernameEl = document.getElementById('profile-username');
    if (usernameEl) usernameEl.textContent = p.username;
    const flagEl = document.getElementById('profile-flag');
    if (flagEl) flagEl.textContent = getFlag(p.flag);
    const joinDateEl = document.getElementById('profile-join-date');
    if (joinDateEl) joinDateEl.innerHTML = 'Joined: <span class="match-date" data-utc1="' + p.join_date + '"></span>';
    const rankEl = document.getElementById('profile-rank');
    if (rankEl) rankEl.textContent = 'Rank: ' + p.rank;
    const pointsEl = document.getElementById('profile-points');
    if (pointsEl) pointsEl.textContent = s.points;
    const accuracyEl = document.getElementById('profile-accuracy');
    if (accuracyEl) accuracyEl.textContent = s.accuracy + '%';
    const streakEl = document.getElementById('profile-streak');
    if (streakEl) streakEl.textContent = s.streak;
    const totalEl = document.getElementById('profile-total');
    if (totalEl) totalEl.textContent = s.total_predictions;
    
    // Create badge HTML
    const badgeHtml = (data.badges||[]).map(b =>
        `<span class='badge' title='${b.tooltip ? b.tooltip.replace(/'/g, '&apos;') : ''}'>${b.icon ? b.icon + ' ' : ''}${b.label}</span>`
    ).join(' ');
    
    const badgeContainer = document.getElementById('profile-badges');
    if (badgeContainer) badgeContainer.innerHTML = badgeHtml;
    
    // If no badges, show a message
    const dynamicBadges = (data.badges||[]).length;
    if (badgeContainer && dynamicBadges === 0) {
        badgeContainer.innerHTML = '<span style="color: #b0bec5; font-style: italic; font-size: 0.9em;">No badges earned yet. Make predictions to earn badges!</span>';
    }
    
    const activityList = document.getElementById('profile-activity-list');
    if (activityList) {
        if (data.activity && data.activity.length > 0) {
            const activityHtml = data.activity.map(activity => {
                const date = new Date(activity.created_at).toLocaleDateString();
                const time = new Date(activity.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                let activityText = '';
                let activityIcon = '';
                
                switch (activity.type) {
                    case 'match_prediction':
                        activityIcon = '🎾';
                        const winner = activity.prediction_data?.winner === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${winner}</b> to win in <b>${activity.tournament_name}</b>.`;
                        break;
                    case 'game_prediction':
                        activityIcon = '🎯';
                        const gameWinner = activity.predicted_winner === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${gameWinner}</b> would win point <b>${activity.game_number}</b> with a score of <b>${activity.predicted_score}</b>.`;
                        break;
                    case 'statistics_prediction':
                        activityIcon = '📊';
                        const playerName = activity.player_type === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${playerName}</b> would serve <b>${activity.aces_predicted}</b> aces and <b>${activity.double_faults_predicted}</b> double faults.`;
                        break;
                    default:
                        activityIcon = '📝';
                        activityText = 'You made a prediction.';
                }
                
                return `
                    <li class="activity-item">
                        <div class="activity-icon">${activityIcon}</div>
                        <div class="activity-content">
                            <div class="activity-text">${activityText}</div>
                            <div class="activity-meta">
                                <span class="activity-tournament">${activity.tournament_name}</span>
                                <span class="activity-date"><span class="match-date" data-utc1="${activity.created_at}"></span></span>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
            activityList.innerHTML = activityHtml;
        } else {
            activityList.innerHTML = '<li class="activity-empty">You haven\'t made any predictions yet. Once you start predicting match outcomes, your recent activity will appear here. Join the action and make your first prediction today!</li>';
        }
    }

    // Add new stats sections as tabs
    const statsBox = document.querySelector('.profile-stats');
    if (statsBox) {
        // Tab bar HTML
        const tabBar = `
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="points">🏅 Points</button>
            <button class="profile-tab" data-tab="participation">📅 Participation</button>
            <button class="profile-tab" data-tab="leaderboard">🏆 Leaderboard</button>
            <button class="profile-tab" data-tab="headtohead">🤝 Head-to-Head</button>
            <button class="profile-tab" data-tab="timing">⏰ Timing</button>
        </div>
        `;
        // Tab content HTML
        const tabContents = `
        <div class="profile-tab-content" id="tab-points">
            <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b>${s.avg_points ?? 0}</b><br>Avg. Points/Prediction</div>
                <div><b>${s.max_points ?? 0}</b><br>Best Single Prediction</div>
            </div>
        </div>
        <div class="profile-tab-content" id="tab-participation" style="display:none;">
            <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b>${s.days_active ?? 0}</b><br>Days Active</div>
                <div><b>${s.first_prediction ? new Date(s.first_prediction).toLocaleDateString() : '-'}</b><br>First Prediction</div>
                <div><b>${s.most_active_day ? new Date(s.most_active_day).toLocaleDateString() : '-'}</b><br>Most Active Day (${s.most_active_day_count ?? 0})</div>
            </div>
        </div>
        <div class="profile-tab-content" id="tab-leaderboard" style="display:none;">
            <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b>${s.best_rank ?? '-'}</b><br>Best Ever Rank</div>
            </div>
        </div>
        <div class="profile-tab-content" id="tab-headtohead" style="display:none;">
            ${s.top_rival_username ? `<div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b>${s.top_rival_username}</b><br>Top Rival </div>
                <div><b>${s.win_rate_vs_rival !== null ? s.win_rate_vs_rival + '%' : '-'}</b><br>Win Rate vs. Rival</div>
            </div>` : '<div style="text-align:center;color:#b0bec5;">No rival data yet.</div>'}
        </div>
        <div class="profile-tab-content" id="tab-timing" style="display:none;">
            <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                <div><b>${s.avg_time_before_match !== null ? s.avg_time_before_match + ' min' : '-'}</b><br>Avg. Time Before Match</div>
                <div><b>${s.last_minute_predictions ?? 0}</b><br>Last-Minute Predictions (&le;10 min)</div>
            </div>
        </div>
        `;
        statsBox.insertAdjacentHTML('beforebegin', tabBar);
        statsBox.insertAdjacentHTML('afterend', tabContents);
    }

    // --- NEW LAYOUT ---
    // 1. Top row: Recent Activity and Badges Awarded
    const container = document.querySelector('.profile-container');
    if (container) {
        // Remove old badges and activity if present
        const oldBadges = document.getElementById('profile-badges');
        if (oldBadges) oldBadges.parentElement.remove();
        const oldActivity = document.getElementById('profile-activity-list');
        if (oldActivity) oldActivity.parentElement.parentElement.remove();
        // Build Recent Activity card
        let activityHtml = '';
        if (data.activity && data.activity.length > 0) {
            activityHtml = data.activity.map(activity => {
                const date = new Date(activity.created_at).toLocaleDateString();
                const time = new Date(activity.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                let activityText = '';
                let activityIcon = '';
                switch (activity.type) {
                    case 'match_prediction':
                        activityIcon = '🎾';
                        const winner = activity.prediction_data?.winner === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${winner}</b> to win in <b>${activity.tournament_name}</b>.`;
                        break;
                    case 'game_prediction':
                        activityIcon = '🎯';
                        const gameWinner = activity.predicted_winner === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${gameWinner}</b> would win point <b>${activity.game_number}</b> with a score of <b>${activity.predicted_score}</b>.`;
                        break;
                    case 'statistics_prediction':
                        activityIcon = '📊';
                        const playerName = activity.player_type === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `You predicted <b>${playerName}</b> would serve <b>${activity.aces_predicted}</b> aces and <b>${activity.double_faults_predicted}</b> double faults.`;
                        break;
                    default:
                        activityIcon = '📝';
                        activityText = 'You made a prediction.';
                }
                return `
                    <li class="activity-item">
                        <div class="activity-icon">${activityIcon}</div>
                        <div class="activity-content">
                            <div class="activity-text">${activityText}</div>
                            <div class="activity-meta">
                                <span class="activity-tournament">${activity.tournament_name}</span>
                                <span class="activity-date"><span class="match-date" data-utc1="${activity.created_at}"></span></span>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
        } else {
            activityHtml = '<li class="activity-empty">You haven\'t made any predictions yet. Once you start predicting match outcomes, your recent activity will appear here. Join the action and make your first prediction today!</li>';
        }
        const activityCard = `
        <div class="profile-card profile-card-activity">
            <h3 class="profile-card-title">Recent Activity</h3>
            <ul id="profile-activity-list" class="activity-list">${activityHtml}</ul>
        </div>`;
        // Build Badges card
        let badgesHtml = '';
        if (data.badges && data.badges.length > 0) {
            badgesHtml = data.badges.map(b =>
                `<span class='badge' title='${b.tooltip ? b.tooltip.replace(/'/g, '&apos;') : ''}'>${b.icon ? b.icon + ' ' : ''}${b.label}</span>`
            ).join(' ');
        } else {
            badgesHtml = '<span style="color: #b0bec5; font-style: italic; font-size: 0.9em;">No badges earned yet. Make predictions to earn badges!</span>';
        }
        const badgesCard = `
        <div class="profile-card profile-card-badges">
            <h3 class="profile-card-title">Badges Awarded</h3>
            <div class="profile-badges">${badgesHtml}</div>
        </div>`;
        // Insert the two cards as a grid
        let topRow = document.getElementById('profile-top-row');
        if (!topRow) {
            topRow = document.createElement('div');
            topRow.id = 'profile-top-row';
            topRow.className = 'profile-top-row';
            container.insertBefore(topRow, container.children[1]);
        }
        topRow.innerHTML = activityCard + badgesCard;
        // 2. Below: Stats card
        let statsCard = document.getElementById('profile-stats-card');
        if (!statsCard) {
            statsCard = document.createElement('div');
            statsCard.id = 'profile-stats-card';
            statsCard.className = 'profile-card profile-card-stats';
            container.appendChild(statsCard);
        }
        statsCard.innerHTML = `
            <h3 class="profile-card-title">Statistics</h3>
            <div class="profile-stats-section">
                <div class="profile-section-title">Points Breakdown</div>
                <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                    <div><b>${s.avg_points ?? 0}</b><br>Avg. Points/Prediction</div>
                    <div><b>${s.max_points ?? 0}</b><br>Best Single Prediction</div>
                </div>
            </div>
            <div class="profile-stats-section">
                <div class="profile-section-title">Participation</div>
                <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                    <div><b>${s.days_active ?? 0}</b><br>Days Active</div>
                    <div><b>${s.first_prediction ? new Date(s.first_prediction).toLocaleDateString() : '-'}</b><br>First Prediction</div>
                    <div><b>${s.most_active_day ? new Date(s.most_active_day).toLocaleDateString() : '-'}</b><br>Most Active Day (${s.most_active_day_count ?? 0})</div>
                </div>
            </div>
            <div class="profile-stats-section">
                <div class="profile-section-title">Leaderboard</div>
                <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                    <div><b>${s.best_rank ?? '-'}</b><br>Best Ever Rank</div>
                </div>
            </div>
            <div class="profile-stats-section">
                <div class="profile-section-title">Head-to-Head</div>
                ${s.top_rival_username ? `<div class="grid grid--responsive-sm gap-md mb-lg text-center">
                    <div><b>${s.top_rival_username}</b><br>Top Rival (${s.top_rival_overlap} matches)</div>
                    <div><b>${s.win_rate_vs_rival !== null ? s.win_rate_vs_rival + '%' : '-'}</b><br>Win Rate vs. Rival</div>
                </div>` : '<div style="text-align:center;color:#b0bec5;">No rival data yet.</div>'}
            </div>
            <div class="profile-stats-section">
                <div class="profile-section-title">Prediction Timing</div>
                <div class="grid grid--responsive-sm gap-md mb-lg text-center">
                    <div><b>${s.avg_time_before_match !== null ? s.avg_time_before_match + ' min' : '-'}</b><br>Avg. Time Before Match</div>
                    <div><b>${s.last_minute_predictions ?? 0}</b><br>Last-Minute Predictions (&le;10 min)</div>
                </div>
            </div>
        `;
    }
}
function getFlag(code) {
    // Simple flag mapping for display purposes
    const flagMap = {
        'ES': '🇪🇸', 'FR': '🇫🇷', 'IT': '🇮🇹', 'US': '🇺🇸', 'GB': '🇬🇧', 'DE': '🇩🇪',
        'RU': '🇷🇺', 'AU': '🇦🇺', 'AR': '🇦🇷', 'PL': '🇵🇱', 'GR': '🇬🇷', 'SE': '🇸🇪',
        'BR': '🇧🇷', 'CA': '🇨🇦', 'CH': '🇨🇭', 'NL': '🇳🇱', 'BE': '🇧🇪', 'HR': '🇭🇷',
        'NO': '🇳🇴', 'CZ': '🇨🇿', 'UA': '🇺🇦', 'JP': '🇯🇵', 'CN': '🇨🇳', 'IN': '🇮🇳',
        'EG': '🇪🇬', 'MA': '🇲🇦', 'TN': '🇹🇳', 'KZ': '🇰🇿', 'SRB': '🇷🇸'
    };
    return flagMap[code] || '🏳️';
}
document.addEventListener('DOMContentLoaded', function() {
    fetchProfile();
    // Tab switching logic
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const tabName = this.getAttribute('data-tab');
            document.querySelectorAll('.profile-tab-content').forEach(tc => tc.style.display = 'none');
            document.getElementById('tab-' + tabName).style.display = '';
        });
    });
});
</script>
<style>
.profile-container { max-width: 900px; margin: 2.5em auto; background: rgba(34,52,58,0.93); border-radius: 18px; box-shadow: 0 8px 32px #0002; padding: 2.5em 2em; }
.profile-card { display: flex; align-items: center; gap: 2em; margin-bottom: 2em; }
.profile-avatar img { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #ffd54f; background: #1a2327; box-shadow: 0 2px 12px #0003; }
.profile-info h2 { margin: 0 0 0.2em 0; font-size: 2em; color: #ffd54f; }
.profile-flag { font-size: 2em; margin-left: 0.3em; }
.profile-meta { color: #b0bec5; font-size: 1.08em; margin-top: 0.2em; }
.profile-sections { display: flex; flex-wrap: wrap; gap: 2em; }
.profile-stats, .profile-activity { background: rgba(255,255,255,0.08); border-radius: 12px; box-shadow: 0 1px 6px #0002; padding: 1.2em 1.5em; flex: 1 1 260px; min-width: 260px; }
.profile-stats h3, .profile-activity h3 { color: #ffd54f; margin-top: 0; }
.stats-row { display: flex; gap: 2em; justify-content: space-between; margin-bottom: 1em; }
.stats-row div { text-align: center; font-size: 1.15em; color: #fff; }
ul { list-style: none; padding: 0; margin: 0; }
.profile-badges { 
    margin-top: 0.5em; 
    min-height: 2em;
    border: 1px solid rgba(255, 213, 79, 0.3);
    border-radius: 8px;
    padding: 0.5em;
    background: rgba(255, 213, 79, 0.1);
}
.badge {
    display: inline-block;
    background: #ffd54f;
    color: #222;
    border-radius: 1em;
    padding: 0.2em 1em;
    font-weight: 600;
    margin-right: 0.5em;
    margin-bottom: 0.3em;
    font-size: 1.08em;
    box-shadow: 0 1px 8px #ffd54f44;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
    vertical-align: middle;
    border: 2px solid #ffd54f;
}
.badge:hover {
    background: #ffe082;
    color: #111;
    border-color: #ffe082;
}
.activity-list { list-style: none; padding: 0; color: #fff; font-size: 1.08em; }
.activity-list li { margin-bottom: 0.7em; background: rgba(0,0,0,0.08); border-radius: 6px; padding: 0.5em 1em; }
.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8em;
    background: rgba(255, 213, 79, 0.1);
    border: 1px solid rgba(255, 213, 79, 0.2);
    border-radius: 8px;
    padding: 0.8em 1em;
    margin-bottom: 0.8em;
    transition: all 0.2s ease;
}
.activity-item:hover {
    background: rgba(255, 213, 79, 0.15);
    border-color: rgba(255, 213, 79, 0.3);
    transform: translateY(-1px);
}
.activity-icon {
    font-size: 1.2em;
    flex-shrink: 0;
    margin-top: 0.1em;
}
.activity-content {
    flex: 1;
    min-width: 0;
}
.activity-text {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.3em;
    line-height: 1.3;
}
.activity-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9em;
    color: #b0bec5;
}
.activity-tournament {
    color: #ffd54f;
    font-weight: 500;
}
.activity-date {
    color: #90a4ae;
    font-style: italic;
}
.activity-empty {
    text-align: center;
    color: #90a4ae;
    font-style: italic;
    padding: 1em;
    background: rgba(0,0,0,0.1);
    border-radius: 8px;
    border: 1px dashed rgba(255, 213, 79, 0.3);
}
@media (max-width: 425px) {
    .profile-container {
        padding-right: 1.5em;
    }
    .profile-stats, .profile-activity {
        transform: translateX(0.6em);
    }
}


.profile-settings label { color: #ffd54f; font-weight: 600; display: block; margin-top: 0.7em; }
.profile-settings input, .profile-settings select { width: 100%; padding: 0.6em; border-radius: 6px; border: 1.5px solid #ffd54f; background: #1a2327; color: #fff; margin-top: 0.2em; margin-bottom: 0.7em; font-size: 1.08em; }
.profile-settings .btn { background: #4fc3f7; color: #fff; border: none; border-radius: 6px; padding: 0.7em 2em; font-size: 1.1em; font-weight: 600; margin-top: 0.5em; cursor: pointer; transition: background 0.2s; }
.profile-settings .btn:hover { background: #2196f3; }
#profile-edit-message { color: #ffd54f; margin-left: 1em; font-weight: 600; }
@media (max-width: 900px) { .profile-sections { flex-direction: column; } .profile-card { flex-direction: column; gap: 1em; } }
.profile-tabs {
  display: flex;
  gap: 0.5em;
  margin-bottom: 1.2em;
  justify-content: center;
}
.profile-tab {
  background: #232b33;
  color: #ffd54f;
  border: 2px solid #ffd54f;
  border-radius: 8px 8px 0 0;
  padding: 0.7em 1.5em;
  font-size: 1.08em;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.18s, color 0.18s, border 0.18s;
  outline: none;
}
.profile-tab.active, .profile-tab:hover {
  background: #ffd54f;
  color: #232b33;
  border-bottom: 2px solid #232b33;
  z-index: 2;
}
.profile-tab-content {
  background: rgba(34,52,58,0.93);
  border-radius: 0 0 18px 18px;
  box-shadow: 0 4px 16px #0002;
  padding: 2em 1.5em 1.5em 1.5em;
  margin-bottom: 2em;
  min-height: 120px;
}
.profile-section-title {
  font-size: 1.15em;
  font-weight: 700;
  color: #ffd54f;
  margin-bottom: 0.7em;
  text-align: center;
}
.profile-top-row {
  display: flex;
  gap: 2em;
  margin-bottom: 2em;
  flex-wrap: wrap;
}
.profile-card {
  background: rgba(34,52,58,0.97);
  border-radius: 18px;
  box-shadow: 0 4px 24px #0002;
  padding: 2em 1.5em 1.5em 1.5em;
  margin-bottom: 2em;
  flex: 1 1 340px;
  min-width: 280px;
  max-width: 100%;
  display: flex;
  flex-direction: column;
}
.profile-card-title {
  font-size: 1.3em;
  font-weight: 800;
  color: #ffd54f;
  margin-bottom: 1em;
  text-align: center;
  letter-spacing: 0.04em;
}
.profile-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7em;
  justify-content: center;
  margin-top: 0.5em;
  min-height: 2em;
}
.activity-list { list-style: none; padding: 0; color: #fff; font-size: 1.08em; }
.activity-list li { margin-bottom: 0.7em; background: rgba(0,0,0,0.08); border-radius: 6px; padding: 0.5em 1em; }
.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8em;
    background: rgba(255, 213, 79, 0.1);
    border: 1px solid rgba(255, 213, 79, 0.2);
    border-radius: 8px;
    padding: 0.8em 1em;
    margin-bottom: 0.8em;
    transition: all 0.2s ease;
}
.activity-item:hover {
    background: rgba(255, 213, 79, 0.18);
    box-shadow: 0 2px 12px #ffd54f33;
}
.activity-icon {
    font-size: 1.5em;
    margin-right: 0.5em;
    align-self: flex-start;
}
.activity-content { flex: 1; }
.activity-text { font-weight: 600; margin-bottom: 0.2em; }
.activity-meta { color: #b0bec5; font-size: 0.97em; }
.profile-stats-section { margin-bottom: 2em; }
.profile-section-title {
  font-size: 1.15em;
  font-weight: 700;
  color: #ffd54f;
  margin-bottom: 0.7em;
  text-align: center;
}
@media (max-width: 900px) {
  .profile-top-row { flex-direction: column; gap: 1.2em; }
  .profile-card { min-width: 0; }
}
</style> 