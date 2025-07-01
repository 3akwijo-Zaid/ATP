<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Player Management</h1>
    <p>Add, edit, and manage tennis players in the system.</p>
</div>

<div class="content-card">
    <h2>Add New Player</h2>
    <form id="add-player-form" style="max-width:500px;margin-bottom:2.5rem;">
        <div class="form-group">
            <label for="player_name">Name</label>
            <input type="text" id="player_name" required style="color: #333;">
        </div>
        <div class="form-group">
            <label for="player_image">Player Image</label>
            <div class="image-upload-container">
                <input type="file" id="player_image_file" accept="image/*" style="display: none;">
                <input type="text" id="player_image_url" placeholder="Or paste image URL here" style="color: #333;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('player_image_file').click()">
                    📁 Upload Image
                </button>
            </div>
        </div>
        <div class="form-group">
            <label for="player_country">Country</label>
            <select id="player_country" required style="width:100%;padding:0.5em;color: #333;">
                <?php include __DIR__ . '/includes/country_options.php'; ?>
            </select>
        </div>
        <div id="playerPreview" style="display:flex;align-items:center;gap:1em;margin:1em 0 0.5em 0;padding:1em;background:#f8f9fa;border-radius:8px;"></div>
        <button type="submit" class="btn">Add Player</button>
        <p id="add-player-message"></p>
    </form>
</div>

<div class="content-card">
    <h2>All Players</h2>
    <div id="players-list"></div>
</div>

<script>
let currentEditingId = null;

function getCountryFlag(code) {
    const option = document.querySelector(`#player_country option[value='${code}']`);
    return option ? option.getAttribute('data-flag') : '🏳️';
}

function updatePlayerPreview() {
    const name = document.getElementById('player_name').value;
    const imageUrl = document.getElementById('player_image_url').value;
    const country = document.getElementById('player_country').value;
    const flag = getCountryFlag(country);
    let html = '';
    
    if (imageUrl) {
        html += `<img src='${imageUrl}' alt='preview' style='height:48px;width:48px;border-radius:50%;border:2px solid #ddd;object-fit:cover;'>`;
    } else {
        html += `<div style='height:48px;width:48px;border-radius:50%;border:2px solid #ddd;background:#f0f0f0;display:flex;align-items:center;justify-content:center;font-size:1.5em;color:#999;'>👤</div>`;
    }
    
    if (name) html += `<span style='font-weight:600;font-size:1.1em;color:#333;'>${name}</span>`;
    if (country) html += `<span style='font-size:2em;'>${flag}</span>`;
    
    document.getElementById('playerPreview').innerHTML = html;
}

function updateEditPreview(editingCard) {
    const name = editingCard.querySelector('.edit-name').value;
    const imageUrl = editingCard.querySelector('.edit-image-url').value;
    const country = editingCard.querySelector('.edit-country').value;
    const flag = getCountryFlag(country);
    
    const imgElement = editingCard.querySelector('.player-card-img');
    if (imgElement) {
        if (imageUrl) {
            imgElement.innerHTML = `<img src='${imageUrl}' alt='${name || 'Player'}' style='width:100%;height:100%;object-fit:cover;border-radius:50%;'>`;
        } else {
            imgElement.innerHTML = `<span class='player-placeholder'>${name ? name[0] : '?'}</span>`;
        }
    }
}

