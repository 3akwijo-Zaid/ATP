<?php
require_once 'Database.php';

class StatisticsPrediction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function submitStatisticsPrediction($userId, $matchId, $playerType, $acesPredicted, $doubleFaultsPredicted) {
        // Check if prediction is locked (1 hour before match)
        $this->db->query('SELECT start_time FROM matches WHERE id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $match = $this->db->single();
        
        if ($match) {
            $startTime = strtotime($match['start_time']);
            if ($startTime - time() <= 3600) {
                return ['error' => 'Predictions are locked for this match.'];
            }
        }

        // Check if user already has a prediction for this player in this match
        $this->db->query('SELECT id FROM statistics_predictions WHERE user_id = :user_id AND match_id = :match_id AND player_type = :player_type');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':player_type', $playerType);
        $existingPrediction = $this->db->single();
        
        if ($existingPrediction) {
            return ['error' => "You have already submitted statistics predictions for this player. Use the clear button to delete your existing predictions first."];
        }

        // Validate player type
        if (!in_array($playerType, ['player1', 'player2'])) {
            return ['error' => 'Invalid player type.'];
        }

        // Validate predictions (non-negative integers)
        if (!is_numeric($acesPredicted) || $acesPredicted < 0 || !is_numeric($doubleFaultsPredicted) || $doubleFaultsPredicted < 0) {
            return ['error' => 'Aces and double faults must be non-negative numbers.'];
        }

        // Insert prediction
        $this->db->query('INSERT INTO statistics_predictions (user_id, match_id, player_type, aces_predicted, double_faults_predicted) 
                         VALUES (:user_id, :match_id, :player_type, :aces_predicted, :double_faults_predicted)');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':player_type', $playerType);
        $this->db->bind(':aces_predicted', intval($acesPredicted));
        $this->db->bind(':double_faults_predicted', intval($doubleFaultsPredicted));

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Statistics prediction submitted successfully.'];
        } else {
            return ['error' => 'Failed to submit statistics prediction.'];
        }
    }

    public function getStatisticsPredictionsForMatch($matchId, $userId = null) {
        $query = 'SELECT sp.*, u.username, p1.name as player1_name, p2.name as player2_name
                  FROM statistics_predictions sp 
                  JOIN users u ON sp.user_id = u.id 
                  JOIN matches m ON sp.match_id = m.id
                  JOIN players p1 ON m.player1_id = p1.id
                  JOIN players p2 ON m.player2_id = p2.id
                  WHERE sp.match_id = :match_id';
        
        if ($userId) {
            $query .= ' AND sp.user_id = :user_id';
        }
        
        $query .= ' ORDER BY sp.player_type ASC, sp.created_at DESC';
        
        $this->db->query($query);
        $this->db->bind(':match_id', $matchId);
        
        if ($userId) {
            $this->db->bind(':user_id', $userId);
        }
        
        return $this->db->resultSet();
    }

    public function getStatisticsPredictionsForUser($userId, $matchId = null) {
        $query = 'SELECT sp.*, m.player1_id, m.player2_id, p1.name as player1_name, p2.name as player2_name
                  FROM statistics_predictions sp 
                  JOIN matches m ON sp.match_id = m.id
                  JOIN players p1 ON m.player1_id = p1.id
                  JOIN players p2 ON m.player2_id = p2.id
                  WHERE sp.user_id = :user_id';
        
        if ($matchId) {
            $query .= ' AND sp.match_id = :match_id';
        }
        
        $query .= ' ORDER BY sp.match_id ASC, sp.player_type ASC';
        
        $this->db->query($query);
        $this->db->bind(':user_id', $userId);
        
        if ($matchId) {
            $this->db->bind(':match_id', $matchId);
        }
        
        return $this->db->resultSet();
    }

    public function addStatisticsResult($matchId, $playerType, $acesActual, $doubleFaultsActual) {
        $this->db->query('INSERT INTO statistics_results (match_id, player_type, aces_actual, double_faults_actual) 
                         VALUES (:match_id, :player_type, :aces_actual, :double_faults_actual)
                         ON DUPLICATE KEY UPDATE 
                         aces_actual = VALUES(aces_actual), 
                         double_faults_actual = VALUES(double_faults_actual)');
        
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':player_type', $playerType);
        $this->db->bind(':aces_actual', intval($acesActual));
        $this->db->bind(':double_faults_actual', intval($doubleFaultsActual));

        return $this->db->execute();
    }

    public function getStatisticsResultsForMatch($matchId) {
        $this->db->query('SELECT * FROM statistics_results WHERE match_id = :match_id ORDER BY player_type ASC');
        $this->db->bind(':match_id', $matchId);
        return $this->db->resultSet();
    }

    public function calculateStatisticsPoints($matchId) {
        // Get point settings
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        $pointSettings = $this->db->single();
        
        // Get statistics results for this match
        $statisticsResults = $this->getStatisticsResultsForMatch($matchId);
        if (empty($statisticsResults)) {
            return false; // No results to calculate
        }

        // Get all predictions for this match
        $predictions = $this->getStatisticsPredictionsForMatch($matchId);
        
        foreach ($predictions as $prediction) {
            $pointsAwarded = 0;
            $correct = false;
            
            // Find corresponding result
            $result = null;
            foreach ($statisticsResults as $res) {
                if ($res['player_type'] == $prediction['player_type']) {
                    $result = $res;
                    break;
                }
            }
            
            if ($result) {
                // Check aces prediction
                $acesDiff = abs($prediction['aces_predicted'] - $result['aces_actual']);
                if ($acesDiff == 0) {
                    $pointsAwarded += $pointSettings['aces_exact_points'];
                    $correct = true;
                } elseif ($acesDiff <= 2) {
                    $pointsAwarded += $pointSettings['aces_close_points'];
                }
                
                // Check double faults prediction
                $doubleFaultsDiff = abs($prediction['double_faults_predicted'] - $result['double_faults_actual']);
                if ($doubleFaultsDiff == 0) {
                    $pointsAwarded += $pointSettings['double_faults_exact_points'];
                    $correct = true;
                } elseif ($doubleFaultsDiff <= 2) {
                    $pointsAwarded += $pointSettings['double_faults_close_points'];
                }
            }
            
            // Update prediction with points
            $this->updateStatisticsPredictionPoints($prediction['id'], $pointsAwarded, $correct);
            
            // Update user points
            $this->updateUserPoints($prediction['user_id'], $pointsAwarded);
        }
        
        return true;
    }

    private function updateStatisticsPredictionPoints($predictionId, $points, $correct) {
        $this->db->query('UPDATE statistics_predictions SET points_awarded = :points, correct = :correct WHERE id = :id');
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

    public function getStatisticsPredictionStats($userId) {
        $this->db->query('SELECT 
                            COUNT(*) as total_predictions,
                            SUM(correct) as correct_predictions,
                            SUM(points_awarded) as total_points
                          FROM statistics_predictions 
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

    public function deleteStatisticsPredictions($userId, $matchId) {
        // Check if prediction is locked (1 hour before match)
        $this->db->query('SELECT start_time FROM matches WHERE id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $match = $this->db->single();
        
        if ($match) {
            $startTime = strtotime($match['start_time']);
            if ($startTime - time() <= 3600) {
                return ['success' => false, 'message' => 'Predictions are locked for this match.'];
            }
        }

        // Delete all statistics predictions for this user and match
        $this->db->query('DELETE FROM statistics_predictions WHERE user_id = :user_id AND match_id = :match_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Statistics predictions deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete statistics predictions.'];
        }
    }
} 