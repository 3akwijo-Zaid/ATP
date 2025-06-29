<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Player Management</h1>
    <p>Add, edit, and manage tennis players in the system.</p>
</div>

<div class="content-card">
    <h2>Add New Player</h2>
    <form id="add-player-form" style="max-width:420px;margin-bottom:2.5rem;">
        <div class="form-group">
            <label for="player_name">Name</label>
            <input type="text" id="player_name" required>
        </div>
        <div class="form-group">
            <label for="player_image">Image URL</label>
            <input type="text" id="player_image" oninput="updatePlayerPreview()">
        </div>
        <div class="form-group">
            <label for="player_country">Country</label>
            <select id="player_country" required style="width:100%;padding:0.5em;">
                <option value="">Select country</option>
                <option value="USA" data-flag="🇺🇸">🇺🇸 USA</option>
                <option value="GBR" data-flag="🇬🇧">🇬🇧 United Kingdom</option>
                <option value="ESP" data-flag="🇪🇸">🇪🇸 Spain</option>
                <option value="FRA" data-flag="🇫🇷">🇫🇷 France</option>
                <option value="GER" data-flag="🇩🇪">🇩🇪 Germany</option>
                <option value="ITA" data-flag="🇮🇹">🇮🇹 Italy</option>
                <option value="AUS" data-flag="🇦🇺">🇦🇺 Australia</option>
                <option value="RUS" data-flag="🇷🇺">🇷🇺 Russia</option>
                <option value="SRB" data-flag="🇷🇸">🇷🇸 Serbia</option>
                <option value="ARG" data-flag="🇦🇷">🇦🇷 Argentina</option>
                <option value="SUI" data-flag="🇨🇭">🇨🇭 Switzerland</option>
                <option value="CRO" data-flag="🇭🇷">🇭🇷 Croatia</option>
                <option value="CAN" data-flag="🇨🇦">🇨🇦 Canada</option>
                <option value="JPN" data-flag="🇯🇵">🇯🇵 Japan</option>
                <option value="Other" data-flag="🏳️">🏳️ Other</option>
            </select>
        </div>
        <div id="playerPreview" style="display:flex;align-items:center;gap:1em;margin:1em 0 0.5em 0;"></div>
        <button type="submit" class="btn">Add Player</button>
        <p id="add-player-message"></p>
    </form>
</div>

<div class="content-card">
    <h2>All Players</h2>
    <div id="players-list"></div>
</div>

