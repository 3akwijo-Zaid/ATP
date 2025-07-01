CREATE DATABASE tennis_predictions;

USE tennis_predictions;

-- USERS TABLE
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  country VARCHAR(8) DEFAULT NULL,
  points INT DEFAULT 0,
  is_admin TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TOURNAMENTS TABLE
CREATE TABLE tournaments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  logo VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- PLAYERS TABLE
CREATE TABLE players (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  image VARCHAR(255),
  country VARCHAR(64),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- MATCHES TABLE
CREATE TABLE matches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tournament_id INT NOT NULL,
  round VARCHAR(32) NOT NULL,
  player1_id INT NOT NULL,
  player2_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  match_format ENUM('best_of_3','best_of_5') DEFAULT 'best_of_5',
  featured TINYINT(1) DEFAULT 0,
  status ENUM('upcoming','in_progress','finished') DEFAULT 'upcoming',
  winner_id INT,
  result_summary VARCHAR(255),
  competition_name VARCHAR(100), -- for legacy support
  game_predictions_enabled TINYINT(1) DEFAULT 1,
  statistics_predictions_enabled TINYINT(1) DEFAULT 1,
  FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
  FOREIGN KEY (player1_id) REFERENCES players(id) ON DELETE CASCADE,
  FOREIGN KEY (player2_id) REFERENCES players(id) ON DELETE CASCADE,
  FOREIGN KEY (winner_id) REFERENCES players(id) ON DELETE SET NULL,
  INDEX idx_tournament (tournament_id),
  INDEX idx_round (round),
  INDEX idx_start_time (start_time)
);

-- MATCH SETS TABLE
CREATE TABLE match_sets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  match_id INT NOT NULL,
  set_number INT NOT NULL,
  player1_games INT NOT NULL,
  player2_games INT NOT NULL,
  player1_tiebreak_points INT DEFAULT NULL,
  player2_tiebreak_points INT DEFAULT NULL,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

-- PREDICTIONS TABLE
CREATE TABLE predictions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  match_id INT NOT NULL,
  prediction_data JSON NOT NULL,
  points_awarded INT DEFAULT 0,
  points_earned INT DEFAULT 0,
  correct TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_match (user_id, match_id)
);

-- GAME PREDICTIONS TABLE
CREATE TABLE game_predictions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  match_id INT NOT NULL,
  game_number INT NOT NULL,
  predicted_winner VARCHAR(16) NOT NULL,
  predicted_score VARCHAR(16) NOT NULL,
  points_awarded INT DEFAULT 0,
  correct TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_match_game (user_id, match_id, game_number)
);

-- GAME RESULTS TABLE
CREATE TABLE game_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  match_id INT NOT NULL,
  game_number INT NOT NULL,
  winner VARCHAR(16) NOT NULL,
  final_score VARCHAR(16) NOT NULL,
  game_duration_seconds INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_match_game (match_id, game_number)
);

-- SET1 COMPLETION TABLE
CREATE TABLE set1_completion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  match_id INT NOT NULL,
  winner VARCHAR(16) NOT NULL,
  final_game INT NOT NULL,
  final_score VARCHAR(16) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_match_set1 (match_id)
);

-- STATISTICS PREDICTIONS TABLE
CREATE TABLE statistics_predictions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  match_id INT NOT NULL,
  player_type VARCHAR(16) NOT NULL,
  aces_predicted INT NOT NULL,
  double_faults_predicted INT NOT NULL,
  points_awarded INT DEFAULT 0,
  correct TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_match_player (user_id, match_id, player_type)
);

-- POINT SETTINGS TABLE
CREATE TABLE point_settings (
  id INT PRIMARY KEY,
  match_winner_points INT DEFAULT 10,
  match_score_points INT DEFAULT 10,
  set_score_points INT DEFAULT 5,
  tiebreak_score_points INT DEFAULT 0,
  game_winner_points INT DEFAULT 2,
  game_score_points INT DEFAULT 5,
  exact_game_score_points INT DEFAULT 10,
  set1_complete_points INT DEFAULT 20,
  aces_exact_points INT DEFAULT 5,
  aces_close_points INT DEFAULT 2,
  double_faults_exact_points INT DEFAULT 5,
  double_faults_close_points INT DEFAULT 2
);

-- SAMPLE DATA
INSERT INTO users (username, password_hash, is_admin) VALUES
('admin', '$2y$10$VylwYl4DOU9xqc1b7k/ssuHlTz9L8tFq.eX6B.3h5JqD8mR.4kS2K', 1);

-- JOKER PREDICTIONS TABLE
CREATE TABLE joker_predictions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tournament_id INT NOT NULL,
  round VARCHAR(32) NOT NULL,
  match_id INT NOT NULL,
  prediction_type ENUM('match', 'game', 'statistics') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_tournament_round (user_id, tournament_id, round)
);

INSERT INTO point_settings (id, match_winner_points, match_score_points, set_score_points, 
                           game_winner_points, game_score_points, exact_game_score_points, 
                           set1_complete_points, aces_exact_points, aces_close_points, 
                           double_faults_exact_points, double_faults_close_points) VALUES
(1, 10, 10, 5, 2, 5, 10, 20, 5, 2, 5, 2);