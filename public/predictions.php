<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please <a href='login.php'>login</a> to make predictions.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

if (!isset($_GET['match_id'])) {
    ?>
    <div class="atp-hero-banner">
      <div class="atp-hero-bg"></div>
      <div class="atp-hero-content">
        <h1>Select a Match</h1>
        <p class="atp-hero-desc">Choose a match to start making your predictions</p>
      </div>
    </div>

    <div class="atp-main-container">
      <div class="atp-card atp-no-match-card">
        <div class="atp-no-match-icon">
          <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M40 15C25.6 15 14 26.6 14 41C14 55.4 25.6 67 40 67C54.4 67 66 55.4 66 41C66 26.6 54.4 15 40 15ZM40 61C29.5 61 21 52.5 21 41C21 29.5 29.5 21 40 21C50.5 21 59 29.5 59 41C59 52.5 50.5 61 40 61Z" fill="#ffd54f"/>
            <path d="M40 25C31.7 25 25 31.7 25 40C25 48.3 31.7 55 40 55C48.3 55 55 48.3 55 40C55 31.7 48.3 25 40 25ZM40 49C35.6 49 32 45.4 32 40C32 34.6 35.6 31 40 31C44.4 31 48 34.6 48 40C48 45.4 44.4 49 40 49Z" fill="#ffd54f"/>
            <path d="M40 35C37.2 35 35 37.2 35 40C35 42.8 37.2 45 40 45C42.8 45 45 42.8 45 40C45 37.2 42.8 35 40 35Z" fill="#ffd54f"/>
          </svg>
        </div>
        <h2>No Match Selected</h2>
        <p>You need to select a match to make predictions.</p>
        <div class="atp-btn-row">
          <a href="fixtures.php" class="atp-btn atp-btn-primary">Browse Matches</a>
        </div>
      </div>
    </div>

    <style>
    /* Base Styles */
    body {
      min-height: 100vh;
      font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
      color: #f5f6fa;
      background: linear-gradient(135deg, #16243a 0%, #23394d 100%);
      background-attachment: fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* Hero Banner */
    .atp-hero-banner {
      width: 100%;
      min-height: 180px;
      background: #1a2233;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      box-shadow: 0 4px 24px 0 #0006;
      z-index: 2;
      padding: 2.5rem 1rem 2rem 1rem;
      overflow: hidden;
    }

    .atp-hero-banner::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: radial-gradient(circle at 60% 40%, #ffd54f33 0%, #ffd54f00 80%);
      z-index: 1;
    }

    .atp-hero-bg {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: none;
      z-index: 1;
    }

    .atp-hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      width: 100%;
      max-width: 700px;
      padding: 0 1rem;
    }

    .atp-hero-content h1 {
      font-size: 2.4rem;
      font-weight: 900;
      color: #fff;
      letter-spacing: 0.08em;
      margin: 0 0 1rem 0;
      text-shadow: 0 2px 16px #16243a, 0 0 8px #ffd54f88;
      line-height: 1.2;
    }

    .atp-hero-desc {
      color: #f5f6fa;
      font-size: 1.1rem;
      font-weight: 500;
      margin-bottom: 0;
      text-shadow: 0 1px 8px #ffd54f44;
      opacity: 0.95;
      line-height: 1.4;
    }

    /* Main Container */
    .atp-main-container {
      max-width: 900px;
      margin: 3rem auto 0 auto;
      padding: 0 2.5rem 0 2.5rem;
      display: flex;
      flex-direction: column;
      gap: 2.8rem;
      position: relative;
      z-index: 3;
      min-height: calc(100vh - 220px);
    }

    /* Cards */
    .atp-card {
      background: #232b33;
      border-radius: 18px;
      box-shadow: 0 4px 24px 0 #0006;
      border: none;
      padding: 2.2rem 1.7rem 1.5rem 1.7rem;
      position: relative;
      animation: fadeInUp 0.8s cubic-bezier(.7,-0.2,.3,1.4);
    }

    /* No Match Card */
    .atp-no-match-card {
      text-align: center;
      max-width: 500px;
      margin: 0 auto;
      border: 2px solid #ffd54f;
      background: #263143;
    }

    .atp-no-match-icon {
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .atp-no-match-icon svg {
      filter: drop-shadow(0 4px 16px #ffd54f44);
      animation: pulse 2s infinite;
    }

    .atp-no-match-card h2 {
      font-size: 1.8rem;
      font-weight: 800;
      color: #ffd54f;
      margin: 0 0 1rem 0;
      text-shadow: 0 2px 8px #ffd54f22;
      letter-spacing: 0.04em;
    }

    .atp-no-match-card p {
      font-size: 1.1rem;
      color: #f5f6fa;
      margin-bottom: 2rem;
      line-height: 1.5;
      opacity: 0.9;
    }

    /* Buttons */
    .atp-btn-row {
      display: flex;
      gap: 1.2rem;
      justify-content: center;
      margin-bottom: 0.7rem;
    }

    .atp-btn {
      padding: 12px 32px;
      border-radius: 10px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      border: 2px solid #ffd54f;
      background: #ffd54f;
      color: #16243a;
      box-shadow: 0 4px 16px 0 #ffd54f33;
      text-shadow: 0 1px 4px #0002;
      transition: background 0.18s, border 0.18s, color 0.18s, box-shadow 0.18s, transform 0.18s;
      letter-spacing: 0.5px;
      text-decoration: none;
      display: inline-block;
    }

    .atp-btn-primary:hover {
      background: #fff;
      border-color: #ffd54f;
      color: #ffd54f;
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 8px 32px 0 #ffd54f44;
    }

    /* Animations */
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }

    /* Responsive Design */
    @media (max-width: 700px) {
      .atp-main-container {
        padding: 0 1rem;
      }
      
      .atp-card {
        padding: 1.5rem 1rem;
      }
      
      .atp-hero-content h1 {
        font-size: 2rem;
      }
      
      .atp-hero-desc {
        font-size: 1rem;
      }
      
      .atp-no-match-card h2 {
        font-size: 1.5rem;
      }
      
      .atp-no-match-card p {
        font-size: 1rem;
      }
      
      .atp-no-match-icon {
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      
      .atp-no-match-icon svg {
        filter: drop-shadow(0 4px 16px #ffd54f44);
        animation: pulse 2s infinite;
      }
    }
    </style>
    <?php
    require_once 'includes/footer.php';
    exit();
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
?>

<div class="atp-hero-banner">
  <div class="atp-hero-bg"></div>
  <div class="atp-hero-content">
    <h1>ATP Predictions</h1>
    <p class="atp-hero-desc">Predict the winner, set scores, and every game. Compete for the top spot!</p>
  </div>
</div>

<div class="atp-main-container">
  <div class="atp-card atp-match-card">
    <div class="atp-match-header" id="match-details"></div>
    <div class="atp-section-title">Match Winner</div>
    <div class="atp-player-row">
      <label class="atp-player-card">
        <input type="radio" name="winner" value="player1" id="winner-player1">
        <div class="atp-player-avatar"><img id="player1-avatar" src="assets/img/default-avatar.png" alt="Player 1"></div>
        <div class="atp-player-name" id="player1-name">Player 1</div>
      </label>
      <label class="atp-player-card">
        <input type="radio" name="winner" value="player2" id="winner-player2">
        <div class="atp-player-avatar"><img id="player2-avatar" src="assets/img/default-avatar.png" alt="Player 2"></div>
        <div class="atp-player-name" id="player2-name">Player 2</div>
      </label>
    </div>
    <div class="atp-section-title">Set Scores</div>
    <div class="atp-sets-grid" id="sets-grid"></div>
    <div class="atp-btn-row">
      <button type="button" class="atp-btn atp-btn-primary" id="save-match-prediction">Save Match Prediction</button>
      <button type="button" class="atp-btn atp-btn-secondary" id="clear-match-prediction">Clear</button>
    </div>
  </div>

  <div class="atp-card atp-game-card">
    <div class="atp-section-title">Game Predictions (Set 1)</div>
    <div class="atp-games-grid" id="games-grid"></div>
    <div class="atp-btn-row">
      <button type="button" class="atp-btn atp-btn-primary" id="save-game-predictions">Save Game Predictions</button>
      <button type="button" class="atp-btn atp-btn-secondary" id="clear-game-predictions">Clear All</button>
    </div>
  </div>

  <div class="atp-card atp-statistics-card">
    <div class="atp-section-title">Player Statistics Predictions</div>
    <div class="atp-statistics-grid" id="statistics-grid"></div>
    <div class="atp-btn-row">
      <button type="button" class="atp-btn atp-btn-primary" id="save-statistics-predictions">Save Statistics Predictions</button>
      <button type="button" class="atp-btn atp-btn-secondary" id="clear-statistics-predictions">Clear All</button>
    </div>
  </div>

  <div class="atp-card atp-summary-card">
    <div class="atp-section-title">Your Predictions</div>
    <div id="prediction-summary" class="atp-prediction-summary"></div>
  </div>

  <div id="lock-message" class="atp-lock-message" style="display:none;">
    <div class="atp-lock-icon">🔒</div>
    <p>Predictions are locked for this match</p>
  </div>
</div>

<style>
/* Base Styles */
body {
  min-height: 100vh;
  font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
  color: #f5f6fa;
  background: linear-gradient(135deg, #16243a 0%, #23394d 100%);
  background-attachment: fixed;
  background-size: cover;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

/* Hero Banner */
.atp-hero-banner {
  width: 100%;
  min-height: 180px;
  background: #1a2233;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  box-shadow: 0 4px 24px 0 #0006;
  z-index: 2;
  padding: 2.5rem 1rem 2rem 1rem;
  overflow: hidden;
}

.atp-hero-banner::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(circle at 60% 40%, #ffd54f33 0%, #ffd54f00 80%);
  z-index: 1;
}

.atp-hero-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: none;
  z-index: 1;
}

.atp-hero-content {
  position: relative;
  z-index: 3;
  text-align: center;
  width: 100%;
  max-width: 700px;
  padding: 0 1rem;
}

.atp-hero-content h1 {
  font-size: 2.4rem;
  font-weight: 900;
  color: #fff;
  letter-spacing: 0.08em;
  margin: 0 0 1rem 0;
  text-shadow: 0 2px 16px #16243a, 0 0 8px #ffd54f88;
  line-height: 1.2;
}

.atp-hero-desc {
  color: #f5f6fa;
  font-size: 1.1rem;
  font-weight: 500;
  margin-bottom: 0;
  text-shadow: 0 1px 8px #ffd54f44;
  opacity: 0.95;
  line-height: 1.4;
}

/* Main Container */
.atp-main-container {
  max-width: 900px;
  margin: 3rem auto 0 auto;
  padding: 0 2.5rem 0 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 2.8rem;
  position: relative;
  z-index: 3;
  min-height: calc(100vh - 220px);
}

/* Cards */
.atp-card {
  background: #232b33;
  border-radius: 18px;
  box-shadow: 0 4px 24px 0 #0006;
  border: none;
  padding: 2.2rem 1.7rem 1.5rem 1.7rem;
  position: relative;
  animation: fadeInUp 0.8s cubic-bezier(.7,-0.2,.3,1.4);
}

.atp-section-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: #ffd54f;
  margin: 1.2rem 0 0.7rem 0;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  text-shadow: 0 1px 8px #ffd54f22;
}

/* Match Header */
.atp-match-header {
  background: #263143;
  color: #ffd54f;
  padding: 1.2rem 1rem 1rem 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 12px #0004;
  border: none;
}

.atp-match-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  font-weight: 600;
  text-shadow: 0 2px 8px #ffd54f44;
  color: #ffd54f;
}

.atp-match-header p {
  font-size: 1rem;
  opacity: 0.85;
  margin-bottom: 0.2rem;
  color: #f5f6fa;
}

/* Player Cards */
.atp-player-row {
  display: flex;
  gap: 2.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.atp-player-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #1a2233;
  border-radius: 14px;
  box-shadow: 0 4px 24px 0 #0003;
  border: 2px solid #ffd54f;
  padding: 1.2rem 1.2rem 1rem 1.2rem;
  min-width: 140px;
  max-width: 180px;
  cursor: pointer;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  position: relative;
}

.atp-player-card input[type="radio"] {
  display: none;
}

.atp-player-card:has(input[type="radio"]:checked) {
  border: 2.5px solid #ffd54f;
  box-shadow: 0 8px 32px 0 #ffd54f33, 0 2.5px 12px 0 #1976d222;
  transform: scale(1.06) rotate(-2deg);
  background: #23394d;
}

.atp-player-avatar img {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  border: 3px solid #ffd54f;
  box-shadow: 0 2px 12px #ffd54f44;
  background: #1a2233;
  object-fit: cover;
  margin-bottom: 0.7rem;
}

.atp-player-name {
  font-weight: 700;
  font-size: 1.15rem;
  text-shadow: 0 1px 8px #ffd54f22;
  letter-spacing: 0.5px;
  margin-top: 0.2rem;
  margin-bottom: 0.1rem;
  line-height: 1.1;
  text-transform: uppercase;
  color: #fff;
}

/* Sets Grid */
.atp-sets-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.2rem;
  margin-bottom: 1.2rem;
}

.set-card {
  background: #263143;
  border-radius: 12px;
  padding: 1.1rem 0.7rem 0.7rem 0.7rem;
  color: #f5f6fa;
  text-align: center;
  box-shadow: 0 2px 8px #0002;
  border: 2px solid #23394d;
  position: relative;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  animation: popIn 0.9s cubic-bezier(.7,-0.2,.3,1.4);
}

.set-card h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #ffd54f;
}

.set-scores {
  display: flex;
  gap: 0.4rem;
  justify-content: center;
  align-items: center;
}

.set-scores input {
  width: 36px;
  padding: 8px 4px;
  border: 2px solid #23394d;
  border-radius: 6px;
  text-align: center;
  font-weight: 600;
  font-size: 0.95rem;
  background: #1a2233;
  color: #ffd54f;
  transition: border 0.2s, box-shadow 0.2s, transform 0.1s;
}

.set-scores input:focus {
  outline: none;
  border: 2px solid #ffd54f;
  box-shadow: 0 0 0 3px #ffd54f33;
  transform: scale(1.05);
}

.set-scores span {
  font-weight: 600;
  font-size: 1rem;
  color: #ffd54f;
}

.tiebreak-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
  background: #263143;
  border-radius: 10px;
  padding: 1em 1.5em;
  margin: 0.8rem auto 0.3rem auto;
  width: 100%;
  max-width: 280px;
  box-sizing: border-box;
  border: 2px solid #ffd54f;
  box-shadow: 0 4px 16px 0 #0002;
  transition: box-shadow 0.2s, border 0.2s;
}

.tiebreak-row label {
  color: #ffd54f;
  font-size: 1em;
  font-weight: 600;
  margin-right: 0.8em;
  letter-spacing: 0.1px;
  text-shadow: 0 1px 4px #ffd54f33;
  white-space: nowrap;
}

.tiebreak-row input[type="number"] {
  width: 55px !important;
  min-width: 55px;
  padding: 10px 6px;
  border: 2px solid #23394d;
  border-radius: 6px;
  text-align: center;
  font-weight: 600;
  font-size: 1.1rem;
  background: #1a2233;
  color: #ffd54f;
  transition: border 0.2s, box-shadow 0.2s, transform 0.1s;
  box-shadow: 0 2px 8px 0 #0002;
}

.tiebreak-row input[type="number"]:focus {
  outline: none;
  background: #232b33;
  border: 2px solid #ffd54f;
  box-shadow: 0 0 0 3px #ffd54f33;
  transform: scale(1.05);
}

.tiebreak-row input[type="number"]:hover {
  border-color: #ffd54f;
  box-shadow: 0 3px 12px 0 #ffd54f33;
}

.tiebreak-row span {
  color: #ffd54f;
  font-weight: 600;
  font-size: 1.2em;
  margin: 0 0.5em;
  text-shadow: 0 1px 4px #ffd54f33;
}

/* Games Grid */
.atp-games-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.2rem;
  margin-bottom: 1.2rem;
}

