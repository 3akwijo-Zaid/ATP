-- Statistics Predictions Schema
-- This adds prediction system for player statistics like double faults and aces

-- STATISTICS PREDICTIONS TABLE
CREATE TABLE statistics_predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    player_type ENUM('player1', 'player2') NOT NULL,
    aces_predicted INT NOT NULL DEFAULT 0,
    double_faults_predicted INT NOT NULL DEFAULT 0,
    points_awarded INT DEFAULT 0,
    correct TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_statistics_prediction (user_id, match_id, player_type)
);

-- STATISTICS RESULTS TABLE
CREATE TABLE statistics_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    player_type ENUM('player1', 'player2') NOT NULL,
    aces_actual INT NOT NULL DEFAULT 0,
    double_faults_actual INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_statistics_result (match_id, player_type)
);

-- EXTENDED POINT SETTINGS TABLE
-- Add points for statistics predictions
ALTER TABLE point_settings 
ADD COLUMN aces_exact_points INT DEFAULT 15,
ADD COLUMN aces_close_points INT DEFAULT 5,
ADD COLUMN double_faults_exact_points INT DEFAULT 15,
ADD COLUMN double_faults_close_points INT DEFAULT 5;

-- SAMPLE DATA for point_settings (update existing record)
UPDATE point_settings SET 
    aces_exact_points = 15,
    aces_close_points = 5,
    double_faults_exact_points = 15,
    double_faults_close_points = 5
WHERE id = 1; 