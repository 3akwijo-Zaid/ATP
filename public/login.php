
<?php require_once 'includes/header.php'; ?>

<main class="login-main">
  <section class="login-section glassmorph">
    <div class="login-header">
      <h2 class="login-title">Log In</h2>
      <p class="login-subtitle">Welcome back! Enter your credentials to continue.</p>
    </div>
    <form id="loginForm" class="login-form" autocomplete="off">
      <div class="login-group">
        <label for="login-username">Username</label>
        <input type="text" id="login-username" name="username" required placeholder="Username">
      </div>
      <div class="login-group">
        <label for="login-password">Password</label>
        <input type="password" id="login-password" name="password" required placeholder="Password">
      </div>
      <button type="submit" id="loginBtn" class="login-btn">Log In</button>
      <div id="login-message" class="login-message" style="display:none;"></div>
      <div class="login-footer">
        <span>Don't have an account? <a href="register.php">Create one</a></span>
      </div>
    </form>
  </section>
</main>

<style>
.login-main {
  min-height: 100dvh;
  min-height: 100vh;
  width: 100vw;
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #101c24 0%, #232b33 60%, #ffd54f22 100%);
  overflow: hidden;
  z-index: 1;
}
.login-main::before {
  content: '';
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background: inherit;
  z-index: -2;
}
.login-main::after {
  content: '';
  position: absolute;
  left: -120px;
  top: -120px;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle at 60% 40%, #ffd54f33 0%, #ffd54f00 80%);
  z-index: -1;
  pointer-events: none;
}
.login-main .login-bg-accent {
  position: absolute;
  right: -100px;
  bottom: -100px;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle at 40% 60%, #4fc3f755 0%, #4fc3f700 80%);
  z-index: -1;
  pointer-events: none;
}
.login-main::before {
  content: '';
  position: absolute;
  left: -120px;
  top: -120px;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle at 60% 40%, #ffd54f33 0%, #ffd54f00 80%);
  z-index: 0;
  pointer-events: none;
}
.login-main::after {
  content: '';
  position: absolute;
  right: -100px;
  bottom: -100px;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle at 40% 60%, #4fc3f755 0%, #4fc3f700 80%);
  z-index: 0;
  pointer-events: none;
}
.login-section.glassmorph {
  background: rgba(34,52,58,0.82);
  border-radius: 22px;
  box-shadow: 0 8px 32px #0006, 0 1.5px 0 #ffd54f44 inset;
  padding: 2.7em 2.2em 2.2em 2.2em;
  max-width: 540px;
  width: 98vw;
  min-width: 0;
  border: 1.5px solid #ffd54f33;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(8px) saturate(1.3);
  -webkit-backdrop-filter: blur(8px) saturate(1.3);
  transition: max-width 0.2s;
}
@media (min-width: 700px) {
  .login-section.glassmorph {
    max-width: 540px;
    width: 98vw;
  }
}
@media (min-width: 1200px) {
  .login-section.glassmorph {
    max-width: 540px;
    width: 98vw;
  }
}
@media (max-width: 700px) {
  .login-section.glassmorph {
    max-width: 98vw;
  }
}
.login-header {
  text-align: center;
  margin-bottom: 1.2em;
}
.login-title {
  font-size: 2.1rem;
  font-weight: 800;
  margin-bottom: 0.2em;
  color: #ffd54f;
  letter-spacing: 0.5px;
  text-shadow: 0 2px 12px #ffd54f33;
}
.login-subtitle {
  color: #b0bec5;
  font-size: 1.08em;
  margin-bottom: 0.2em;
  font-weight: 500;
  letter-spacing: 0.1em;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.3rem;
  z-index: 1;
  position: relative;
}
.login-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.login-group label {
  font-weight: 600;
  color: #ffd54f;
  margin-bottom: 0.1em;
  font-size: 1.08em;
}
.login-group input {
  padding: 0.85rem 1.1rem;
  border: 1.5px solid #ffd54f99;
  border-radius: 0.8rem;
  font-size: 1.08rem;
  background: rgba(26,35,39,0.92);
  color: #fff;
  transition: border 0.2s, background 0.2s, box-shadow 0.18s;
  font-weight: 500;
  box-shadow: 0 2px 8px #0002;
}
.login-group input:focus {
  border-color: #ffd54f;
  outline: none;
  background: #222c31;
}
.login-btn {
  background: linear-gradient(90deg, #ffd54f 60%, #ffb300 100%);
  color: #222;
  font-weight: 700;
  border: none;
  border-radius: 0.8rem;
  padding: 1.05rem 0;
  font-size: 1.13rem;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background 0.2s, color 0.2s, box-shadow 0.18s;
  box-shadow: 0 2px 12px #ffd54f44, 0 0.5px 0 #fffbe7 inset;
  letter-spacing: 0.03em;
}
.login-btn:hover:not(:disabled) {
  background: linear-gradient(90deg, #ffe082 60%, #ffd54f 100%);
  color: #111;
  box-shadow: 0 4px 18px #ffd54f55, 0 0.5px 0 #fffbe7 inset;
}
.login-btn:disabled {
  background: #eee;
  color: #aaa;
  cursor: not-allowed;
  box-shadow: none;
}
.login-footer {
  margin-top: 1.2rem;
  text-align: center;
  font-size: 0.95rem;
  color: #b0bec5;
}
.login-footer a {
  color: #ffd54f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}
.login-message {
  margin-top: 1.2rem;
  padding: 0.9rem 1.1rem;
  border-radius: 0.8rem;
  font-size: 1.07rem;
  text-align: center;
  display: none;
  font-weight: 600;
}
.login-message.success {
  background: #e8f5e9;
  color: #388e3c;
  border: 1.5px solid #388e3c;
}
.login-message.error {
  background: #ffebee;
  color: #c62828;
  border: 1.5px solid #c62828;
}
@media (max-width: 600px) {
  .login-section {
    padding: 1.2em 0.5em 1.2em 0.5em;
    max-width: 98vw;
  }
  .login-title {
    font-size: 1.4rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('loginForm');
  const username = document.getElementById('login-username');
  const password = document.getElementById('login-password');
  const btn = document.getElementById('loginBtn');
  const msg = document.getElementById('login-message');

  function showMessage(text, type) {
    msg.textContent = text;
    msg.className = 'login-message ' + type;
    msg.style.display = 'block';
  }
  function hideMessage() {
    msg.textContent = '';
    msg.className = 'login-message';
    msg.style.display = 'none';
  }
  function setLoading(loading) {
    btn.disabled = loading;
    btn.textContent = loading ? 'Logging In...' : 'Log In';
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    hideMessage();
    if (username.value.trim() === '' || password.value === '') {
      showMessage('Please enter your username and password.', 'error');
      return;
    }
    setLoading(true);
    fetch('../api/users.php?action=login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username: username.value.trim(),
        password: password.value
      })
    })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        showMessage('Login successful! Redirecting...', 'success');
        setTimeout(() => { window.location.href = 'profile.php'; }, 1200);
      } else {
        showMessage(result.message || 'Login failed.', 'error');
      }
    })
    .catch(() => showMessage('Network error. Please try again.', 'error'))
    .finally(() => setLoading(false));
  });
});
</script>
