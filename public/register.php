<?php require_once 'includes/header.php'; ?>


<main class="register-main">
  <section class="register-section glassmorph">
      <div class="register-header">
        <h2 class="register-title">Create Your Account</h2>
        <p class="register-subtitle">Join the game and start making predictions!</p>
      </div>
      <form id="registerForm" class="register-form" autocomplete="off">
        <div class="register-group">
          <label for="reg-username">Username</label>
          <input type="text" id="reg-username" name="username" required minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+" placeholder="Username">
        </div>
        <div class="register-group">
          <label for="reg-password">Password</label>
          <input type="password" id="reg-password" name="password" required minlength="6" placeholder="Password">
        </div>
        <div class="register-group">
          <label for="reg-confirm">Confirm Password</label>
          <input type="password" id="reg-confirm" name="confirm" required placeholder="Confirm Password">
        </div>
        <div class="register-group">
          <label for="reg-country">Country</label>
          <select id="reg-country" name="country" required>
            <?php include __DIR__ . '/../admin/includes/country_options.php'; ?>
          </select>
        </div>
        <button type="submit" id="registerBtn" class="register-btn">Register</button>
        <!-- register-footer removed -->
        <div id="register-message" class="register-message" style="display:none;"></div>
      </form>
    </section>
  </main>

<style>

.register-viewport {
  display: none;
}
.register-main {
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
.register-main::before {
  content: '';
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background: inherit;
  z-index: -2;
}
.register-main::after {
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
.register-main .register-bg-accent {
  position: absolute;
  right: -100px;
  bottom: -100px;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle at 40% 60%, #4fc3f755 0%, #4fc3f700 80%);
  z-index: -1;
  pointer-events: none;
}
.register-main::before {
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
.register-main::after {
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
  .register-section.glassmorph {
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
    .register-section.glassmorph {
      max-width: 540px;
      width: 98vw;
    }
  }
  @media (min-width: 1200px) {
    .register-section.glassmorph {
      max-width: 540px;
      width: 98vw;
    }
  }
@media (max-width: 700px) {
  .register-section.glassmorph {
    max-width: 98vw;
  }
}
.register-header {
  text-align: center;
  margin-bottom: 1.2em;
}
.register-title {
  font-size: 2.1rem;
  font-weight: 800;
  margin-bottom: 0.2em;
  color: #ffd54f;
  letter-spacing: 0.5px;
  text-shadow: 0 2px 12px #ffd54f33;
}
.register-subtitle {
  color: #b0bec5;
  font-size: 1.08em;
  margin-bottom: 0.2em;
  font-weight: 500;
  letter-spacing: 0.1em;
}
.register-title {
  text-align: center;
  font-size: 2.1rem;
  font-weight: 700;
  margin-bottom: 1.6rem;
  color: #ffd54f;
  letter-spacing: 0.5px;
}
.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.3rem;
  z-index: 1;
  position: relative;
}
.register-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.register-group label {
  font-weight: 600;
  color: #ffd54f;
  margin-bottom: 0.1em;
  font-size: 1.08em;
}
.register-group input {
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
.register-group input:focus {
  border-color: #ffd54f;
  outline: none;
  background: #222c31;
}
.register-btn {
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
.register-btn:hover:not(:disabled) {
  background: linear-gradient(90deg, #ffe082 60%, #ffd54f 100%);
  color: #111;
  box-shadow: 0 4px 18px #ffd54f55, 0 0.5px 0 #fffbe7 inset;
}
.register-btn:disabled {
  background: #eee;
  color: #aaa;
  cursor: not-allowed;
  box-shadow: none;
}
.register-footer {
  margin-top: 1.2rem;
  text-align: center;
  font-size: 0.95rem;
  color: #b0bec5;
}
.register-footer a {
  color: #ffd54f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}
.register-message {
  margin-top: 1.2rem;
  padding: 0.9rem 1.1rem;
  border-radius: 0.8rem;
  font-size: 1.07rem;
  text-align: center;
  display: none;
  font-weight: 600;
}
.register-message.success {
  background: #e8f5e9;
  color: #388e3c;
  border: 1.5px solid #388e3c;
}
.register-message.error {
  background: #ffebee;
  color: #c62828;
  border: 1.5px solid #c62828;
}
@media (max-width: 600px) {
  .register-section {
    padding: 1.2em 0.5em 1.2em 0.5em;
    max-width: 98vw;
  }
  .register-title {
    font-size: 1.4rem;
  }
  .register-brand-title {
    font-size: 1em;
  }
}

/* Style the country dropdown to fit the form and look modern */
.register-group select {
  padding: 0.85rem 1.1rem;
  border: 1.5px solid #ffd54f99;
  border-radius: 0.8rem;
  font-size: 1.08rem;
  background: rgba(26,35,39,0.92);
  color: #fff;
  transition: border 0.2s, background 0.2s, box-shadow 0.18s;
  font-weight: 500;
  box-shadow: 0 2px 8px #0002;
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  outline: none;
}
.register-group select:focus {
  border-color: #ffd54f;
  background: #222c31;
}
.register-group select option {
  color: #222;
  background: #fffbe7;
}
@media (max-width: 600px) {
  .register-group select {
    font-size: 1em;
    padding: 0.7rem 0.8rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registerForm');
  const username = document.getElementById('reg-username');
  const password = document.getElementById('reg-password');
  const confirm = document.getElementById('reg-confirm');
  const btn = document.getElementById('registerBtn');
  const msg = document.getElementById('register-message');
  const country = document.getElementById('reg-country');

  function showMessage(text, type) {
    msg.textContent = text;
    msg.className = 'register-message ' + type;
    msg.style.display = 'block';
  }
  function hideMessage() {
    msg.textContent = '';
    msg.className = 'register-message';
    msg.style.display = 'none';
  }
  function setLoading(loading) {
    btn.disabled = loading;
    btn.textContent = loading ? 'Creating Account...' : 'Register';
  }

  function validateInputs() {
    if (username.value.trim().length < 3 || username.value.trim().length > 20) {
      showMessage('Username must be 3-20 characters.', 'error');
      return false;
    }
    if (!/^[a-zA-Z0-9_]+$/.test(username.value.trim())) {
      showMessage('Username can only contain letters, numbers, and underscores.', 'error');
      return false;
    }
    if (password.value.length < 6) {
      showMessage('Password must be at least 6 characters.', 'error');
      return false;
    }
    if (password.value !== confirm.value) {
      showMessage('Passwords do not match.', 'error');
      return false;
    }
    return true;
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    hideMessage();
    if (!validateInputs()) return;
    setLoading(true);
    fetch('../api/users.php?action=register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username: username.value.trim(),
        password: password.value,
        country: country.value
      })
    })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        showMessage('Account created! Redirecting...', 'success');
        // Redirect admin users to admin panel, regular users to profile
        const redirectUrl = result.user && result.user.is_admin ? '../admin/dashboard.php' : 'profile.php?welcome=1';
        setTimeout(() => { window.location.href = redirectUrl; }, 1200);
      } else {
        showMessage(result.message || 'Registration failed.', 'error');
      }
    })
    .catch(() => showMessage('Network error. Please try again.', 'error'))
    .finally(() => setLoading(false));
  });
});
</script>