.game-card {
  background: #263143;
  border-radius: 12px;
  padding: 1.2rem 0.9rem;
  color: #f5f6fa;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px #0002;
  border: 2px solid #23394d;
  width: 100%;
  min-width: 0;
  box-sizing: border-box;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  animation: popIn 1s cubic-bezier(.7,-0.2,.3,1.4);
}

.game-card h3 {
  margin: 0 0 0.7rem 0;
  font-size: 1.1rem;
  text-align: center;
  color: #ffd54f;
  font-weight: 700;
  text-shadow: 0 1px 8px #ffd54f22;
}

.game-inputs {
  display: grid;
  gap: 0.7rem;
}

.input-group {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.input-group label {
  font-weight: 600;
  min-width: 60px;
  color: #ffd54f;
}

.input-group select,
.input-group input {
  flex: 1 1 0%;
  min-width: 0;
  max-width: 100%;
  width: 100%;
  padding: 8px 12px;
  border: 2px solid #23394d;
  border-radius: 6px;
  background: #1a2233;
  font-size: 0.98rem;
  transition: border 0.2s, box-shadow 0.2s, transform 0.1s;
  box-sizing: border-box;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #ffd54f;
}

.input-group select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url('data:image/svg+xml;utf8,<svg fill="%23ffd54f" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
  background-repeat: no-repeat;
  background-position: right 0.7em center;
  background-size: 1.2em;
  padding-right: 2.2em;
}

.input-group select:focus,
.input-group input:focus {
  outline: none;
  background: #232b33;
  border: 2px solid #ffd54f;
  box-shadow: 0 0 0 3px #ffd54f33;
  transform: scale(1.02);
}

.score-input {
  display: flex;
  gap: 0.4rem;
  align-items: center;
  width: 100%;
}

.score-input input {
  width: 60px;
  min-width: 60px;
  text-align: center;
  font-weight: 600;
  background: #1a2233;
  color: #ffd54f;
  border: 2px solid #23394d;
  border-radius: 6px;
  padding: 8px 4px;
  font-size: 1rem;
}

.score-input span {
  font-weight: 600;
  font-size: 1rem;
  color: #ffd54f;
}

/* Statistics Grid */
.atp-statistics-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 1.2rem;
}

