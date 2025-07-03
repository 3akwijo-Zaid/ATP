<?php
require_once 'Database.php';

class GamePrediction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function submitGamePrediction($userId, $matchId, $gameNumber, $predictedWinner, $predictedScore) {
        // Check if prediction is locked (5 minutes before match)
        $this->db->query('SELECT start_time FROM matches WHERE id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $match = $this->db->single();
        
        if ($match) {
            $startTime = strtotime($match['start_time']);
            if ($startTime - time() <= 300) {
                return ['error' => 'Predictions are locked for this match.'];
            }
        }

        // Check if user already has a prediction for this game
        $this->db->query('SELECT id FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id AND game_number = :game_number');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':game_number', $gameNumber);
        $existingPrediction = $this->db->single();
        
        if ($existingPrediction) {
            return ['error' => "You have already submitted a prediction for game $gameNumber. Use the clear button to delete your existing predictions first."];
        }

        // Validate game number (1-12 for Set 1)
        if ($gameNumber < 1 || $gameNumber > 12) {
            return ['error' => 'Invalid game number. Must be between 1 and 12.'];
        }

        // Validate score format
        if (!$this->isValidScore($predictedScore)) {
            return ['error' => 'Invalid score format. Use format like "40-0", "30-15", "AD-40".'];
        }

