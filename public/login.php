<?php require_once 'includes/header.php'; ?>

<div class="container container--full">
    <div class="form-container">
        <h2>Login</h2>
        <form id="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p id="message" style="text-align: center; margin-top: 1rem;"></p>
        </form>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const messageEl = document.getElementById('message');
    messageEl.textContent = '';
    messageEl.style.color = '';
    
    // Disable button to prevent multiple submits
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Logging in...';
    try {
        const response = await fetch('../api/users.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        let result = {};
        try {
            result = await response.json();
        } catch (jsonErr) {
            // Try to get text for more details
            let errorText = '';
            try {
                errorText = await response.text();
            } catch (e) {}
            result = { message: 'Server error. Please try again.' + (errorText ? ' (' + errorText + ')' : '') };
        }
        if (response.ok && result.message && result.message.toLowerCase().includes('success')) {
            messageEl.textContent = 'Login successful! Redirecting...';
            messageEl.style.color = 'lightgreen';
            setTimeout(() => { window.location.href = 'index.php'; }, 1500);
        } else {
            // Show more detailed error if available
            let errorMsg = result.message || 'Login failed. Please check your credentials.';
            if (result.errors && Array.isArray(result.errors)) {
                errorMsg += '\n' + result.errors.join('\n');
            }
            messageEl.textContent = errorMsg;
            messageEl.style.color = 'salmon';
        }
    } catch (err) {
        // Show more details for network/server errors
        let errorMsg = 'Network or server error. Please try again.';
        if (err && err.message) {
            errorMsg += ' (' + err.message + ')';
        }
        messageEl.textContent = errorMsg;
        messageEl.style.color = 'salmon';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Login';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 