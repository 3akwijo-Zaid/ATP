<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>User Management</h1>
    <p>Manage user accounts, view points, and control admin permissions.</p>
</div>

<div class="content-card">
    <div class="container--table">
        <div class="table-container">
            <div id="users-list">
                <!-- Users will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Get the current admin username from PHP session
const currentAdmin = '<?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : ""; ?>';

document.addEventListener('DOMContentLoaded', function() {
    const usersList = document.getElementById('users-list');

    async function fetchUsers() {
        usersList.innerHTML = '<p>Loading users...</p>';
        try {
            // Add cache-busting parameter to ensure fresh data
            const timestamp = new Date().getTime();
            const response = await fetch(`../api/admin.php?action=get_all_users&_t=${timestamp}`);
            const data = await response.json();

            if (!Array.isArray(data) || data.length === 0) {
                usersList.innerHTML = '<p>No users found.</p>';
                return;
            }

            const users = data;
            const table = document.createElement('table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Points</th>
                        <th>Country</th>
                        <th>Join Date</th>
                        <th>Admin Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.points || 0}</td>
                            <td>${user.country || '-'}</td>
                            <td>${user.created_at ? `<span class='match-date' data-utc1='${user.created_at}'></span>` : '-'}</td>
                            <td>${user.is_admin == 1 ? '<span class="badge badge-success">Admin</span>' : '<span class="badge badge-secondary">User</span>'}</td>
                            <td>
                                ${user.is_admin == 0 && currentAdmin === 'admin' ? 
                                    `<button onclick="promoteUser(${user.id}, '${user.username}')" class="btn btn-sm">Promote to Admin</button>` : 
                                    user.is_admin == 0 ? '<span style="color: #666;">Regular User</span>' : '<span style="color: #666;">Already Admin</span>'
                                }
                                ${user.is_admin == 1 && user.username !== 'admin' && currentAdmin === 'admin' ? 
                                    `<button onclick="revokeAdmin(${user.id}, '${user.username}')" class="btn btn-sm btn-danger" style="margin-left: 5px;">Revoke Admin</button>` : 
                                    ''
                                }
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            `;
            usersList.innerHTML = '';
            usersList.appendChild(table);
        } catch (error) {
            console.error('Error fetching users:', error);
            usersList.innerHTML = '<p>Could not load users.</p>';
        }
    }

    window.promoteUser = async function(userId, username) {
        if (confirm(`Are you sure you want to promote ${username} to admin?`)) {
            try {
                // Show loading state
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Promoting...';
                button.disabled = true;
                
                const response = await fetch('../api/admin.php?action=promote_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    // Refresh the user list immediately
                    await fetchUsers();
                } else {
                    alert(`Error: ${result.message || 'Failed to promote user'}`);
                }
            } catch (error) {
                console.error('Error promoting user:', error);
                alert('An error occurred while promoting the user.');
            } finally {
                // Reset button state
                const button = event.target;
                button.textContent = 'Promote to Admin';
                button.disabled = false;
            }
        }
    };

    window.revokeAdmin = async function(userId, username) {
        if (confirm(`Are you sure you want to revoke admin privileges from ${username}? This will remove all admin access.`)) {
            try {
                // Show loading state
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Revoking...';
                button.disabled = true;
                
                const response = await fetch('../api/admin.php?action=revoke_admin', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    // Refresh the user list immediately
                    await fetchUsers();
                } else {
                    alert(`Error: ${result.message || 'Failed to revoke admin privileges'}`);
                }
            } catch (error) {
                console.error('Error revoking admin:', error);
                alert('An error occurred while revoking admin privileges.');
            } finally {
                // Reset button state
                const button = event.target;
                button.textContent = 'Revoke Admin';
                button.disabled = false;
            }
        }
    };

    fetchUsers();
});
</script>

<?php require_once 'includes/footer.php'; ?> 