async function fetchPlayers() {
    try {
        const res = await fetch('../api/users.php?action=get_players');
        const data = await res.json();
        const list = document.getElementById('players-list');
        
        if (data.length > 0) {
            let html = `<div class='players-grid'>`;
            data.forEach(p => {
                const flag = getCountryFlag(p.country);
                const isEditing = currentEditingId === p.id;
                
                if (isEditing) {
                    // Inline edit form
                    html += `
                    <div class='player-card editing'>
                        <div class='player-card-img'>
                            ${p.image ? `<img src='${p.image}' alt='${p.name}'>` : `<span class='player-placeholder'>${p.name ? p.name[0] : '?'}</span>`}
                        </div>
                        <div class='player-card-edit-form'>
                            <input type='text' class='edit-name' value='${p.name || ''}' placeholder='Player name' style='color: #333;'>
                            <div class='image-upload-container'>
                                <input type='file' class='edit-image-file' accept='image/*' style='display: none;'>
                                <input type='text' class='edit-image-url' value='${p.image || ''}' placeholder='Image URL' style='color: #333;'>
                                <button type='button' class='btn btn-sm btn-secondary' onclick='this.previousElementSibling.previousElementSibling.click()'>📁</button>
                            </div>
                            <select class='edit-country' style='color: #333;'>
                                <?php include __DIR__ . '/includes/country_options.php'; ?>
                            </select>
                            <div class='edit-actions'>
                                <button class='btn btn-sm btn-success' onclick='savePlayerEdit(${p.id})'>💾 Save</button>
                                <button class='btn btn-sm btn-secondary' onclick='cancelPlayerEdit()'>❌ Cancel</button>
                            </div>
                        </div>
                    </div>`;
                } else {
                    // Normal display
                    html += `
                    <div class='player-card'>
                        <div class='player-card-img'>
                            ${p.image ? `<img src='${p.image}' alt='${p.name}'>` : `<span class='player-placeholder'>${p.name ? p.name[0] : '?'}</span>`}
                        </div>
                        <div class='player-card-info'>
                            <div class='player-card-name'>${p.name}</div>
                            <div class='player-card-country'>${flag}</div>
                        </div>
                        <div class='player-card-actions'>
                            <button class='btn btn-sm btn-primary' onclick='editPlayer(${p.id})'>✏️ Edit</button>
                            <button class='btn btn-sm btn-danger' onclick='deletePlayer(${p.id})'>🗑️ Delete</button>
                        </div>
                    </div>`;
                }
            });
            html += '</div>';
            list.innerHTML = html;
            
            // Set country values for editing forms and add event listeners
            if (currentEditingId) {
                const editingCard = document.querySelector('.player-card.editing');
                if (editingCard) {
                    const countrySelect = editingCard.querySelector('.edit-country');
                    const player = data.find(p => p.id == currentEditingId);
                    if (player && countrySelect) {
                        countrySelect.value = player.country;
                    }
                    
                    // Add file upload event listener for edit form
                    const editFileInput = editingCard.querySelector('.edit-image-file');
                    if (editFileInput) {
                        editFileInput.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            
                            // Check file size (limit to 2MB)
                            if (file.size > 2 * 1024 * 1024) {
                                alert('Image file is too large. Please select an image smaller than 2MB.');
                                this.value = '';
                                return;
                            }
                            
                            // Check file type
                            if (!file.type.startsWith('image/')) {
                                alert('Please select a valid image file.');
                                this.value = '';
                                return;
                            }
                            
                            // Convert to data URL
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const dataUrl = e.target.result;
                                // Check if data URL is too long
                                if (dataUrl.length > 500000) { // ~500KB limit
                                    alert('Image is too large when converted. Please use a smaller image or provide a URL instead.');
                                    return;
                                }
                                editingCard.querySelector('.edit-image-url').value = dataUrl;
                                updateEditPreview(editingCard);
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                    
                    // Add event listeners for other edit form fields
                    const editNameInput = editingCard.querySelector('.edit-name');
                    const editImageUrlInput = editingCard.querySelector('.edit-image-url');
                    const editCountrySelect = editingCard.querySelector('.edit-country');
                    
                    if (editNameInput) {
                        editNameInput.addEventListener('input', () => updateEditPreview(editingCard));
                    }
                    if (editImageUrlInput) {
                        editImageUrlInput.addEventListener('input', () => updateEditPreview(editingCard));
                    }
                    if (editCountrySelect) {
                        editCountrySelect.addEventListener('change', () => updateEditPreview(editingCard));
                    }
                    
                    // Initialize edit preview
                    updateEditPreview(editingCard);
                }
            }
        } else {
            list.innerHTML = '<p>No players found.</p>';
        }
    } catch (error) {
        console.error('Error fetching players:', error);
        document.getElementById('players-list').innerHTML = '<p>Error loading players.</p>';
    }
}

function editPlayer(id) {
    currentEditingId = id;
    fetchPlayers();
}

function cancelPlayerEdit() {
    currentEditingId = null;
    fetchPlayers();
}

async function savePlayerEdit(id) {
    const editingCard = document.querySelector('.player-card.editing');
    if (!editingCard) return;
    
    const name = editingCard.querySelector('.edit-name').value.trim();
    const imageUrl = editingCard.querySelector('.edit-image-url').value.trim();
    const country = editingCard.querySelector('.edit-country').value;
    
    if (!name) {
        alert('Player name is required');
        return;
    }
    
    try {
        const response = await fetch('../api/users.php?action=edit_player', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, name, image: imageUrl, country })
        });
        
        const result = await response.json();
        
        if (response.ok && result.message && result.message.includes('success')) {
            currentEditingId = null;
            fetchPlayers();
        } else {
            alert(result.message || 'Error updating player');
        }
    } catch (error) {
        console.error('Error saving player:', error);
        alert('Network or server error');
    }
}