.statistics-card {
  background: #263143;
  border-radius: 12px;
  padding: 1.5rem 1.2rem;
  color: #f5f6fa;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px #0002;
  border: 2px solid #23394d;
  width: 100%;
  min-width: 0;
  box-sizing: border-box;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  animation: popIn 1.1s cubic-bezier(.7,-0.2,.3,1.4);
}

.statistics-card h3 {
  margin: 0 0 1rem 0;
  font-size: 1.2rem;
  text-align: center;
  color: #ffd54f;
  font-weight: 700;
  text-shadow: 0 1px 8px #ffd54f22;
}

.statistics-inputs {
  display: grid;
  gap: 1rem;
}

.statistics-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.statistics-input-group label {
  font-weight: 600;
  color: #ffd54f;
  font-size: 0.95rem;
  text-shadow: 0 1px 4px #ffd54f22;
}

.statistics-input-group input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #23394d;
  border-radius: 8px;
  background: #1a2233;
  font-size: 1rem;
  font-weight: 600;
  transition: border 0.2s, box-shadow 0.2s, transform 0.1s;
  box-sizing: border-box;
  color: #ffd54f;
  text-align: center;
}

.statistics-input-group input:focus {
  outline: none;
  background: #232b33;
  border: 2px solid #ffd54f;
  box-shadow: 0 0 0 3px #ffd54f33;
  transform: scale(1.02);
}

.statistics-input-group input:hover {
  border-color: #ffd54f;
  box-shadow: 0 3px 12px 0 #ffd54f33;
}

/* Buttons */
.atp-btn-row {
  display: flex;
  gap: 1.2rem;
  justify-content: center;
  margin-bottom: 0.7rem;
}

.atp-btn {
  padding: 12px 32px;
  border-radius: 10px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  border: 2px solid #ffd54f;
  background: #ffd54f;
  color: #16243a;
  box-shadow: 0 4px 16px 0 #ffd54f33;
  text-shadow: 0 1px 4px #0002;
  transition: background 0.18s, border 0.18s, color 0.18s, box-shadow 0.18s, transform 0.18s;
  letter-spacing: 0.5px;
}

.atp-btn-primary:hover {
  background: #fff;
  border-color: #ffd54f;
  color: #ffd54f;
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 8px 32px 0 #ffd54f44;
}

.atp-btn-secondary {
  background: #232b33;
  color: #ffd54f;
  border: 2px solid #ffd54f;
  font-weight: 700;
}

.atp-btn-secondary:hover {
  background: #263143;
  color: #ffd54f;
  border-color: #ffd54f;
  transform: translateY(-1px) scale(1.03);
  box-shadow: 0 6px 24px 0 #ffd54f33;
}

.atp-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  background: #e0e0e0;
  color: #757575;
  border-color: #bdbdbd;
}

/* Prediction Summary */
.atp-prediction-summary {
  background: #263143;
  border-radius: 12px;
  padding: 1.7rem 1.2rem 1.2rem 1.2rem;
  color: #f5f6fa;
  margin-top: 1.2rem;
  box-shadow: 0 4px 16px 0 #0002;
  border: 2px solid #ffd54f;
  position: relative;
  overflow: visible;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  animation: fadeInUp 1.2s cubic-bezier(.7,-0.2,.3,1.4);
}

.atp-prediction-summary h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  text-align: left;
  font-size: 1.25rem;
  font-weight: 800;
  text-shadow: 0 1px 8px #ffd54f22;
  color: #ffd54f;
}

