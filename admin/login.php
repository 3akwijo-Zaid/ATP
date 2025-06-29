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
    <style>
        .error-message {
            color: #f44336;
            font-weight: 600;
            margin-top: 1rem;
            text-align: center;
        }
        .success-message {
            color: #4CAF50;
            font-weight: 600;
            margin-top: 1rem;
            text-align: center;
        }
        .loading-message {
            color: #333;
            font-weight: 500;
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Admin Login</h2>
        <form id="admin-login-form" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn" id="login-btn">Login</button>
            <p id="message"></p>
        </form>
    </div>
    <script>
        const form = document.getElementById('admin-login-form');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const messageEl = document.getElementById('message');
        const loginBtn = document.getElementById('login-btn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            messageEl.textContent = '';
            messageEl.className = '';

            const username = usernameInput.value.trim();
            const password = passwordInput.value;

            // Client-side validation
            if (!username || !password) {
                messageEl.textContent = 'Please enter both username and password.';
                messageEl.className = 'error-message';
                return;
            }

            loginBtn.disabled = true;
            messageEl.textContent = 'Logging in...';
            messageEl.className = 'loading-message';

            try {
                const response = await fetch('../api/admin.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                let result;
                try {
                    result = await response.json();
                } catch (jsonErr) {
                    throw new Error('Invalid server response.');
                }
                if (response.ok && result.message && result.message.toLowerCase().includes('success')) {
                    messageEl.textContent = 'Login successful! Redirecting...';
                    messageEl.className = 'success-message';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 700);
                } else {
                    messageEl.textContent = result.message || 'Login failed. Please try again.';
                    messageEl.className = 'error-message';
                }
            } catch (err) {
                messageEl.textContent = 'Network or server error. Please try again later.';
                messageEl.className = 'error-message';
            } finally {
                loginBtn.disabled = false;
            }
        });
    </script>
</body>
</html> 