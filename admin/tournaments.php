<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>Tournament Management</h1>
    <p>Add, edit, and manage tennis tournaments in the system.</p>
</div>

<div class="content-card">
    <div class="table-container">
        <div id="tournaments-list"></div>
    </div>
</div>

<!-- Add Tournament Modal -->
<div class="modal fade" id="addTournamentModal" tabindex="-1" aria-labelledby="addTournamentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="addTournamentModalLabel">Add Tournament</h2>
      </div>
      <div class="modal-body">
        <form id="addTournamentForm">
          <div class="form-group">
            <label for="tournamentName" class="form-label">Name</label>
            <input type="text" class="form-control" id="tournamentName" name="name" required>
          </div>
          <div class="form-group">
            <label for="tournamentLogo" class="form-label">Logo URL</label>
            <input type="text" class="form-control" id="tournamentLogo" name="logo" oninput="updateTournamentPreview()">
          </div>
          <div id="tournamentPreview" class="form-group" style="display:flex;align-items:center;gap:1em;"></div>
          <button type="submit" class="btn btn-success">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Edit Tournament Modal (to be implemented) -->
<div class="modal fade" id="editTournamentModal" tabindex="-1" aria-labelledby="editTournamentModalLabel" aria-hidden="true"></div>
<!-- Delete Confirmation Modal (to be implemented) -->
<div class="modal fade" id="deleteTournamentModal" tabindex="-1" aria-labelledby="deleteTournamentModalLabel" aria-hidden="true"></div>
<script>
function fetchTournaments() {
    fetch('../api/tournaments.php')
        .then(res => res.json())
        .then(data => {
            let html = '<table class="table"><thead><tr><th>Logo</th><th>Name</th><th>Rounds</th><th>Actions</th></tr></thead><tbody>';
            data.forEach(t => {
                html += `<tr>
                    <td>${t.logo ? `<img src='${t.logo}' alt='logo' style='height:38px;border-radius:6px;border:1.5px solid #eee;background:#fff;'>` : ''}</td>
                    <td>${t.name}</td>
                    <td><button class='btn btn-sm btn-info' onclick='viewRounds(${t.id})'>View Rounds</button></td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editTournament(${t.id})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteTournament(${t.id})'>Delete</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('tournaments-list').innerHTML = html;
        });
}
function viewRounds(tid) {
    fetch(`../api/tournaments.php?rounds=1&tournament_id=${tid}`)
        .then(res => res.json())
        .then(rounds => {
            alert('Rounds: ' + rounds.join(', '));
        });
}
function updateTournamentPreview() {
    const logo = document.getElementById('tournamentLogo').value;
    let html = '';
    if (logo) html += `<img src='${logo}' alt='logo' style='height:38px;border-radius:6px;border:1.5px solid #eee;background:#fff;'>`;
    document.getElementById('tournamentPreview').innerHTML = html;
}
function editTournament(id) {
    fetch(`../api/tournaments.php?id=${id}`)
        .then(res => res.json())
        .then(t => {
            let modalHtml = `
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h2 class='modal-title'>Edit Tournament</h2>
                    </div>
                    <div class='modal-body'>
                        <form id='editTournamentForm'>
                            <div class='mb-3'>
                                <label class='form-label'>Name</label>
                                <input type='text' class='form-control' id='editTournamentName' name='name' value="${t.name || ''}" required>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Logo URL</label>
                                <input type='text' class='form-control' id='editTournamentLogo' name='logo' value="${t.logo || ''}" oninput='updateEditTournamentPreview()'>
                            </div>
                            <div id='editTournamentPreview' class='mb-3' style='display:flex;align-items:center;gap:1em;'></div>
                            <div id='editTournamentError'></div>
                            <button type='submit' class='btn btn-success'>Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>`;
            const modalDiv = document.getElementById('editTournamentModal');
            modalDiv.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(modalDiv);
            modal.show();
            updateEditTournamentPreview();
            document.getElementById('editTournamentForm').onsubmit = function(e) {
                e.preventDefault();
                const form = e.target;
                const data = {
                    name: form.name.value,
                    logo: form.logo.value
                };
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                fetch(`../api/tournaments.php?id=${id}`, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(resp => {
                    submitBtn.disabled = false;
                    if (resp.success) {
                        fetchTournaments();
                        modal.hide();
                    } else {
                        showEditTournamentError(resp.error || 'Error updating tournament');
                    }
                })
                .catch(() => {
                    submitBtn.disabled = false;
                    showEditTournamentError('Network or server error');
                });
            };
        });
}
function updateEditTournamentPreview() {
    const logo = document.getElementById('editTournamentLogo').value;
    let html = '';
    if (logo) html += `<img src='${logo}' alt='logo' style='height:38px;border-radius:6px;border:1.5px solid #eee;background:#fff;'>`;
    document.getElementById('editTournamentPreview').innerHTML = html;
}
function showEditTournamentError(msg) {
    let err = document.getElementById('editTournamentError');
    if (!err) {
        err = document.createElement('div');
        err.id = 'editTournamentError';
        err.className = 'alert alert-danger mt-2';
        document.querySelector('#editTournamentForm').prepend(err);
    }
    err.textContent = msg;
}
function deleteTournament(id) {
    fetch(`../api/tournaments.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            fetchTournaments();
        } else {
            alert(resp.error || 'Error deleting tournament');
        }
    })
    .catch(() => {
        alert('Network or server error');
    });
}
document.getElementById('addTournamentForm').onsubmit = function(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        name: form.name.value,
        logo: form.logo.value
    };
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    fetch('../api/tournaments.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(resp => {
        submitBtn.disabled = false;
        if (resp.success) {
            fetchTournaments();
            var modal = bootstrap.Modal.getInstance(document.getElementById('addTournamentModal'));
            modal.hide();
            form.reset();
            clearTournamentError();
            updateTournamentPreview();
        } else {
            showTournamentError(resp.error || 'Error adding tournament');
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        showTournamentError('Network or server error');
    });
};
function showTournamentError(msg) {
    let err = document.getElementById('tournamentError');
    if (!err) {
        err = document.createElement('div');
        err.id = 'tournamentError';
        err.className = 'alert alert-danger mt-2';
        document.querySelector('#addTournamentForm').prepend(err);
    }
    err.textContent = msg;
}
function clearTournamentError() {
    let err = document.getElementById('tournamentError');
    if (err) err.remove();
}
fetchTournaments();
</script>
<?php require_once 'includes/footer.php'; ?> 