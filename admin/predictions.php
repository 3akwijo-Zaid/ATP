
<?php require_once 'includes/header.php'; ?>

<main class="tennis-prediction-hero">
  <!-- Hero Section -->
  <section class="hero-court-bg">
    <div class="hero-content">
      <div class="player player-left">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Jannik_Sinner_%28cropped%29.jpg" alt="Jannik Sinner" class="player-img main-player">
        <div class="player-name">Jannik Sinner <span class="flag">🇮🇹</span></div>
      </div>
      <div class="vs-circle">VS</div>
      <div class="player player-right">
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Carlos_Alcaraz_%28cropped%29.jpg" alt="Carlos Alcaraz" class="player-img faded-opponent">
        <div class="player-name">Carlos Alcaraz <span class="flag">🇪🇸</span></div>
      </div>
    </div>
    <div class="match-meta">
      <span class="tournament">Wimbledon Championships</span>
      <span class="round">Semifinal</span>
      <span class="start-time">July 12, 2025 &bull; 15:00</span>
    </div>
  </section>

  <!-- Winner Prediction Section -->
  <section class="winner-prediction">
    <h1>Our Pick: <span class="winner-name">Jannik Sinner</span></h1>
    <blockquote class="winner-quote">“The ice-cold Italian will rise above the Centre Court pressure.”</blockquote>
    <div class="winner-visual">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Jannik_Sinner_%28cropped%29.jpg" alt="Jannik Sinner" class="winner-photo">
      <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Carlos_Alcaraz_%28cropped%29.jpg" alt="Carlos Alcaraz" class="opponent-blur">
    </div>
  </section>

  <!-- Set-by-Set Storyline Section -->
  <section class="match-storyline">
    <h2>Match Storyline</h2>
    <div class="sets-list">
      <div class="set-card set-win">
        <span class="set-label">Set 1</span>
        <span class="set-score">6–4</span>
        <span class="set-winner">✅ Sinner</span>
      </div>
      <div class="set-card set-loss">
        <span class="set-label">Set 2</span>
        <span class="set-score">6–7 <span class="tiebreak">(5–7)</span></span>
        <span class="set-winner">❌ Alcaraz</span>
      </div>
      <div class="set-card set-win">
        <span class="set-label">Set 3</span>
        <span class="set-score">6–3</span>
        <span class="set-winner">✅ Sinner</span>
      </div>
    </div>
  </section>

  <!-- First Game Prediction Section -->
  <section class="first-game-prediction">
    <h2>Opening Move</h2>
    <div class="first-game-card">
      <span class="first-game-text">Sinner to win the first game <span class="first-game-detail">(Serving)</span></span>
    </div>
  </section>

  <!-- Footer -->
  <footer class="prediction-footer">
    <nav>
      <a href="/public/index.php">Home</a>
      <a href="/public/fixtures.php">Matches</a>
      <a href="#">About</a>
      <a href="#">Contact</a>
    </nav>
  </footer>
</main>

