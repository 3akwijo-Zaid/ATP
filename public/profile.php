<?php require_once 'includes/header.php'; ?>
<?php
require_once __DIR__ . '/../src/classes/User.php';
$avatar = '/ATP/public/assets/img/default-avatar.png';
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
            $avatar = '/ATP/public/assets/img/' . $profile['avatar'];
        }
    }
    $country = $profile['flag'] ?? '';
}
?>
<?php if (isset($_GET['welcome'])): ?>
    <div style="background: #ffd54f; color: #222; font-weight: 600; text-align: center; padding: 1em; border-radius: 10px; margin-bottom: 1.5em; font-size: 1.15em; box-shadow: 0 2px 12px #ffd54f44;">
        Set your account informations
    </div>
<?php endif; ?>
<?php $isWelcome = isset($_GET['welcome']); ?>
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
        <?php if (!$isWelcome): ?>
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
        <?php endif; ?>
        <div class="profile-settings">
            <h3>Edit Profile</h3>
            <form id="profile-edit-form">
                <label>Avatar URL: <input type="text" id="edit-avatar" placeholder="Paste image URL or upload below"></label><br>
                <input type="file" id="upload-avatar" accept="image/*"><br>
                <label>Country: <select id="edit-country"></select></label><br>
                <label>Change Password: <input type="password" id="edit-password" placeholder="New password"></label><br>
                <button type="submit" class="btn">Save Changes</button>
                <span id="profile-edit-message"></span>
            </form>
        </div>
    </div>
