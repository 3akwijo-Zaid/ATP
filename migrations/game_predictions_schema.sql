-- Game Predictions Schema
-- This adds detailed game-by-game prediction system for Set 1

-- GAME PREDICTIONS TABLE
CREATE TABLE game_predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    game_number INT NOT NULL, -- Game 1, 2, 3, etc. in Set 1
    predicted_winner ENUM('player1', 'player2') NOT NULL,
    predicted_score VARCHAR(10) NOT NULL, -- e.g., "40-0", "30-15", "AD-40"
    points_awarded INT DEFAULT 0,
    correct TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_game_prediction (user_id, match_id, game_number)
);

-- GAME RESULTS TABLE
CREATE TABLE game_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    game_number INT NOT NULL, -- Game 1, 2, 3, etc. in Set 1
    winner ENUM('player1', 'player2') NOT NULL,
    final_score VARCHAR(10) NOT NULL, -- e.g., "40-0", "30-15", "AD-40"
    game_duration_seconds INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_game_result (match_id, game_number)
);

-- SET 1 COMPLETION TABLE
CREATE TABLE set1_completion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    winner ENUM('player1', 'player2') NOT NULL,
    final_game INT NOT NULL, -- The game number where Set 1 was won (e.g., 6, 7, 12)
    final_score VARCHAR(10) NOT NULL, -- e.g., "6-4", "7-5", "7-6(5)"
    completed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_set1_completion (match_id)
);

-- EXTENDED POINT SETTINGS TABLE
-- Extend existing point_settings table with game-level points
ALTER TABLE point_settings 
ADD COLUMN game_winner_points INT DEFAULT 2,
ADD COLUMN game_score_points INT DEFAULT 5,
ADD COLUMN exact_game_score_points INT DEFAULT 10,
ADD COLUMN set1_complete_points INT DEFAULT 20;

-- SAMPLE DATA for point_settings (update existing record)
UPDATE point_settings SET 
    game_winner_points = 2,
    game_score_points = 5,
    exact_game_score_points = 10,
    set1_complete_points = 20
WHERE id = 1; 