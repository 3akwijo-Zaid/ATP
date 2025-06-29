    /**
     * Compare tiebreaks for a set
     */
    private function tiebreakCorrect($predSet, $actualSet) {
        if (!isset($predSet['tiebreak']) || !isset($actualSet['tiebreak'])) return false;
        if (!is_array($predSet['tiebreak']) || !is_array($actualSet['tiebreak'])) return false;
        return (
            (isset($predSet['tiebreak']['player1']) && isset($actualSet['tiebreak']['player1']) && $predSet['tiebreak']['player1'] !== '' && $actualSet['tiebreak']['player1'] !== '' && intval($predSet['tiebreak']['player1']) === intval($actualSet['tiebreak']['player1'])) &&
            (isset($predSet['tiebreak']['player2']) && isset($actualSet['tiebreak']['player2']) && $predSet['tiebreak']['player2'] !== '' && $actualSet['tiebreak']['player2'] !== '' && intval($predSet['tiebreak']['player2']) === intval($actualSet['tiebreak']['player2']))
        );
    }

    /**
     * Calculate points for a user's prediction for a match, including tiebreaks
     */
    public function calculatePoints($matchId) {
        // Get all predictions for this match
        $this->db->query('SELECT * FROM predictions WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $predictions = $this->db->resultSet();
        if (!$predictions) return false;

        // Get actual result sets
        $this->db->query('SELECT * FROM match_sets WHERE match_id = :match_id ORDER BY set_number ASC');
        $this->db->bind(':match_id', $matchId);
        $actualSets = $this->db->resultSet();
        if (!$actualSets) return false;

        // Get point settings
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        $settings = $this->db->single();
        if (!$settings) return false;

        foreach ($predictions as $prediction) {
            $data = json_decode($prediction['prediction_data'], true);
            $points = 0;
            // Winner points
            if (isset($data['winner']) && $prediction['match_id']) {
                $this->db->query('SELECT winner_id, player1_id, player2_id FROM matches WHERE id = :match_id');
                $this->db->bind(':match_id', $prediction['match_id']);
                $match = $this->db->single();
                if ($match) {
                    $predWinner = $data['winner'] === 'player1' ? $match['player1_id'] : $match['player2_id'];
                    if ($match['winner_id'] && $predWinner == $match['winner_id']) {
                        $points += intval($settings['match_winner_points']);
                    }
                }
            }
            // Set and tiebreak points
            if (isset($data['sets']) && is_array($data['sets'])) {
                foreach ($data['sets'] as $i => $predSet) {
                    if (!isset($actualSets[$i])) continue;
                    $actualSet = $actualSets[$i];
                    // Set winner
                    if (
                        (isset($predSet['player1']) && isset($predSet['player2'])) &&
                        ((intval($predSet['player1']) > intval($predSet['player2']) && intval($actualSet['player1_games']) > intval($actualSet['player2_games'])) ||
                         (intval($predSet['player2']) > intval($predSet['player1']) && intval($actualSet['player2_games']) > intval($actualSet['player1_games'])))
                    ) {
                        $points += intval($settings['set_winner_points']);
                    }
                    // Set score
                    if (
                        isset($predSet['player1']) && isset($predSet['player2']) &&
                        intval($predSet['player1']) === intval($actualSet['player1_games']) &&
                        intval($predSet['player2']) === intval($actualSet['player2_games'])
                    ) {
                        $points += intval($settings['set_score_points']);
                    }
                    // Tiebreak score
                    if (
                        $this->tiebreakCorrect($predSet, [
                            'tiebreak' => [
                                'player1' => $actualSet['player1_tiebreak_points'],
                                'player2' => $actualSet['player2_tiebreak_points']
                            ]
                        ])
                    ) {
                        $points += intval($settings['tiebreak_score_points'] ?? 0);
                    }
                }
            }
            // TODO: Add game-level and set1-complete points if needed
            // Update prediction points
            $this->db->query('UPDATE predictions SET points_awarded = :points WHERE id = :id');
            $this->db->bind(':points', $points);
            $this->db->bind(':id', $prediction['id']);
            $this->db->execute();
        }
        return true;
    }
<?php
require_once 'Database.php';

class Prediction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function submit($userId, $matchId, $winner, $sets) {
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

        // Check if user already has a prediction for this match
        $this->db->query('SELECT id FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $existingPrediction = $this->db->single();
        
        if ($existingPrediction) {
            return ['success' => false, 'message' => 'You have already submitted a prediction for this match. Use the clear button to delete your existing prediction first.'];
        }

        // Validate winner
        if (!in_array($winner, ['player1', 'player2'])) {
            return ['success' => false, 'message' => 'Invalid winner selection.'];
        }

        // Validate sets array
        if (!is_array($sets) || empty($sets)) {
            return ['success' => false, 'message' => 'Sets data is required.'];
        }

        // Create prediction data
        $predictionData = [
            'winner' => $winner,
            'sets' => $sets,
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        // Insert prediction (no overwrite)
        $this->db->query('INSERT INTO predictions (user_id, match_id, prediction_data, created_at) 
                         VALUES (:user_id, :match_id, :prediction_data, :created_at)');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':prediction_data', json_encode($predictionData));
        $this->db->bind(':created_at', date('Y-m-d H:i:s'));

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Prediction submitted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to submit prediction.'];
        }
    }

    public function getUserPrediction($userId, $matchId) {
        $this->db->query('SELECT * FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        
        $prediction = $this->db->single();
        
        if ($prediction) {
            $prediction['prediction_data'] = json_decode($prediction['prediction_data'], true);
        }
        
        return $prediction;
    }

    public function getMatchPredictions($matchId) {
        $this->db->query('SELECT p.*, u.username, u.avatar 
                         FROM predictions p 
                         JOIN users u ON p.user_id = u.id 
                         WHERE p.match_id = :match_id 
                         ORDER BY p.created_at DESC');
        $this->db->bind(':match_id', $matchId);
        
        $predictions = $this->db->resultSet();
        
        foreach ($predictions as &$prediction) {
            $prediction['prediction_data'] = json_decode($prediction['prediction_data'], true);
        }
        
        return $predictions;
    }

    public function getUserPredictions($userId) {
        $this->db->query('SELECT p.*, m.player1_id, m.player2_id, m.start_time, m.status,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name
                         FROM predictions p 
                         JOIN matches m ON p.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON m.tournament_id = t.id
                         WHERE p.user_id = :user_id 
                         ORDER BY m.start_time DESC');
        $this->db->bind(':user_id', $userId);
        
        $predictions = $this->db->resultSet();
        
        foreach ($predictions as &$prediction) {
            $prediction['prediction_data'] = json_decode($prediction['prediction_data'], true);
        }
        
        return $predictions;
    }

    public function calculatePoints($matchId) {
        // Get point settings
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        $pointSettings = $this->db->single();
        
        // Get match result
        $this->db->query('SELECT m.*, p1.name as player1_name, p2.name as player2_name,
                         ms.set_number, ms.player1_games, ms.player2_games
                         FROM matches m
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         LEFT JOIN match_sets ms ON m.id = ms.match_id
                         WHERE m.id = :match_id AND m.status = "finished"
                         ORDER BY ms.set_number ASC');
        $this->db->bind(':match_id', $matchId);
        $matchData = $this->db->resultSet();
        
        if (empty($matchData)) {
            return false; // Match not finished
        }
        
        $match = $matchData[0];
        $sets = [];
        foreach ($matchData as $row) {
            if (isset($row['set_number'])) {
                $sets[] = [
                    'set_number' => $row['set_number'],
                    'player1_games' => $row['player1_games'],
                    'player2_games' => $row['player2_games']
                ];
            }
        }
        
        // Get all predictions for this match
        $predictions = $this->getMatchPredictions($matchId);
        
        foreach ($predictions as $prediction) {
            $pointsAwarded = 0;
            $correct = false;
            
            $predData = $prediction['prediction_data'];
            
            // Check match winner
            $actualWinner = ($match['winner_id'] == $match['player1_id']) ? 'player1' : 'player2';
            if ($predData['winner'] == $actualWinner) {
                $pointsAwarded += $pointSettings['match_winner_points'];
                $correct = true;
            }
            
            // Check set predictions
            if (isset($predData['sets']) && is_array($predData['sets'])) {
                foreach ($predData['sets'] as $setIndex => $predictedSet) {
                    if (isset($sets[$setIndex])) {
                        $actualSet = $sets[$setIndex];
                        
                        // Check set winner
                        $actualSetWinner = ($actualSet['player1_games'] > $actualSet['player2_games']) ? 'player1' : 'player2';
                        $predictedSetWinner = ($predictedSet['player1'] > $predictedSet['player2']) ? 'player1' : 'player2';
                        
                        if ($actualSetWinner == $predictedSetWinner) {
                            $pointsAwarded += $pointSettings['set_winner_points'];
                        }
                        
                        // Check exact set score
                        if ($actualSet['player1_games'] == $predictedSet['player1'] && 
                            $actualSet['player2_games'] == $predictedSet['player2']) {
                            $pointsAwarded += $pointSettings['set_score_points'];
                        }
                    }
                }
            }
            
            // Update prediction with points
            $this->updatePredictionPoints($prediction['id'], $pointsAwarded, $correct);
            
            // Update user points
            $this->updateUserPoints($prediction['user_id'], $pointsAwarded);
        }
        
        return true;
    }

    private function updatePredictionPoints($predictionId, $points, $correct) {
        $this->db->query('UPDATE predictions SET points_earned = :points, correct = :correct WHERE id = :id');
        $this->db->bind(':points', $points);
        $this->db->bind(':correct', $correct ? 1 : 0);
        $this->db->bind(':id', $predictionId);
        return $this->db->execute();
    }

    private function updateUserPoints($userId, $points) {
        $this->db->query('UPDATE users SET points = points + :points WHERE id = :id');
        $this->db->bind(':points', $points);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function getPredictionStats($userId) {
        $this->db->query('SELECT 
                         COUNT(*) as total_predictions,
                         SUM(correct) as correct_predictions,
                         SUM(points_earned) as total_points
                         FROM predictions 
                         WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        
        $stats = $this->db->single();
        
        if ($stats['total_predictions'] > 0) {
            $stats['accuracy'] = round(($stats['correct_predictions'] / $stats['total_predictions']) * 100, 1);
        } else {
            $stats['accuracy'] = 0;
        }
        
        return $stats;
    }

    public function deletePrediction($userId, $matchId) {
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

        // Delete the prediction
        $this->db->query('DELETE FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Prediction deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete prediction.'];
        }
    }
} 