<style>
body, html {
  margin: 0;
  padding: 0;
  font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
  background: #1b3a2b;
  color: #fff;
}
.tennis-prediction-hero {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(180deg, #1b3a2b 0%, #2e8b57 100%);
}
.hero-court-bg {
  background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
  padding: 4rem 0 2rem 0;
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}
.hero-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3rem;
  position: relative;
  z-index: 2;
}
.player {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.player-img {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border-radius: 50%;
  border: 5px solid #fff;
  box-shadow: 0 4px 24px rgba(0,0,0,0.3);
  background: #fff;
}
.main-player {
  filter: none;
  z-index: 2;
}
.faded-opponent {
  filter: blur(2px) grayscale(60%) brightness(0.7);
  opacity: 0.7;
  z-index: 1;
}
.player-name {
  margin-top: 1rem;
  font-size: 1.3rem;
  font-weight: bold;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.flag {
  font-size: 1.5rem;
  margin-left: 0.5rem;
}
.vs-circle {
  background: #fff;
  color: #2e8b57;
  font-weight: 900;
  font-size: 2.2rem;
  border-radius: 50%;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 12px rgba(0,0,0,0.2);
  margin: 0 2rem;
  z-index: 3;
}
.match-meta {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 2rem;
  font-size: 1.2rem;
  font-weight: 600;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.tournament {
  color: #ffe066;
}
.round {
  color: #fff;
  background: #2e8b57;
  padding: 0.2em 0.8em;
  border-radius: 1em;
  margin: 0 0.5em;
}
.start-time {
  color: #b2f7ef;
}

/* Winner Prediction Section */
.winner-prediction {
  background: #fff;
  color: #1b3a2b;
  text-align: center;
  padding: 3rem 1rem 2rem 1rem;
  border-radius: 0 0 40px 40px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  margin-bottom: 2rem;
  position: relative;
  z-index: 2;
}
.winner-prediction h1 {
  font-size: 2.5rem;
  font-weight: 900;
  margin-bottom: 1rem;
  letter-spacing: 2px;
}
.winner-name {
  color: #2e8b57;
  text-shadow: 0 2px 8px #b2f7ef;
}
.winner-quote {
  font-size: 1.2rem;
  font-style: italic;
  color: #444;
  margin-bottom: 2rem;
}
.winner-visual {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 200px;
}
.winner-photo {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  border: 6px solid #2e8b57;
  box-shadow: 0 4px 24px rgba(46,139,87,0.2);
  z-index: 2;
  background: #fff;
}
.opponent-blur {
  position: absolute;
  right: 20%;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  filter: blur(3px) grayscale(80%) brightness(0.7);
  opacity: 0.5;
  z-index: 1;
  border: 4px solid #eee;
}

/* Set-by-Set Storyline Section */
.match-storyline {
  background: #2e8b57;
  color: #fff;
  padding: 2.5rem 1rem 2rem 1rem;
  border-radius: 40px;
  margin: 2rem auto;
  max-width: 700px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
}
.match-storyline h2 {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 2rem;
  text-align: center;
  letter-spacing: 1px;
}
.sets-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.set-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  color: #1b3a2b;
  border-radius: 18px;
  padding: 1.2rem 2rem;
  font-size: 1.2rem;
  font-weight: 700;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
.set-win {
  border-left: 8px solid #2e8b57;
  background: linear-gradient(90deg, #e0ffe6 0%, #fff 100%);
}
.set-loss {
  border-left: 8px solid #f44336;
  background: linear-gradient(90deg, #ffeaea 0%, #fff 100%);
}
.set-label {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2e8b57;
}
.set-score {
  font-size: 1.3rem;
  font-weight: 900;
  margin: 0 1.5rem;
}
.set-winner {
  font-size: 1.1rem;
  font-weight: 700;
}
.set-winner .tiebreak {
  font-size: 1rem;
  color: #888;
}
.set-card .tiebreak {
  font-size: 1rem;
  color: #888;
}

/* First Game Prediction Section */
.first-game-prediction {
  background: #ffe066;
  color: #1b3a2b;
  padding: 2.5rem 1rem 2rem 1rem;
  border-radius: 40px;
  margin: 2rem auto 3rem auto;
  max-width: 600px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  text-align: center;
}
.first-game-prediction h2 {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  letter-spacing: 1px;
}
.first-game-card {
  background: #fff;
  color: #2e8b57;
  border-radius: 18px;
  padding: 1.5rem 2rem;
  font-size: 1.4rem;
  font-weight: 900;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  display: inline-block;
}
.first-game-detail {
  font-size: 1.1rem;
  color: #888;
  margin-left: 0.5rem;
}

/* Footer */
.prediction-footer {
  background: #1b3a2b;
  color: #fff;
  padding: 2rem 0 1rem 0;
  text-align: center;
  border-radius: 40px 40px 0 0;
  margin-top: auto;
  box-shadow: 0 -4px 24px rgba(0,0,0,0.18);
}
.prediction-footer nav {
  display: flex;
  justify-content: center;
  gap: 2.5rem;
}
.prediction-footer a {
  color: #ffe066;
  text-decoration: none;
  font-weight: 700;
  font-size: 1.1rem;
  transition: color 0.2s;
}
.prediction-footer a:hover {
  color: #fff;
  text-shadow: 0 2px 8px #ffe066;
}

@media (max-width: 900px) {
  .hero-content {
    flex-direction: column;
    gap: 1.5rem;
  }
  .vs-circle {
    margin: 1.5rem 0;
  }
}
@media (max-width: 600px) {
  .hero-court-bg {
    padding: 2rem 0 1rem 0;
  }
  .winner-prediction {
    padding: 2rem 0.5rem 1.5rem 0.5rem;
  }
  .match-storyline, .first-game-prediction {
    padding: 1.5rem 0.5rem 1.2rem 0.5rem;
    border-radius: 20px;
  }
  .set-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 1rem 1rem;
  }
  .first-game-card {
    padding: 1rem 1rem;
    font-size: 1.1rem;
  }
  .prediction-footer nav {
    gap: 1.2rem;
  }
}
</style>

<?php require_once 'includes/footer.php'; ?>