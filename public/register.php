<?php require_once 'includes/header.php'; ?>

<div class="container container--full">
    <div class="form-container">
        <h2>Register</h2>
        <form id="register-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Register</button>
            <p id="message" style="text-align: center; margin-top: 1rem;"></p>
        </form>
    </div>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const messageEl = document.getElementById('message');

    const response = await fetch('../api/users.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });

    const result = await response.json();
    if (messageEl) messageEl.textContent = result.message;

    if (response.ok && result.message.includes('success')) {
        if (messageEl) messageEl.style.color = 'lightgreen';
        setTimeout(() => { window.location.href = 'profile.php?welcome=1'; }, 1200);
    } else {
        if (messageEl) messageEl.style.color = 'salmon';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 