.game-predictions-sequence {
  margin-top: 0.7rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
  gap: 0.4rem;
  max-width: 100%;
}

.game-prediction-item {
  background: #232b33;
  border: 2px solid #ffd54f;
  border-radius: 8px;
  padding: 0.7rem 0.3rem;
  text-align: center;
  font-size: 1.01rem;
  box-shadow: 0 2px 8px 0 #ffd54f22;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
  animation: popIn 1.1s cubic-bezier(.7,-0.2,.3,1.4);
}

.game-prediction-item:hover {
  box-shadow: 0 6px 24px 0 #ffd54f33;
  border: 2px solid #ffd54f;
  transform: translateY(-2px) scale(1.05);
  z-index: 2;
}

.game-prediction-item .game-number {
  font-weight: 700;
  margin-bottom: 0.2rem;
  font-size: 1.05rem;
  text-shadow: 0 1px 4px #ffd54f22;
  color: #ffd54f;
}

.game-prediction-item .game-score {
  font-size: 1.01rem;
  margin-bottom: 0.1rem;
  font-weight: 700;
  color: #ffd54f;
}

.game-prediction-item .game-winner {
  font-size: 0.92rem;
  opacity: 0.9;
  font-style: normal;
  color: #f5f6fa;
}

/* Lock Message */
.atp-lock-message {
  background: #232b33;
  color: #ffd54f;
  padding: 1.5rem 1.2rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 4px 16px 0 #0002;
  margin-top: 1.5rem;
  border: 2px solid #ffd54f;
  font-size: 1.15rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.atp-lock-icon {
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
  color: #ffd54f;
  text-shadow: 0 2px 8px #ffd54f22;
}

/* Messages */
.message {
  padding: 1rem;
  border-radius: 10px;
  margin: 1.1rem 0;
  text-align: center;
  font-weight: 700;
  font-size: 1.08rem;
  box-shadow: 0 4px 16px 0 #ffd54f22;
  border: 2px solid #ffd54f;
  background: #263143;
  color: #ffd54f;
  letter-spacing: 0.04em;
  transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
}

.message.success {
  background: #e8f5e9;
  color: #388e3c;
  border: 2px solid #b2dfdb;
}

.message.error {
  background: #ffebee;
  color: #c62828;
  border: 2px solid #ffcdd2;
}

.message.warning {
  background: #fff8e1;
  color: #ffb300;
  border: 2px solid #ffe082;
}

/* Animations */
@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes popIn {
  0% { opacity: 0; transform: scale(0.8); }
  100% { opacity: 1; transform: scale(1); }
}

/* Responsive Design */
@media (max-width: 900px) {
  .atp-main-container {
    max-width: 99vw;
    padding: 0 0.2rem;
  }
  
  .atp-hero-content h1 {
    font-size: 2.2rem;
  }
  
  .atp-hero-desc {
    font-size: 1.1rem;
  }
}

@media (max-width: 700px) {
  .atp-main-container {
    padding: 0 0.1rem;
  }
  
  .atp-card {
    padding: 1.2rem 0.5rem 1rem 0.5rem;
  }
  
  .atp-player-row {
    flex-direction: column;
    gap: 1.5rem;
    align-items: center;
  }
  
  .atp-player-card {
    min-width: 200px;
    max-width: 280px;
    width: 100%;
    padding: 1.5rem 1rem 1.2rem 1rem;
    margin: 0 auto;
  }
  
  .atp-player-avatar img {
    width: 80px;
    height: 80px;
    margin-bottom: 1rem;
  }
  
  .atp-player-name {
    font-size: 1.1rem;
    line-height: 1.2;
    text-align: center;
    word-wrap: break-word;
    hyphens: auto;
  }
  
  .atp-sets-grid, .atp-games-grid {
    grid-template-columns: 1fr;
    gap: 0.7rem;
  }
  
  .atp-statistics-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .atp-btn-row {
    flex-direction: column;
    gap: 0.7rem;
  }
  
  .atp-btn {
    width: 100%;
  }
  
  .atp-hero-banner {
    min-height: 160px;
    padding: 2rem 1rem;
  }
  
  .atp-hero-content h1 {
    font-size: 1.8rem;
    letter-spacing: 0.05em;
  }
  
  .atp-hero-desc {
    font-size: 1rem;
  }
  
  .tiebreak-row {
    max-width: 240px;
    padding: 0.8em 1.2em;
    gap: 0.8em;
  }
  
  .tiebreak-row label {
    font-size: 0.95em;
    margin-right: 0.6em;
  }
  
  .tiebreak-row input[type="number"] {
    width: 50px !important;
    min-width: 50px;
    padding: 8px 4px;
    font-size: 1rem;
  }
  
  .tiebreak-row span {
    font-size: 1.1em;
    margin: 0 0.4em;
  }
  
  /* Enhanced mobile styles for better touch interaction */
  .atp-player-card:active {
    transform: scale(0.98);
  }
  
  .atp-player-card:has(input[type="radio"]:checked) {
    transform: scale(1.02);
    rotate: 0deg;
  }
  
  /* Improve section titles on mobile */
  .atp-section-title {
    font-size: 1.2rem;
    text-align: center;
    margin: 1rem 0 0.8rem 0;
  }
  
  /* Better spacing for mobile */
  .atp-match-header {
    padding: 1rem 0.8rem;
    margin-bottom: 1.2rem;
  }
  
  .atp-match-header h1 {
    font-size: 1.3rem;
    line-height: 1.3;
  }
  
  .atp-match-header p {
    font-size: 0.95rem;
    margin-bottom: 0.3rem;
  }
}

/* Footer adjustments */
footer {
  margin-top: 0;
  padding-bottom: 0.5rem;
}
</style>

<script>
const user_id = <?php echo $user_id; ?>;

document.addEventListener('DOMContentLoaded', async function() {
    const matchId = new URLSearchParams(window.location.search).get('match_id');
    const matchDetails = document.getElementById('match-details');
    const gamesGrid = document.getElementById('games-grid');
    const setsGrid = document.getElementById('sets-grid');
    const saveMatchBtn = document.getElementById('save-match-prediction');
    const clearMatchBtn = document.getElementById('clear-match-prediction');
    const saveGameBtn = document.getElementById('save-game-predictions');
    const clearGameBtn = document.getElementById('clear-game-predictions');
    const saveStatisticsBtn = document.getElementById('save-statistics-predictions');
    const clearStatisticsBtn = document.getElementById('clear-statistics-predictions');
    const lockMessage = document.getElementById('lock-message');
    const predictionSummary = document.getElementById('prediction-summary');

    let match = null;

    // Fetch match details
    try {
        const response = await fetch(`../api/matches.php?id=${matchId}`);
        match = await response.json();

        if (match) {
            matchDetails.innerHTML = `
                <h1>${match.player1_name} vs ${match.player2_name}</h1>
                <p>${match.tournament_name} - ${match.round}</p>
                <p>${new Date(match.start_time).toLocaleString()}</p>
            `;
            
            // Update player names and avatars
            document.getElementById('player1-name').textContent = match.player1_name;
            document.getElementById('player2-name').textContent = match.player2_name;
            
            if (match.player1_image) {
                document.getElementById('player1-avatar').src = match.player1_image;
            }
            if (match.player2_image) {
                document.getElementById('player2-avatar').src = match.player2_image;
            }
            
            // Check if predictions are locked
            const startTime = new Date(match.start_time);
            const now = new Date();
            const timeDiff = (startTime - now) / 1000; // seconds
            
            if (timeDiff <= 3600) {
                lockMessage.style.display = 'block';
                document.querySelectorAll('.atp-card').forEach(card => {
                    card.style.display = 'none';
                });
                return;
            }
            
            // Generate sets and games
            generateSetsGrid(match);
            generateGameCards(match);
            generateStatisticsCards(match);
            
            // Load existing predictions
            await loadExistingPredictions();
            await loadPredictionSummary();
        }
    } catch (error) {
        console.error('Error fetching match:', error);
        matchDetails.innerHTML = '<p>Error loading match details.</p>';
    }

    function generateSetsGrid(match) {
        setsGrid.innerHTML = '';
        // Dynamically determine number of sets to show based on match format and any previous prediction
        let maxSets = match.match_format === 'best_of_3' ? 3 : 5;
        let previousSets = [];
        if (match.prediction_data && Array.isArray(match.prediction_data.sets)) {
            previousSets = match.prediction_data.sets;
            maxSets = Math.max(maxSets, previousSets.length);
        }
        // Store tiebreak values if present in previousSets
        let previousTiebreaks = [];
        if (previousSets.length > 0) {
            previousTiebreaks = previousSets.map(set => set.tiebreak || '');
        }
        for (let setNum = 1; setNum <= maxSets; setNum++) {
            const setCard = document.createElement('div');
            setCard.className = 'set-card';
            setCard.innerHTML = `
                <h4>Set ${setNum}</h4>
                <div class="set-scores">
                    <input type="number" id="set${setNum}-player1" min="0" max="7" placeholder="0">
                    <span>-</span>
                    <input type="number" id="set${setNum}-player2" min="0" max="7" placeholder="0">
                </div>
                <div class="tiebreak-row" id="tiebreak-row-${setNum}" style="display:none;">
                    <label>Tiebreak: </label>
                    <input type="number" id="tiebreak${setNum}-player1" min="0" max="10" placeholder="0">
                    <span>-</span>
                    <input type="number" id="tiebreak${setNum}-player2" min="0" max="10" placeholder="0">
                </div>
            `;
            setsGrid.appendChild(setCard);
        }
        // If previous sets exist, fill them in (including tiebreaks)
        if (previousSets.length > 0) {
            previousSets.forEach((set, index) => {
                const setNum = index + 1;
                const player1Input = document.getElementById(`set${setNum}-player1`);
                const player2Input = document.getElementById(`set${setNum}-player2`);
                if (player1Input && player2Input) {
                    player1Input.value = set.player1 || '';
                    player2Input.value = set.player2 || '';
                }
                // If tiebreak exists, show tiebreak row and fill values
                if (set.tiebreak) {
                    const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
                    if (tiebreakRow) tiebreakRow.style.display = '';
                    const tb1 = document.getElementById(`tiebreak${setNum}-player1`);
                    const tb2 = document.getElementById(`tiebreak${setNum}-player2`);
                    if (tb1 && tb2) {
                        tb1.value = set.tiebreak.player1 || '';
                        tb2.value = set.tiebreak.player2 || '';
                    }
                }
            });
        }
        // Helper to show/hide tiebreak row for a set
        function updateTiebreakRow(setNum) {
            const p1 = document.getElementById(`set${setNum}-player1`);
            const p2 = document.getElementById(`set${setNum}-player2`);
            const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
            if (!p1 || !p2 || !tiebreakRow) return;
            const v1 = p1.value !== '' ? parseInt(p1.value) : null;
            const v2 = p2.value !== '' ? parseInt(p2.value) : null;
            if ((v1 === 7 && v2 === 6) || (v1 === 6 && v2 === 7)) {
                tiebreakRow.style.display = '';
            } else {
                tiebreakRow.style.display = 'none';
                // Optionally clear tiebreak values if hiding
                document.getElementById(`tiebreak${setNum}-player1`).value = '';
                document.getElementById(`tiebreak${setNum}-player2`).value = '';
            }
        }
        // Attach event listeners to set inputs for tiebreak logic
        for (let i = 1; i <= maxSets; i++) {
            const p1 = document.getElementById(`set${i}-player1`);
            const p2 = document.getElementById(`set${i}-player2`);
            if (p1 && p2) {
                p1.addEventListener('input', () => updateTiebreakRow(i));
                p2.addEventListener('input', () => updateTiebreakRow(i));
                // Initial tiebreak row state
                updateTiebreakRow(i);
            }
        }
        // Add logic to disable remaining sets if a player has already won
        function updateSetDisabling() {
            let setsToWin = match.match_format === 'best_of_5' ? 3 : 2;
            let player1Sets = 0, player2Sets = 0;
            let matchOverAt = null;
            for (let i = 1; i <= maxSets; i++) {
                const p1 = document.getElementById(`set${i}-player1`);
                const p2 = document.getElementById(`set${i}-player2`);
                if (!p1 || !p2) continue;
                const v1 = p1.value !== '' ? parseInt(p1.value) : null;
                const v2 = p2.value !== '' ? parseInt(p2.value) : null;
                if (v1 !== null && v2 !== null) {
                    if (v1 > v2) player1Sets++;
                    else if (v2 > v1) player2Sets++;
                }
                if ((player1Sets === setsToWin || player2Sets === setsToWin) && matchOverAt === null) {
                    matchOverAt = i;
                }
            }
            for (let i = 1; i <= maxSets; i++) {
                const p1 = document.getElementById(`set${i}-player1`);
                const p2 = document.getElementById(`set${i}-player2`);
                if (!p1 || !p2) continue;
                if (matchOverAt !== null && i > matchOverAt) {
                    p1.disabled = true;
                    p2.disabled = true;
                    p1.classList.add('disabled');
                    p2.classList.add('disabled');
                } else {
                    p1.disabled = false;
                    p2.disabled = false;
                    p1.classList.remove('disabled');
                    p2.classList.remove('disabled');
                }
            }
        }
        // Attach event listeners to all set inputs for disabling logic
        for (let i = 1; i <= maxSets; i++) {
            const p1 = document.getElementById(`set${i}-player1`);
            const p2 = document.getElementById(`set${i}-player2`);
            if (p1 && p2) {
                p1.addEventListener('input', updateSetDisabling);
                p2.addEventListener('input', updateSetDisabling);
            }
        }
        // Initial disabling
        updateSetDisabling();
    }

    function generateGameCards(match) {
        gamesGrid.innerHTML = '';
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const gameCard = document.createElement('div');
            gameCard.className = 'game-card';
            gameCard.id = `game-${gameNum}`;

            gameCard.innerHTML = `
                <h3>Point ${gameNum}</h3>
                <div class="game-inputs">
                    <div class="input-group">
                        <label>Winner:</label>
                        <select id="winner-${gameNum}" class="game-winner">
                            <option value="">Select Winner</option>
                            <option value="player1">${match.player1_name}</option>
                            <option value="player2">${match.player2_name}</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Score:</label>
                        <div class="score-input" style="width:100%;display:flex;align-items:center;gap:0.7rem;">
                            <select id="score1-${gameNum}" class="game-score" disabled style="width:60px;min-width:60px;">
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                            <span>-</span>
                            <select id="score2-${gameNum}" class="game-score" disabled style="width:60px;min-width:60px;">
                                <option value="">-</option>
                                <option value="0">0</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="AD">AD</option>
                                <option value="game">GAME</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            // Only show the first game card initially
            if (gameNum !== 1) {
                gameCard.style.display = 'none';
            }
            gamesGrid.appendChild(gameCard);
        }

        // Add event listeners for winner selection to update scores and reveal next card
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winnerSelect = document.getElementById(`winner-${gameNum}`);
            const score1Select = document.getElementById(`score1-${gameNum}`);
            const score2Select = document.getElementById(`score2-${gameNum}`);

            winnerSelect.addEventListener('change', function() {
                // For first game, winner gets 15, loser gets 0
                if (gameNum === 1) {
                    if (winnerSelect.value === 'player1') {
                        score1Select.value = '15';
                        score2Select.value = '0';
                    } else if (winnerSelect.value === 'player2') {
                        score1Select.value = '0';
                        score2Select.value = '15';
                    } else {
                        score1Select.value = '';
                        score2Select.value = '';
                    }
                } else {
                    // For subsequent games, winner's score increases by tennis logic: 0 -> 15 -> 30 -> 40 -> GAME
                    const tennisOrder = ['0', '15', '30', '40', 'AD', 'game'];
                    let prevScore1 = document.getElementById(`score1-${gameNum-1}`).value;
                    let prevScore2 = document.getElementById(`score2-${gameNum-1}`).value;
                    prevScore1 = tennisOrder.includes(prevScore1) ? prevScore1 : '0';
                    prevScore2 = tennisOrder.includes(prevScore2) ? prevScore2 : '0';
                    function nextTennisScore(score, opponentScore) {
                        if (score === '0') return '15';
                        if (score === '15') return '30';
                        if (score === '30') return '40';
                        if (score === '40') {
                            if (opponentScore === '40') return 'AD';
                            if (opponentScore === 'AD') return '40';
                            return 'game';
                        }
                        if (score === 'AD') return 'game';
                        return score;
                    }
                    if (winnerSelect.value === 'player1') {
                        if (prevScore2 === 'AD') {
                            score1Select.value = '40';
                            score2Select.value = '40';
                        } else {
                            score1Select.value = nextTennisScore(prevScore1, prevScore2);
                            score2Select.value = prevScore2;
                        }
                    } else if (winnerSelect.value === 'player2') {
                        if (prevScore1 === 'AD') {
                            score1Select.value = '40';
                            score2Select.value = '40';
                        } else {
                            score1Select.value = prevScore1;
                            score2Select.value = nextTennisScore(prevScore2, prevScore1);
                        }
                    } else {
                        score1Select.value = '';
                        score2Select.value = '';
                    }
                }
                // Reveal the next game card if needed
                const currentGameCard = document.getElementById(`game-${gameNum}`);
                const nextGameCard = document.getElementById(`game-${gameNum+1}`);
                if (score1Select.value === 'game' || score2Select.value === 'game') {
                    // Stop revealing further cards
                } else if (nextGameCard) {
                    nextGameCard.style.display = '';
                }
            });
        }
    }

    function generateStatisticsCards(match) {
        const statisticsGrid = document.getElementById('statistics-grid');
        statisticsGrid.innerHTML = '';
        
        // Create Player 1 statistics card
        const player1Card = document.createElement('div');
        player1Card.className = 'statistics-card';
        player1Card.innerHTML = `
            <h3>${match.player1_name}</h3>
            <div class="statistics-inputs">
                <div class="statistics-input-group">
                    <label>Aces</label>
                    <input type="number" id="aces-player1" min="0" max="50" placeholder="0">
                </div>
                <div class="statistics-input-group">
                    <label>Double Faults</label>
                    <input type="number" id="double-faults-player1" min="0" max="20" placeholder="0">
                </div>
            </div>
        `;
        statisticsGrid.appendChild(player1Card);
        
        // Create Player 2 statistics card
        const player2Card = document.createElement('div');
        player2Card.className = 'statistics-card';
        player2Card.innerHTML = `
            <h3>${match.player2_name}</h3>
            <div class="statistics-inputs">
                <div class="statistics-input-group">
                    <label>Aces</label>
                    <input type="number" id="aces-player2" min="0" max="50" placeholder="0">
                </div>
                <div class="statistics-input-group">
                    <label>Double Faults</label>
                    <input type="number" id="double-faults-player2" min="0" max="20" placeholder="0">
                </div>
            </div>
        `;
        statisticsGrid.appendChild(player2Card);
    }

    async function loadExistingPredictions() {
        try {
            // Load match predictions - Fix: use actual user ID, not matchId
            const matchResponse = await fetch(`../api/predictions.php?match_id=${matchId}&user_id=${user_id}`);
            const matchData = await matchResponse.json();
            
            console.log('Match prediction API response:', matchData);
            
            if (matchData.success && matchData.prediction) {
                const predData = matchData.prediction.prediction_data;
                console.log('Prediction data:', predData);
                
                // Set winner
                if (predData.winner) {
                    document.getElementById(`winner-${predData.winner}`).checked = true;
                }
                
                // Set set scores
                if (predData.sets) {
                    predData.sets.forEach((set, index) => {
                        const setNum = index + 1;
                        const player1Input = document.getElementById(`set${setNum}-player1`);
                        const player2Input = document.getElementById(`set${setNum}-player2`);
                        
                        if (player1Input && player2Input) {
                            player1Input.value = set.player1 || '';
                            player2Input.value = set.player2 || '';
                        }
                    });
                }
                
                // Disable save button since prediction exists
                saveMatchBtn.disabled = true;
                saveMatchBtn.textContent = 'Prediction Submitted';
            } else {
                console.log('No match prediction found or API error');
                // Enable save button since no prediction exists
                saveMatchBtn.disabled = false;
                saveMatchBtn.textContent = 'Save Match Prediction';
            }
            
            // Load game predictions
            const gameResponse = await fetch(`../api/game_predictions.php?match_id=${matchId}&user_predictions=1`);
            const gameData = await gameResponse.json();
            
            console.log('Game prediction API response:', gameData);
            
            if (gameData.success && gameData.predictions && gameData.predictions.length > 0) {
                gameData.predictions.forEach(prediction => {
                    const winnerSelect = document.getElementById(`winner-${prediction.game_number}`);
                    const score1Select = document.getElementById(`score1-${prediction.game_number}`);
                    const score2Select = document.getElementById(`score2-${prediction.game_number}`);
                    
                    if (winnerSelect) winnerSelect.value = prediction.predicted_winner;
                    
                    const scores = prediction.predicted_score.split('-');
                    if (score1Select && scores[0]) score1Select.value = scores[0];
                    if (score2Select && scores[1]) score2Select.value = scores[1];
                });
                
                // Disable save button since predictions exist
                saveGameBtn.disabled = true;
                saveGameBtn.textContent = 'Game Predictions Submitted';
            } else {
                console.log('No game predictions found or API error');
                // Enable save button since no predictions exist
                saveGameBtn.disabled = false;
                saveGameBtn.textContent = 'Save Game Predictions';
            }
            
            // Load statistics predictions
            const statisticsResponse = await fetch(`../api/statistics_predictions.php?match_id=${matchId}&user_predictions=1`);
            const statisticsData = await statisticsResponse.json();
            
            console.log('Statistics prediction API response:', statisticsData);
            
            if (statisticsData.success && statisticsData.predictions && statisticsData.predictions.length > 0) {
                statisticsData.predictions.forEach(prediction => {
                    const acesInput = document.getElementById(`aces-${prediction.player_type}`);
                    const doubleFaultsInput = document.getElementById(`double-faults-${prediction.player_type}`);
                    
                    if (acesInput) acesInput.value = prediction.aces_predicted;
                    if (doubleFaultsInput) doubleFaultsInput.value = prediction.double_faults_predicted;
                });
                
                // Disable save button since predictions exist
                saveStatisticsBtn.disabled = true;
                saveStatisticsBtn.textContent = 'Statistics Predictions Submitted';
            } else {
                console.log('No statistics predictions found or API error');
                // Enable save button since no predictions exist
                saveStatisticsBtn.disabled = false;
                saveStatisticsBtn.textContent = 'Save Statistics Predictions';
            }
        } catch (error) {
            console.error('Error loading predictions:', error);
        }
    }

    // Helper function to format score display
    function formatScoreDisplay(score) {
        return score.replace(/game/g, 'GAME');
    }

    async function loadPredictionSummary() {
        try {
            const [matchResponse, gameResponse, statisticsResponse] = await Promise.all([
                fetch(`../api/predictions.php?match_id=${matchId}&user_id=${user_id}`),
                fetch(`../api/game_predictions.php?match_id=${matchId}&user_predictions=1`),
                fetch(`../api/statistics_predictions.php?match_id=${matchId}&user_predictions=1`)
            ]);
            
            const matchData = await matchResponse.json();
            const gameData = await gameResponse.json();
            const statisticsData = await statisticsResponse.json();
            
            let summaryHTML = '<h3>Your Current Predictions</h3>';
            
            // Match prediction summary
            if (matchData.success && matchData.prediction) {
                const predData = matchData.prediction.prediction_data;
                summaryHTML += `
                    <div style="margin-bottom: 1rem;">
                        <strong>Match Winner:</strong> ${predData.winner === 'player1' ? match.player1_name : match.player2_name}<br>
                        <strong>Set Scores:</strong> ${predData.sets.map((set, i) => {
                            let score = `Set ${i+1}: ${set.player1}-${set.player2}`;
                            if (set.tiebreak && (typeof set.tiebreak.player1 !== 'undefined' || typeof set.tiebreak.player2 !== 'undefined')) {
                                const tb1 = set.tiebreak.player1 !== undefined && set.tiebreak.player1 !== '' ? set.tiebreak.player1 : '';
                                const tb2 = set.tiebreak.player2 !== undefined && set.tiebreak.player2 !== '' ? set.tiebreak.player2 : '';
                                if (tb1 !== '' || tb2 !== '') {
                                    score += ` <span style='color:#ffd54f;'>(TB: ${tb1}-${tb2})</span>`;
                                }
                            }
                            return score;
                        }).join(', ')}
                    </div>
                `;
            } else {
                summaryHTML += '<div style="margin-bottom: 1rem;"><em>No match prediction submitted yet.</em></div>';
            }
            
            // Game prediction summary
            if (gameData.success && gameData.predictions && gameData.predictions.length > 0) {
                // Sort predictions by game number
                const sortedPredictions = gameData.predictions.sort((a, b) => a.game_number - b.game_number);
                
                summaryHTML += `
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong>Game Predictions (Set 1):</strong>
                        </div>
                        <div class="game-predictions-sequence">
                `;
                
                sortedPredictions.forEach(prediction => {
                    const winnerName = prediction.predicted_winner === 'player1' ? match.player1_name : match.player2_name;
                    summaryHTML += `
                        <div class="game-prediction-item">
                            <div class="game-number">Point ${prediction.game_number}</div>
                            <div class="game-score">${formatScoreDisplay(prediction.predicted_score)}</div>
                            <div class="game-winner">${winnerName}</div>
                        </div>
                    `;
                });
                
                summaryHTML += `
                        </div>
                    </div>
                `;
            } else {
                summaryHTML += '<div style="margin-bottom: 1rem;"><em>No game predictions submitted yet.</em></div>';
            }
            
            // Statistics prediction summary
            if (statisticsData.success && statisticsData.predictions && statisticsData.predictions.length > 0) {
                summaryHTML += `
                    <div style="margin-bottom: 1rem;">
                        <strong>Statistics Predictions:</strong><br>
                `;
                
                statisticsData.predictions.forEach(prediction => {
                    const playerName = prediction.player_type === 'player1' ? match.player1_name : match.player2_name;
                    summaryHTML += `
                        <span style="color: #ffd54f;">${playerName}:</span> ${prediction.aces_predicted} aces, ${prediction.double_faults_predicted} double faults<br>
                    `;
                });
                
                summaryHTML += '</div>';
            } else {
                summaryHTML += '<div style="margin-bottom: 1rem;"><em>No statistics predictions submitted yet.</em></div>';
            }
            
            predictionSummary.innerHTML = summaryHTML;
        } catch (error) {
            console.error('Error loading prediction summary:', error);
        }
    }

    // Match prediction handlers
    saveMatchBtn.addEventListener('click', async function() {
        const winner = document.querySelector('input[name="winner"]:checked')?.value;
        const sets = [];
        // Collect set scores and tiebreaks
        const setCards = document.querySelectorAll('.set-card');
        for (let i = 0; i < setCards.length; i++) {
            const setNum = i + 1;
            const player1Input = document.getElementById(`set${setNum}-player1`);
            const player2Input = document.getElementById(`set${setNum}-player2`);
            if (player1Input && player2Input && player1Input.value !== '' && player2Input.value !== '') {
                const setObj = {
                    player1: parseInt(player1Input.value),
                    player2: parseInt(player2Input.value)
                };
                // If tiebreak row is visible and has values, add tiebreak property
                const tiebreakRow = document.getElementById(`tiebreak-row-${setNum}`);
                if (tiebreakRow && tiebreakRow.style.display !== 'none') {
                    const tb1 = document.getElementById(`tiebreak${setNum}-player1`);
                    const tb2 = document.getElementById(`tiebreak${setNum}-player2`);
                    if (tb1 && tb2 && (tb1.value !== '' || tb2.value !== '')) {
                        setObj.tiebreak = {
                            player1: tb1.value !== '' ? parseInt(tb1.value) : '',
                            player2: tb2.value !== '' ? parseInt(tb2.value) : ''
                        };
                    }
                }
                sets.push(setObj);
            }
        }
        // Dynamically determine how many sets are needed to win
        let setsToWin = 2;
        if (sets.length > 0 && sets.length > 3) setsToWin = 3;
        if (typeof match !== 'undefined' && match && match.match_format === 'best_of_5') setsToWin = 3;

        // Count sets won by each player
        let player1Sets = 0, player2Sets = 0;
        sets.forEach(set => {
            if (set.player1 > set.player2) player1Sets++;
            else if (set.player2 > set.player1) player2Sets++;
        });

        // Only allow submission if a player has won the required number of sets
        if (!winner) {
            showMessage('Please select a match winner.', 'error');
            return;
        }
        if (sets.length === 0) {
            showMessage('Please predict at least one set score.', 'error');
            return;
        }
        if ((winner === 'player1' && player1Sets < setsToWin) || (winner === 'player2' && player2Sets < setsToWin)) {
            showMessage(`The selected winner must win at least ${setsToWin} sets.`, 'error');
            return;
        }
        if (player1Sets === player2Sets) {
            showMessage('There cannot be a tie in sets. Please adjust your set scores.', 'error');
            return;
        }
        try {
            const response = await fetch('../api/predictions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    match_id: matchId,
                    winner: winner,
                    sets: sets
                })
            });
            
            const result = await response.json();
            console.log('Save match prediction result:', result);
            
            if (result.success) {
                showMessage(result.message, 'success');
                saveMatchBtn.disabled = true;
                saveMatchBtn.textContent = 'Prediction Submitted';
                await loadExistingPredictions();
                await loadPredictionSummary();
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving match prediction:', error);
            showMessage('Error saving prediction. Please try again.', 'error');
        }
    });

    clearMatchBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to clear your match prediction? This will permanently delete it from the database.')) {
            try {
                const response = await fetch(`../api/predictions.php?match_id=${matchId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                console.log('Delete match prediction result:', result);
                
                if (result.success) {
                    // Clear form fields
                    document.querySelectorAll('input[name="winner"]').forEach(radio => radio.checked = false);
                    document.querySelectorAll('.set-card input').forEach(input => input.value = '');
                    // Re-enable save button
                    saveMatchBtn.disabled = false;
                    saveMatchBtn.textContent = 'Save Match Prediction';
                    showMessage(result.message, 'success');
                    await loadPredictionSummary();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting match prediction:', error);
                showMessage('Error deleting prediction. Please try again.', 'error');
            }
        }
    });

    // Game prediction handlers
    saveGameBtn.addEventListener('click', async function() {
        const predictions = [];
        let hasErrors = false;
        
        // Collect all predictions
        for (let gameNum = 1; gameNum <= 12; gameNum++) {
            const winner = document.getElementById(`winner-${gameNum}`).value;
            const score1 = document.getElementById(`score1-${gameNum}`).value;
            const score2 = document.getElementById(`score2-${gameNum}`).value;
            
            if (winner && score1 && score2) {
                predictions.push({
                    match_id: matchId,
                    game_number: gameNum,
                    predicted_winner: winner,
                    predicted_score: `${score1}-${score2}`
                });
            }
        }
        
        if (predictions.length === 0) {
            showMessage('Please make at least one game prediction before saving.', 'error');
            return;
        }
        
        // Save predictions
        let savedCount = 0;
        for (const prediction of predictions) {
            try {
                const response = await fetch('../api/game_predictions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(prediction)
                });
                
                const result = await response.json();
                console.log('Save game prediction result:', result);
                if (result.success) {
                    savedCount++;
                }
            } catch (error) {
                console.error('Error saving prediction:', error);
            }
        }
        
        if (savedCount === predictions.length) {
            showMessage(`Successfully saved ${savedCount} game predictions!`, 'success');
            saveGameBtn.disabled = true;
            saveGameBtn.textContent = 'Game Predictions Submitted';
            await loadExistingPredictions();
            await loadPredictionSummary();
        } else {
            showMessage(`Saved ${savedCount} out of ${predictions.length} predictions.`, 'warning');
        }
    });

    clearGameBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to clear all game predictions? This will permanently delete them from the database.')) {
            try {
                const response = await fetch(`../api/game_predictions.php?match_id=${matchId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                console.log('Delete game predictions result:', result);
                
                if (result.success) {
                    // Clear form fields
                    for (let gameNum = 1; gameNum <= 12; gameNum++) {
                        document.getElementById(`winner-${gameNum}`).value = '';
                        document.getElementById(`score1-${gameNum}`).value = '';
                        document.getElementById(`score2-${gameNum}`).value = '';
                    }
                    // Re-enable save button
                    saveGameBtn.disabled = false;
                    saveGameBtn.textContent = 'Save Game Predictions';
                    showMessage(result.message, 'success');
                    await loadPredictionSummary();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting game predictions:', error);
                showMessage('Error deleting predictions. Please try again.', 'error');
            }
        }
    });

    // Statistics prediction handlers
    saveStatisticsBtn.addEventListener('click', async function() {
        const predictions = [];
        
        // Collect statistics predictions for both players
        const player1Aces = document.getElementById('aces-player1').value;
        const player1DoubleFaults = document.getElementById('double-faults-player1').value;
        const player2Aces = document.getElementById('aces-player2').value;
        const player2DoubleFaults = document.getElementById('double-faults-player2').value;
        
        // Validate inputs
        if (!player1Aces || !player1DoubleFaults || !player2Aces || !player2DoubleFaults) {
            showMessage('Please fill in all statistics predictions for both players.', 'error');
            return;
        }
        
        // Add player 1 predictions
        predictions.push({
            match_id: matchId,
            player_type: 'player1',
            aces_predicted: parseInt(player1Aces),
            double_faults_predicted: parseInt(player1DoubleFaults)
        });
        
        // Add player 2 predictions
        predictions.push({
            match_id: matchId,
            player_type: 'player2',
            aces_predicted: parseInt(player2Aces),
            double_faults_predicted: parseInt(player2DoubleFaults)
        });
        
        // Save predictions
        let savedCount = 0;
        for (const prediction of predictions) {
            try {
                const response = await fetch('../api/statistics_predictions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(prediction)
                });
                
                const result = await response.json();
                console.log('Save statistics prediction result:', result);
                if (result.success) {
                    savedCount++;
                }
            } catch (error) {
                console.error('Error saving statistics prediction:', error);
            }
        }
        
        if (savedCount === predictions.length) {
            showMessage(`Successfully saved statistics predictions for both players!`, 'success');
            saveStatisticsBtn.disabled = true;
            saveStatisticsBtn.textContent = 'Statistics Predictions Submitted';
            await loadExistingPredictions();
            await loadPredictionSummary();
        } else {
            showMessage(`Saved ${savedCount} out of ${predictions.length} statistics predictions.`, 'warning');
        }
    });

    clearStatisticsBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to clear all statistics predictions? This will permanently delete them from the database.')) {
            try {
                const response = await fetch(`../api/statistics_predictions.php?match_id=${matchId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                console.log('Delete statistics predictions result:', result);
                
                if (result.success) {
                    // Clear form fields
                    document.getElementById('aces-player1').value = '';
                    document.getElementById('double-faults-player1').value = '';
                    document.getElementById('aces-player2').value = '';
                    document.getElementById('double-faults-player2').value = '';
                    // Re-enable save button
                    saveStatisticsBtn.disabled = false;
                    saveStatisticsBtn.textContent = 'Save Statistics Predictions';
                    showMessage(result.message, 'success');
                    await loadPredictionSummary();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting statistics predictions:', error);
                showMessage('Error deleting predictions. Please try again.', 'error');
            }
        }
    });

    function showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message
        const messageEl = document.createElement('div');
        messageEl.className = `message ${type}`;
        messageEl.textContent = message;
        
        // Insert after the first card
        const firstCard = document.querySelector('.atp-card');
        if (firstCard && firstCard.parentNode) {
            firstCard.parentNode.insertBefore(messageEl, firstCard.nextSibling);
        }
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>