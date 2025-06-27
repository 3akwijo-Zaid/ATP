<?php
session_start();
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form id="admin-login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="admin" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" value="tenniss123" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p id="message"></p>
        </form>
    </div>
    <script>
        document.getElementById('admin-login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const messageEl = document.getElementById('message');

            const response = await fetch('../api/admin.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const result = await response.json();
            if (result.message.includes('success')) {
                window.location.href = 'dashboard.php';
            } else {
                messageEl.textContent = result.message;
            }
        });
    </script>
</body>
</html> 