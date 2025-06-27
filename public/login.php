<?php require_once 'includes/header.php'; ?>

<div class="container">
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

    const response = await fetch('../api/users.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });

    const result = await response.json();
    
    if (response.ok && result.message.includes('success')) {
        messageEl.textContent = 'Login successful! Redirecting...';
        messageEl.style.color = 'lightgreen';
        setTimeout(() => { window.location.href = 'index.php'; }, 1500);
    } else {
        messageEl.textContent = result.message || 'Login failed.';
        messageEl.style.color = 'salmon';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 