<!-- Edit Player Modal (to be implemented) -->
<div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true"></div>
<script>
function getCountryFlag(code) {
    const option = document.querySelector(`#player_country option[value='${code}']`);
    return option ? option.getAttribute('data-flag') : '🏳️';
}
function updatePlayerPreview() {
    const name = document.getElementById('player_name').value;
    const image = document.getElementById('player_image').value;
    const country = document.getElementById('player_country').value;
    const flag = getCountryFlag(country);
    let html = '';
    if (image) html += `<img src='${image}' alt='preview' style='height:48px;border-radius:50%;border:2px solid #eee;'>`;
    if (name) html += `<span style='font-weight:600;font-size:1.1em;'>${name}</span>`;
    if (country) html += `<span style='font-size:2em;'>${flag}</span>`;
    document.getElementById('playerPreview').innerHTML = html;
}
async function fetchPlayers() {
    const res = await fetch('../api/users.php?action=get_players');
    const data = await res.json();
    const list = document.getElementById('players-list');
    if (data.length > 0) {
        let html = `<div class='players-grid'>`;
        data.forEach(p => {
            const flag = getCountryFlag(p.country);
            html += `
            <div class='player-card'>
                <div class='player-card-img'>${p.image ? `<img src='${p.image}' alt='${p.name}'>` : `<span class='player-placeholder'>${p.name ? p.name[0] : '?'}</span>`}</div>
                <div class='player-card-info'>
                    <div class='player-card-name'>${p.name}</div>
                    <div class='player-card-country'>${flag}</div>
                </div>
                <div class='player-card-actions'>
                    <button class='btn btn-sm btn-primary' onclick='editPlayer(${p.id})'>Edit</button>
                    <button class='btn btn-sm btn-danger' onclick='deletePlayer(${p.id})'>Delete</button>
                </div>
            </div>`;
        });
        html += '</div>';
        list.innerHTML = html;
    } else {
        list.innerHTML = '<p>No players found.</p>';
    }
}
function editPlayer(id) {
    fetch(`../api/users.php?action=get_players`)
        .then(res => res.json())
        .then(players => {
            const p = players.find(x => x.id == id);
            if (!p) return alert('Player not found');
            let modalHtml = `
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h2 class='modal-title'>Edit Player</h2>
                    </div>
                    <div class='modal-body'>
                        <form id='editPlayerForm'>
                            <div class='form-group'>
                                <label>Name</label>
                                <input type='text' class='form-control' id='edit_player_name' name='name' value="${p.name || ''}" required>
                            </div>
                            <div class='form-group'>
                                <label>Image URL</label>
                                <input type='text' class='form-control' id='edit_player_image' name='image' value="${p.image || ''}" oninput='updateEditPlayerPreview()'>
                            </div>
                            <div class='form-group'>
                                <label>Country</label>
                                <select id='edit_player_country' name='country' required style='width:100%;padding:0.5em;'>
                                    <option value=''>Select country</option>
                                    <option value='USA' data-flag='🇺🇸'>🇺🇸 USA</option>
                                    <option value='GBR' data-flag='🇬🇧'>🇬🇧 United Kingdom</option>
                                    <option value='ESP' data-flag='🇪🇸'>🇪🇸 Spain</option>
                                    <option value='FRA' data-flag='🇫🇷'>🇫🇷 France</option>
                                    <option value='GER' data-flag='🇩🇪'>🇩🇪 Germany</option>
                                    <option value='ITA' data-flag='🇮🇹'>🇮🇹 Italy</option>
                                    <option value='AUS' data-flag='🇦🇺'>🇦🇺 Australia</option>
                                    <option value='RUS' data-flag='🇷🇺'>🇷🇺 Russia</option>
                                    <option value='SRB' data-flag='🇷🇸'>🇷🇸 Serbia</option>
                                    <option value='ARG' data-flag='🇦🇷'>🇦🇷 Argentina</option>
                                    <option value='SUI' data-flag='🇨🇭'>🇨🇭 Switzerland</option>
                                    <option value='CRO' data-flag='🇭🇷'>🇭🇷 Croatia</option>
                                    <option value='CAN' data-flag='🇨🇦'>🇨🇦 Canada</option>
                                    <option value='JPN' data-flag='🇯🇵'>🇯🇵 Japan</option>
                                    <option value='Other' data-flag='🏳️'>🏳️ Other</option>
                                </select>
                            </div>
                            <div id='editPlayerPreview' style='display:flex;align-items:center;gap:1em;margin:1em 0 0.5em 0;'></div>
                            <div id='editPlayerError'></div>
                            <button type='submit' class='btn btn-success'>Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>`;
            const modalDiv = document.getElementById('editPlayerModal');
            modalDiv.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(modalDiv);
            modal.show();
            // Set country value
            setTimeout(() => {
                document.getElementById('edit_player_country').value = p.country;
                updateEditPlayerPreview();
            }, 100);
            document.getElementById('edit_player_image').addEventListener('input', updateEditPlayerPreview);
            document.getElementById('edit_player_name').addEventListener('input', updateEditPlayerPreview);
            document.getElementById('edit_player_country').addEventListener('change', updateEditPlayerPreview);
            document.getElementById('editPlayerForm').onsubmit = function(e) {
                e.preventDefault();
                const name = document.getElementById('edit_player_name').value;
                const image = document.getElementById('edit_player_image').value;
                const country = document.getElementById('edit_player_country').value;
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                fetch('../api/users.php?action=edit_player', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, name, image, country })
                })
                .then(res => res.json())
                .then(resp => {
                    submitBtn.disabled = false;
                    if (resp.message && resp.message.includes('success')) {
                        fetchPlayers();
                        modal.hide();
                    } else {
                        showEditPlayerError(resp.message || 'Error updating player');
                    }
                })
                .catch(() => {
                    submitBtn.disabled = false;
                    showEditPlayerError('Network or server error');
                });
            };
        });
}
function updateEditPlayerPreview() {
    const name = document.getElementById('edit_player_name').value;
    const image = document.getElementById('edit_player_image').value;
    const country = document.getElementById('edit_player_country').value;
    const flag = getCountryFlag(country);
    let html = '';
    if (image) html += `<img src='${image}' alt='preview' style='height:48px;border-radius:50%;border:2px solid #eee;'>`;
    if (name) html += `<span style='font-weight:600;font-size:1.1em;'>${name}</span>`;
    if (country) html += `<span style='font-size:2em;'>${flag}</span>`;
    document.getElementById('editPlayerPreview').innerHTML = html;
}
function showEditPlayerError(msg) {
    let err = document.getElementById('editPlayerError');
    if (!err) {
        err = document.createElement('div');
        err.id = 'editPlayerError';
        err.className = 'alert alert-danger mt-2';
        document.querySelector('#editPlayerForm').prepend(err);
    }
    err.textContent = msg;
}
function deletePlayer(id) {
    fetch('../api/users.php?action=delete_player', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.message && resp.message.includes('success')) {
            fetchPlayers();
        } else {
            alert(resp.message || 'Error deleting player');
        }
    })
    .catch(() => {
        alert('Network or server error');
    });
}
document.getElementById('add-player-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const name = document.getElementById('player_name').value;
    const image = document.getElementById('player_image').value;
    const country = document.getElementById('player_country').value;
    const res = await fetch('../api/users.php?action=add_player', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, image, country })
    });
    const result = await res.json();
    document.getElementById('add-player-message').textContent = result.message;
    if (res.ok) {
        fetchPlayers();
        e.target.reset();
        updatePlayerPreview();
    }
});
document.getElementById('player_image').addEventListener('input', updatePlayerPreview);
document.getElementById('player_name').addEventListener('input', updatePlayerPreview);
document.getElementById('player_country').addEventListener('change', updatePlayerPreview);
fetchPlayers();
</script>
<style>
.players-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5em;
    margin-top: 1.5em;
}
.player-card {
    background: var(--bg-primary, #f9fafb);
    border-radius: 12px;
    box-shadow: 0 4px 18px #0001;
    padding: 1.2em 1em 1em 1em;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
    color: var(--text-primary, #23272f);
}
.player-card:hover {
    box-shadow: 0 8px 32px #0002;
    transform: translateY(-2px) scale(1.02);
}
.player-card-img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    overflow: hidden;
    background: #f4f7fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.7em;
    border: 2px solid #e5e7eb;
}
.player-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}
.player-placeholder {
    font-size: 2em;
    color: #b0b8c1;
    font-weight: 700;
}
.player-card-info {
    text-align: center;
    margin-bottom: 0.7em;
}
.player-card-name {
    font-weight: 600;
    font-size: 1.1em;
    margin-bottom: 0.2em;
    color: var(--text-primary, #23272f);
}
.player-card-country {
    font-size: 1.7em;
    margin-bottom: 0.2em;
}
.player-card-actions {
    display: flex;
    gap: 0.5em;
    justify-content: center;
}
.btn-sm { padding:0.4em 1em; font-size:0.98em; border-radius:5px; }
.btn-primary { background:#2196f3; color:#fff; border:none; }
.btn-danger { background:#e53935; color:#fff; border:none; }
.btn-primary:hover { background:#1976d2; }
.btn-danger:hover { background:#b71c1c; }
@media (max-width: 600px) {
    .players-grid {
        grid-template-columns: 1fr;
    }
    .player-card {
        padding: 1em 0.5em;
    }
}
</style>
<?php require_once 'includes/footer.php'; ?> 