</div>
<script>
const countryList = [
    { code: 'ES', flag: '🇪🇸', name: 'Spain' },
    { code: 'FR', flag: '🇫🇷', name: 'France' },
    { code: 'IT', flag: '🇮🇹', name: 'Italy' },
    { code: 'US', flag: '🇺🇸', name: 'USA' },
    { code: 'GB', flag: '🇬🇧', name: 'UK' },
    { code: 'DE', flag: '🇩🇪', name: 'Germany' },
    { code: 'RU', flag: '🇷🇺', name: 'Russia' },
    { code: 'AU', flag: '🇦🇺', name: 'Australia' },
    { code: 'AR', flag: '🇦🇷', name: 'Argentina' },
    { code: 'PL', flag: '🇵🇱', name: 'Poland' },
    { code: 'GR', flag: '🇬🇷', name: 'Greece' },
    { code: 'SE', flag: '🇸🇪', name: 'Sweden' },
    { code: 'BR', flag: '🇧🇷', name: 'Brazil' },
    { code: 'CA', flag: '🇨🇦', name: 'Canada' },
    { code: 'CH', flag: '🇨🇭', name: 'Switzerland' },
    { code: 'NL', flag: '🇳🇱', name: 'Netherlands' },
    { code: 'BE', flag: '🇧🇪', name: 'Belgium' },
    { code: 'HR', flag: '🇭🇷', name: 'Croatia' },
    { code: 'NO', flag: '🇳🇴', name: 'Norway' },
    { code: 'CZ', flag: '🇨🇿', name: 'Czech Republic' },
    { code: 'UA', flag: '🇺🇦', name: 'Ukraine' },
    { code: 'JP', flag: '🇯🇵', name: 'Japan' },
    { code: 'CN', flag: '🇨🇳', name: 'China' },
    { code: 'IN', flag: '🇮🇳', name: 'India' },
    { code: 'EG', flag: '🇪🇬', name: 'Egypt' },
    { code: 'MA', flag: '🇲🇦', name: 'Morocco' },
    { code: 'TN', flag: '🇹🇳', name: 'Tunisia' },
    { code: 'KZ', flag: '🇰🇿', name: 'Kazakhstan' },
    { code: 'SRB', flag: '🇷🇸', name: 'Serbia' },
    { code: 'Other', flag: '🏳️', name: 'Other' }
];
function populateCountryPicker(selected) {
    const sel = document.getElementById('edit-country');
    sel.innerHTML = countryList.map(c => `<option value="${c.code}" ${selected === c.code ? 'selected' : ''}>${c.flag} ${c.name}</option>`).join('');
}
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
        avatarPath = '/ATP/public/assets/img/default-avatar.png';
    } else if (!avatarPath.startsWith('http') && !avatarPath.startsWith('/')) {
        avatarPath = '/ATP/public/assets/img/' + avatarPath;
    }
    const avatarImg = document.getElementById('profile-avatar-img');
    if (avatarImg) avatarImg.src = avatarPath;
    
    const usernameEl = document.getElementById('profile-username');
    if (usernameEl) usernameEl.textContent = p.username;
    const flagEl = document.getElementById('profile-flag');
    if (flagEl) flagEl.textContent = getFlag(p.flag);
    const joinDateEl = document.getElementById('profile-join-date');
    if (joinDateEl) joinDateEl.textContent = 'Joined: ' + p.join_date;
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
                        activityText = `Predicted ${winner} to win ${activity.tournament_name}`;
                        break;
                    case 'game_prediction':
                        activityIcon = '🎯';
                        const gameWinner = activity.predicted_winner === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `Predicted Point ${activity.game_number}: ${gameWinner} (${activity.predicted_score})`;
                        break;
                    case 'statistics_prediction':
                        activityIcon = '📊';
                        const playerName = activity.player_type === 'player1' ? activity.player1_name : activity.player2_name;
                        activityText = `Predicted ${playerName}: ${activity.aces_predicted} aces, ${activity.double_faults_predicted} double faults`;
                        break;
                    default:
                        activityIcon = '📝';
                        activityText = 'Made a prediction';
                }
                
                return `
                    <li class="activity-item">
                        <div class="activity-icon">${activityIcon}</div>
                        <div class="activity-content">
                            <div class="activity-text">${activityText}</div>
                            <div class="activity-meta">
                                <span class="activity-tournament">${activity.tournament_name}</span>
                                <span class="activity-date">${date} at ${time}</span>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
            activityList.innerHTML = activityHtml;
        } else {
            activityList.innerHTML = '<li class="activity-empty">No recent activity. Start making predictions to see your activity here!</li>';
        }
    }
    const editAvatar = document.getElementById('edit-avatar');
    if (editAvatar) editAvatar.value = p.avatar || '';
    populateCountryPicker(p.flag);
}
function getFlag(code) {
    const c = countryList.find(c => c.code === code);
    return c ? c.flag : '🏳️';
}
document.getElementById('profile-edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('upload-avatar');
    const avatarUrl = document.getElementById('edit-avatar').value.trim();
    const country = document.getElementById('edit-country').value;
    const password = document.getElementById('edit-password').value;
    const msg = document.getElementById('profile-edit-message');

    let res, text, data;
    // If a file is selected, use FormData and send as multipart/form-data
    if (fileInput.files && fileInput.files[0]) {
        const formData = new FormData();
        formData.append('avatar', fileInput.files[0]);
        if (country) formData.append('country', country);
        if (password) formData.append('password', password);

        res = await fetch('../api/profile.php', {
            method: 'POST',
            body: formData
        });
        text = await res.text();
        try {
            data = JSON.parse(text);
        } catch (e) {
            msg.textContent = 'Server error: ' + text;
            setTimeout(() => { msg.textContent = ''; }, 5000);
            return;
        }
    } else {
        // Otherwise, send JSON (for avatar URL or other fields)
        const payload = { avatar: avatarUrl, country };
        if (password) payload.password = password;
        res = await fetch('../api/profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        text = await res.text();
        try {
            data = JSON.parse(text);
        } catch (e) {
            msg.textContent = 'Server error: ' + text;
            setTimeout(() => { msg.textContent = ''; }, 5000);
            return;
        }
    }
    if (data.success) {
        msg.textContent = 'Profile updated!';
        // If on welcome page, redirect to home after saving changes
        if (window.location.search.includes('welcome=1')) {
            setTimeout(() => { window.location.href = 'index.php'; }, 1000);
        } else {
            fetchProfile();
        }
    } else {
        msg.textContent = data.error || 'Update failed.';
    }
    setTimeout(() => { msg.textContent = ''; }, 3000);
});
document.getElementById('upload-avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(evt) {
        document.getElementById('edit-avatar').value = evt.target.result;
        document.getElementById('profile-avatar-img').src = evt.target.result;
    };
    reader.readAsDataURL(file);
});
document.addEventListener('DOMContentLoaded', function() {
    populateCountryPicker(); // Populate country select with all options, no selection
    fetchProfile(); // Then fetch and update with user data
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
.profile-stats, .profile-activity, .profile-settings { background: rgba(255,255,255,0.08); border-radius: 12px; box-shadow: 0 1px 6px #0002; padding: 1.2em 1.5em; flex: 1 1 260px; min-width: 260px; }
.profile-stats h3, .profile-activity h3, .profile-settings h3 { color: #ffd54f; margin-top: 0; }
.stats-row { display: flex; gap: 2em; justify-content: space-between; margin-bottom: 1em; }
.stats-row div { text-align: center; font-size: 1.15em; color: #fff; }
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
.profile-settings label { color: #ffd54f; font-weight: 600; display: block; margin-top: 0.7em; }
.profile-settings input, .profile-settings select { width: 100%; padding: 0.6em; border-radius: 6px; border: 1.5px solid #ffd54f; background: #1a2327; color: #fff; margin-top: 0.2em; margin-bottom: 0.7em; font-size: 1.08em; }
.profile-settings .btn { background: #4fc3f7; color: #fff; border: none; border-radius: 6px; padding: 0.7em 2em; font-size: 1.1em; font-weight: 600; margin-top: 0.5em; cursor: pointer; transition: background 0.2s; }
.profile-settings .btn:hover { background: #2196f3; }
#profile-edit-message { color: #ffd54f; margin-left: 1em; font-weight: 600; }
@media (max-width: 900px) { .profile-sections { flex-direction: column; } .profile-card { flex-direction: column; gap: 1em; } }
</style>
<?php require_once 'includes/footer.php'; ?> 