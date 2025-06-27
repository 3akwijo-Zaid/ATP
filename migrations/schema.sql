CREATE DATABASE tennis_predictions;

USE tennis_predictions;

-- USERS TABLE
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  points INT DEFAULT 0,
  is_admin TINYINT(1) DEFAULT 0
);

-- MATCHES TABLE
CREATE TABLE matches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  competition_name VARCHAR(100) NOT NULL,
  player1 VARCHAR(100) NOT NULL,
  player2 VARCHAR(100) NOT NULL,
  start_time DATETIME NOT NULL,
  match_format ENUM('best_of_3','best_of_5') DEFAULT 'best_of_5',
  status ENUM('upcoming','in_progress','finished') DEFAULT 'upcoming',
  winner VARCHAR(100),
  result_summary VARCHAR(255)
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
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

-- POINT SETTINGS TABLE
CREATE TABLE point_settings (
  id INT PRIMARY KEY,
  match_winner_points INT DEFAULT 10,
  set_winner_points INT DEFAULT 3,
  set_score_points INT DEFAULT 5
);

-- SAMPLE DATA
INSERT INTO users (username, password_hash, is_admin) VALUES
('admin', '$2y$10$VylwYl4DOU9xqc1b7k/ssuHlTz9L8tFq.eX6B.3h5JqD8mR.4kS2K', 1);

INSERT INTO point_settings (id, match_winner_points, set_winner_points, set_score_points) VALUES
(1, 10, 3, 5); 