async function deletePlayer(id) {
    if (!confirm('Are you sure you want to delete this player?')) return;
    
    try {
        const response = await fetch('../api/users.php?action=delete_player', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (response.ok && result.message && result.message.includes('success')) {
            fetchPlayers();
        } else {
            alert(result.message || 'Error deleting player');
        }
    } catch (error) {
        console.error('Error deleting player:', error);
        alert('Network or server error');
    }
}

// File upload handling
document.getElementById('player_image_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Check file size (limit to 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image file is too large. Please select an image smaller than 2MB.');
        this.value = '';
        return;
    }
    
    // Check file type
    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        this.value = '';
        return;
    }
    
    // For now, we'll use a simple approach - convert to data URL
    // In production, you'd want to upload to server and get a URL back
    const reader = new FileReader();
    reader.onload = function(e) {
        const dataUrl = e.target.result;
        // Check if data URL is too long
        if (dataUrl.length > 500000) { // ~500KB limit
            alert('Image is too large when converted. Please use a smaller image or provide a URL instead.');
            return;
        }
        document.getElementById('player_image_url').value = dataUrl;
        updatePlayerPreview();
    };
    reader.readAsDataURL(file);
});

// Form submission
document.getElementById('add-player-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const name = document.getElementById('player_name').value.trim();
    const imageUrl = document.getElementById('player_image_url').value.trim();
    const country = document.getElementById('player_country').value;
    
    if (!name) {
        alert('Player name is required');
        return;
    }
    
    const messageEl = document.getElementById('add-player-message');
    messageEl.textContent = 'Adding player...';
    messageEl.style.color = '#666';
    
    try {
        const response = await fetch('../api/users.php?action=add_player', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, image: imageUrl, country })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            messageEl.textContent = result.message;
            messageEl.style.color = '#28a745';
            fetchPlayers();
            e.target.reset();
            updatePlayerPreview();
            // Clear message after 3 seconds
            setTimeout(() => {
                messageEl.textContent = '';
            }, 3000);
        } else {
            messageEl.textContent = result.message || 'Failed to add player';
            messageEl.style.color = '#dc3545';
        }
    } catch (error) {
        console.error('Error adding player:', error);
        messageEl.textContent = 'Network or server error';
        messageEl.style.color = '#dc3545';
    }
});

// Event listeners for preview updates
document.getElementById('player_image_url').addEventListener('input', updatePlayerPreview);
document.getElementById('player_name').addEventListener('input', updatePlayerPreview);
document.getElementById('player_country').addEventListener('change', updatePlayerPreview);

// Initialize
fetchPlayers();
updatePlayerPreview();
</script>

<style>
.players-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5em;
    margin-top: 1.5em;
}

.player-card {
    background: var(--bg-primary, #f9fafb);
    border-radius: 12px;
    box-shadow: 0 4px 18px #0001;
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    color: var(--text-primary, #23272f);
    border: 2px solid transparent;
}

.player-card:hover {
    box-shadow: 0 8px 32px #0002;
    transform: translateY(-2px);
}

.player-card.editing {
    border-color: #2196f3;
    background: #f0f8ff;
}

.player-card-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #f4f7fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1em;
    border: 3px solid #e5e7eb;
    transition: border-color 0.3s ease;
}

.player-card.editing .player-card-img {
    border-color: #2196f3;
}

.player-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}

.player-placeholder {
    font-size: 2.5em;
    color: #b0b8c1;
    font-weight: 700;
}

.player-card-info {
    text-align: center;
    margin-bottom: 1em;
    flex: 1;
}

.player-card-name {
    font-weight: 600;
    font-size: 1.2em;
    margin-bottom: 0.3em;
    color: var(--text-primary, #23272f);
}

.player-card-country {
    font-size: 2em;
    margin-bottom: 0.3em;
}

.player-card-actions {
    display: flex;
    gap: 0.5em;
    justify-content: center;
    width: 100%;
}

.player-card-edit-form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.8em;
}

.player-card-edit-form input,
.player-card-edit-form select {
    padding: 0.6em;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9em;
    background: white;
}

.player-card-edit-form input:focus,
.player-card-edit-form select:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}

.edit-actions {
    display: flex;
    gap: 0.5em;
    justify-content: center;
    margin-top: 0.5em;
}

.image-upload-container {
    display: flex;
    gap: 0.5em;
    align-items: center;
}

.image-upload-container input[type="text"] {
    flex: 1;
    padding: 0.6em;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9em;
    background: white;
}

.image-upload-container input[type="text"]:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}

.btn-sm { 
    padding: 0.5em 1em; 
    font-size: 0.9em; 
    border-radius: 6px; 
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary { 
    background: #2196f3; 
    color: #fff; 
}

.btn-secondary { 
    background: #6c757d; 
    color: #fff; 
}

.btn-success { 
    background: #28a745; 
    color: #fff; 
}

.btn-danger { 
    background: #dc3545; 
    color: #fff; 
}

.btn-primary:hover { background: #1976d2; }
.btn-secondary:hover { background: #5a6268; }
.btn-success:hover { background: #218838; }
.btn-danger:hover { background: #c82333; }

@media (max-width: 768px) {
    .players-grid {
        grid-template-columns: 1fr;
    }
    
    .player-card {
        padding: 1.2em;
    }
    
    .image-upload-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .edit-actions {
        flex-direction: column;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 