        // Insert prediction (no overwrite)
        $this->db->query('INSERT INTO game_predictions (user_id, match_id, game_number, predicted_winner, predicted_score) 
                         VALUES (:user_id, :match_id, :game_number, :predicted_winner, :predicted_score)');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':game_number', $gameNumber);
        $this->db->bind(':predicted_winner', $predictedWinner);
        $this->db->bind(':predicted_score', $predictedScore);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Game prediction submitted successfully.'];
        } else {
            return ['error' => 'Failed to submit game prediction.'];
        }
    }

    public function getGamePredictionsForMatch($matchId, $userId = null) {
        $query = 'SELECT gp.*, u.username 
                  FROM game_predictions gp 
                  JOIN users u ON gp.user_id = u.id 
                  WHERE gp.match_id = :match_id';
        
        if ($userId) {
            $query .= ' AND gp.user_id = :user_id';
        }
        
        $query .= ' ORDER BY gp.game_number ASC, gp.created_at DESC';
        
        $this->db->query($query);
        $this->db->bind(':match_id', $matchId);
        
        if ($userId) {
            $this->db->bind(':user_id', $userId);
        }
        
        return $this->db->resultSet();
    }

    public function getGamePredictionsForUser($userId, $matchId = null) {
        $query = 'SELECT gp.*, m.player1_id, m.player2_id, p1.name as player1_name, p2.name as player2_name
                  FROM game_predictions gp 
                  JOIN matches m ON gp.match_id = m.id
                  JOIN players p1 ON m.player1_id = p1.id
                  JOIN players p2 ON m.player2_id = p2.id
                  WHERE gp.user_id = :user_id';
        
        if ($matchId) {
            $query .= ' AND gp.match_id = :match_id';
        }
        
        $query .= ' ORDER BY gp.match_id ASC, gp.game_number ASC';
        
        $this->db->query($query);
        $this->db->bind(':user_id', $userId);
        
        if ($matchId) {
            $this->db->bind(':match_id', $matchId);
        }
        
        return $this->db->resultSet();
    }

    public function addGameResult($matchId, $gameNumber, $winner, $finalScore) {
        $this->db->query('INSERT INTO game_results (match_id, game_number, winner, final_score)
            VALUES (:match_id, :game_number, :winner, :final_score)
            ON DUPLICATE KEY UPDATE
            winner = VALUES(winner),
            final_score = VALUES(final_score)');
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':game_number', $gameNumber);
        $this->db->bind(':winner', $winner);
        $this->db->bind(':final_score', $finalScore);
        $this->db->execute();
    }

    public function getGameResultsForMatch($matchId) {
        $this->db->query('SELECT * FROM game_results WHERE match_id = :match_id ORDER BY game_number ASC');
        $this->db->bind(':match_id', $matchId);
        return $this->db->resultSet();
    }

    public function setSet1Completion($matchId, $winner, $finalGame, $finalScore) {
        $this->db->query('INSERT INTO set1_completion (match_id, winner, final_game, final_score) 
                         VALUES (:match_id, :winner, :final_game, :final_score)
                         ON DUPLICATE KEY UPDATE 
                         winner = VALUES(winner), 
                         final_game = VALUES(final_game), 
                         final_score = VALUES(final_score)');
        
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':winner', $winner);
        $this->db->bind(':final_game', $finalGame);
        $this->db->bind(':final_score', $finalScore);

        return $this->db->execute();
    }

    public function getSet1Completion($matchId) {
        $this->db->query('SELECT * FROM set1_completion WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        return $this->db->single();
    }

    public function calculateGamePoints($matchId) {
        // Get point settings
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        $pointSettings = $this->db->single();
        
        // Get game results for this match
        $gameResults = $this->getGameResultsForMatch($matchId);
        if (empty($gameResults)) {
            return false; // No results to calculate
        }

        // Get all predictions for this match
        $predictions = $this->getGamePredictionsForMatch($matchId);
        
        foreach ($predictions as $prediction) {
            $pointsAwarded = 0;
            $correct = false;
            $oldPoints = isset($prediction['points_awarded']) ? (int)$prediction['points_awarded'] : 0;
            
            // Find corresponding game result
            $gameResult = null;
            foreach ($gameResults as $result) {
                if ($result['game_number'] == $prediction['game_number']) {
                    $gameResult = $result;
                    break;
                }
            }
            
            if ($gameResult) {
                // Check if winner prediction is correct
                if ($prediction['predicted_winner'] == $gameResult['winner']) {
                    $pointsAwarded += $pointSettings['game_winner_points'];
                }
                
                // Check if score prediction is correct
                if ($prediction['predicted_score'] == $gameResult['final_score']) {
                    $pointsAwarded += $pointSettings['exact_game_score_points'];
                    $correct = true;
                } else {
                    // Check if score is close (same winner, different score)
                    if ($prediction['predicted_winner'] == $gameResult['winner']) {
                        $pointsAwarded += $pointSettings['game_score_points'];
                    }
                }
            }
            
            // Update prediction with points
            $this->updateGamePredictionPoints($prediction['id'], $pointsAwarded, $correct);
            
            // Subtract old points, add new points
            $this->updateUserPoints($prediction['user_id'], $pointsAwarded - $oldPoints);
        }
        
        // Apply joker multipliers after regular points calculation
        $this->applyJokerMultipliers($matchId);
        
        return true;
    }

    private function updateGamePredictionPoints($predictionId, $points, $correct) {
        $this->db->query('UPDATE game_predictions SET points_awarded = :points, correct = :correct WHERE id = :id');
        $this->db->bind(':points', $points);
        $this->db->bind(':correct', $correct ? 1 : 0);
        $this->db->bind(':id', $predictionId);
        return $this->db->execute();
    }

    private function updateUserPoints($userId, $points) {
        $this->db->query('UPDATE users SET points = points + :points WHERE id = :user_id');
        $this->db->bind(':points', $points);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
    
    /**
     * Apply joker multipliers to game predictions
     */
    private function applyJokerMultipliers($matchId) {
        require_once 'Joker.php';
        $joker = new Joker();
        $joker->applyJokerMultiplier($matchId);
    }

    private function isValidScore($score) {
        // Valid tennis scores: 0, 15, 30, 40, AD, game
        $validScores = ['0', '15', '30', '40', 'AD', 'game'];
        $parts = explode('-', $score);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        return in_array($parts[0], $validScores) && in_array($parts[1], $validScores);
    }

    public function getGamePredictionStats($userId) {
        $this->db->query('SELECT 
                            COUNT(*) as total_predictions,
                            SUM(correct) as correct_predictions,
                            SUM(points_awarded) as total_points
                          FROM game_predictions 
                          WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $stats = $this->db->single();
        
        $accuracy = $stats['total_predictions'] > 0 ? 
                   round(($stats['correct_predictions'] / $stats['total_predictions']) * 100, 2) : 0;
        
        return [
            'total_predictions' => $stats['total_predictions'] ?? 0,
            'correct_predictions' => $stats['correct_predictions'] ?? 0,
            'total_points' => $stats['total_points'] ?? 0,
            'accuracy' => $accuracy
        ];
    }

    public function deleteGamePredictions($userId, $matchId) {
        // Check if prediction is locked (5 minutes before match)
        $this->db->query('SELECT start_time FROM matches WHERE id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $match = $this->db->single();
        
        if ($match) {
            $startTime = strtotime($match['start_time']);
            if ($startTime - time() <= 300) {
                return ['success' => false, 'message' => 'Predictions are locked for this match.'];
            }
        }

        // Delete all game predictions for this user and match
        $this->db->query('DELETE FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Game predictions deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete game predictions.'];
        }
    }
} 