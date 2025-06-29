<?php require_once 'includes/header.php'; ?>

<div class="page-header">
    <h1>User Management</h1>
    <p>Manage user accounts, view points, and control admin permissions.</p>
</div>

<div class="content-card">
    <div class="table-container">
        <div id="users-list">
            <!-- Users will be loaded here -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usersList = document.getElementById('users-list');

    async function fetchUsers() {
        try {
            const response = await fetch('../api/admin.php?action=get_all_users');
            const users = await response.json();

            if (users.length > 0) {
                const table = document.createElement('table');
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Points</th>
                            <th>Admin Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${users.map(user => `
                            <tr>
                                <td>${user.username}</td>
                                <td>${user.points}</td>
                                <td>${user.is_admin == 1 ? 'Admin' : 'User'}</td>
                                <td>
                                    ${user.is_admin == 0 ? 
                                        `<button onclick="promoteUser(${user.id}, '${user.username}')" class="btn btn-sm">Promote to Admin</button>` : 
                                        '<span style="color: #666;">Already Admin</span>'
                                    }
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                `;
                usersList.appendChild(table);
            } else {
                usersList.innerHTML = '<p>No users found.</p>';
            }
        } catch (error) {
            console.error('Error fetching users:', error);
            usersList.innerHTML = '<p>Could not load users.</p>';
        }
    }

    window.promoteUser = async function(userId, username) {
        if (confirm(`Are you sure you want to promote ${username} to admin?`)) {
            const response = await fetch('../api/admin.php?action=promote_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            const result = await response.json();
            alert(result.message);
            if (response.ok) {
                fetchUsers(); // Refresh the list
            }
        }
    };

    fetchUsers();
});
</script>

<?php require_once 'includes/footer.php'; ?> 