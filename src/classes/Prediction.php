<?php
require_once 'Database.php';

class Prediction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Compare tiebreaks for a set
     */
    private function tiebreakCorrect($predSet, $actualSet) {
        // Defensive: If either tiebreak is missing or not an array, it's not correct
        if (!isset($predSet['tiebreak']) || !isset($actualSet['tiebreak'])) return false;
        if (!is_array($predSet['tiebreak']) || !is_array($actualSet['tiebreak'])) return false;
        // Defensive: If both tiebreak arrays are empty, treat as not correct
        if (empty($predSet['tiebreak']) && empty($actualSet['tiebreak'])) return false;
        // Defensive: If either tiebreak value is not numeric, treat as not correct
        foreach (['player1', 'player2'] as $key) {
            if (
                !isset($predSet['tiebreak'][$key]) || !isset($actualSet['tiebreak'][$key]) ||
                $predSet['tiebreak'][$key] === '' || $actualSet['tiebreak'][$key] === '' ||
                !is_numeric($predSet['tiebreak'][$key]) || !is_numeric($actualSet['tiebreak'][$key])
            ) {
                return false;
            }
        }
        return (
            intval($predSet['tiebreak']['player1']) === intval($actualSet['tiebreak']['player1']) &&
            intval($predSet['tiebreak']['player2']) === intval($actualSet['tiebreak']['player2'])
        );
    }

    /**
     * Calculate points for a user's prediction for a match, including tiebreaks
     */
    public function calculatePoints($matchId) {
        // Check if match is retired
        $this->db->query('SELECT status FROM matches WHERE id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $match = $this->db->single();
        $isRetired = false;
        if ($match && isset($match['status']) && 
            ($match['status'] === 'retired_player1' || $match['status'] === 'retired_player2')) {
            $isRetired = true;
        }
        // Get all predictions for this match
        $this->db->query('SELECT * FROM predictions WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $predictions = $this->db->resultSet();
        if (!$predictions) return false;

        // Get actual result sets
        $this->db->query('SELECT * FROM match_sets WHERE match_id = :match_id ORDER BY set_number ASC');
        $this->db->bind(':match_id', $matchId);
        $actualSets = $this->db->resultSet();
        
        // For retired matches, we can still calculate winner points even without sets
        if (!$actualSets && !$isRetired) {
            return false;
        }
        
        $numSetsToCompare = $actualSets ? count($actualSets) : 0;
        // For retired matches, skip the last set for points calculation
        if ($isRetired && $numSetsToCompare > 1) {
            $numSetsToCompare--;
        }

        // Get point settings
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        $settings = $this->db->single();
        if (!$settings) return false;

        foreach ($predictions as $prediction) {
            $data = json_decode($prediction['prediction_data'], true);
            $points = 0;
            $oldPoints = isset($prediction['points_awarded']) ? (int)$prediction['points_awarded'] : 0;
            
            // Always award winner points, even for retired matches
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
            
            // Set and tiebreak points (only if we have actual sets)
            if ($actualSets && isset($data['sets']) && is_array($data['sets'])) {
                // Only compare up to the number of sets actually played (for retired matches)
                $predSetScores = [];
                $actualSetScores = [];
                $predSetScoresWithTiebreaks = [];
                $actualSetScoresWithTiebreaks = [];
                // Prepare predicted sets with tiebreaks
                for ($i = 0; $i < $numSetsToCompare; $i++) {
                    if (!isset($data['sets'][$i])) break;
                    $predSet = $data['sets'][$i];
                    $predSetScores[] = [
                        'player1' => intval($predSet['player1']),
                        'player2' => intval($predSet['player2'])
                    ];
                    $predSetWithTiebreak = [
                        'player1' => intval($predSet['player1']),
                        'player2' => intval($predSet['player2'])
                    ];
                    if (isset($predSet['tiebreak']) && is_array($predSet['tiebreak']) && 
                        isset($predSet['tiebreak']['player1']) && isset($predSet['tiebreak']['player2']) &&
                        $predSet['tiebreak']['player1'] !== '' && $predSet['tiebreak']['player2'] !== '') {
                        $predSetWithTiebreak['tiebreak'] = [
                            'player1' => intval($predSet['tiebreak']['player1']),
                            'player2' => intval($predSet['tiebreak']['player2'])
                        ];
                    }
                    $predSetScoresWithTiebreaks[] = $predSetWithTiebreak;
                }
                // Prepare actual sets with tiebreaks
                for ($i = 0; $i < $numSetsToCompare; $i++) {
                    $actualSet = $actualSets[$i];
                    $actualSetScores[] = [
                        'player1' => intval($actualSet['player1_games']),
                        'player2' => intval($actualSet['player2_games'])
                    ];
                    $actualSetWithTiebreak = [
                        'player1' => intval($actualSet['player1_games']),
                        'player2' => intval($actualSet['player2_games'])
                    ];
                    if (isset($actualSet['player1_tiebreak_points']) && isset($actualSet['player2_tiebreak_points']) &&
                        $actualSet['player1_tiebreak_points'] !== '' && $actualSet['player2_tiebreak_points'] !== '') {
                        $actualSetWithTiebreak['tiebreak'] = [
                            'player1' => intval($actualSet['player1_tiebreak_points']),
                            'player2' => intval($actualSet['player2_tiebreak_points'])
                        ];
                    }
                    $actualSetScoresWithTiebreaks[] = $actualSetWithTiebreak;
                }
                // Count set score points with order sensitivity
                $setScorePoints = 0;
                $tiebreakPoints = 0;
                for ($i = 0; $i < $numSetsToCompare; $i++) {
                    if (!isset($predSetScoresWithTiebreaks[$i]) || !isset($actualSetScoresWithTiebreaks[$i])) {
                        continue;
                    }
                    $predSet = $predSetScoresWithTiebreaks[$i];
                    $actualSet = $actualSetScoresWithTiebreaks[$i];
                    if ($predSet['player1'] === $actualSet['player1'] && $predSet['player2'] === $actualSet['player2']) {
                        $setScorePoints += intval($settings['set_score_points']);
                        if ($this->tiebreakCorrect($predSet, $actualSet)) {
                            $tiebreakPoints += intval($settings['tiebreak_score_points'] ?? 0);
                        }
                    }
                }
                $points += $setScorePoints + $tiebreakPoints;
                // For retired matches, do not award match_score_points (full match prediction)
                // Only award match_score_points for non-retired matches
                if (!$isRetired && count($predSetScores) === $numSetsToCompare) {
                    $allMatch = true;
                    for ($i = 0; $i < $numSetsToCompare; $i++) {
                        if ($predSetScores[$i]['player1'] !== $actualSetScores[$i]['player1'] ||
                            $predSetScores[$i]['player2'] !== $actualSetScores[$i]['player2']) {
                            $allMatch = false;
                            break;
                        }
                    }
                    if ($allMatch) {
                        $points += intval($settings['match_score_points']);
                    }
                }
            }
    
            // Update prediction points
            $this->db->query('UPDATE predictions SET points_awarded = :points WHERE id = :id');
            $this->db->bind(':points', $points);
            $this->db->bind(':id', $prediction['id']);
            $this->db->execute();
            
            // Update user points: subtract old points, add new points
            $this->db->query('UPDATE users SET points = points - :old_points + :new_points WHERE id = :user_id');
            $this->db->bind(':old_points', $oldPoints);
            $this->db->bind(':new_points', $points);
            $this->db->bind(':user_id', $prediction['user_id']);
            $this->db->execute();
        }
        
        // Apply joker multipliers after regular points calculation
        $this->applyJokerMultipliers($matchId);
        
        return true;
    }
    
    /**
     * Apply joker multipliers to match predictions
     */
    private function applyJokerMultipliers($matchId) {
        require_once 'Joker.php';
        $joker = new Joker();
        $joker->applyJokerMultiplier($matchId);
    }

    public function submit($userId, $matchId, $winner, $sets) {
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
            'submitted_at' => (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i:s')
        ];

        // Insert prediction (no overwrite)
        $this->db->query('INSERT INTO predictions (user_id, match_id, prediction_data, created_at) 
                         VALUES (:user_id, :match_id, :prediction_data, :created_at)');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':match_id', $matchId);
        $this->db->bind(':prediction_data', json_encode($predictionData));
        $this->db->bind(':created_at', (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i:s'));

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
    /**
     * Get recent prediction activity for a user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentActivity($userId, $limit = 10) {
        // Get match predictions with full details
        $this->db->query('SELECT p.*, m.start_time, m.status, m.player1_id, m.player2_id,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name, t.logo as tournament_logo
                         FROM predictions p 
                         JOIN matches m ON p.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON m.tournament_id = t.id
                         WHERE p.user_id = :user_id 
                         ORDER BY p.created_at DESC 
                         LIMIT :limit');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $matchPredictions = $this->db->resultSet();
        
        // Decode prediction data for each match prediction
        foreach ($matchPredictions as &$prediction) {
            $prediction['prediction_data'] = json_decode($prediction['prediction_data'], true);
            $prediction['type'] = 'match_prediction';
        }
        
        // Get game predictions with full details
        $this->db->query('SELECT gp.*, m.start_time, m.status, m.player1_id, m.player2_id,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name, t.logo as tournament_logo
                         FROM game_predictions gp 
                         JOIN matches m ON gp.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON m.tournament_id = t.id
                         WHERE gp.user_id = :user_id 
                         ORDER BY gp.created_at DESC 
                         LIMIT :limit');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $gamePredictions = $this->db->resultSet();
        
        // Add type to game predictions
        foreach ($gamePredictions as &$prediction) {
            $prediction['type'] = 'game_prediction';
        }
        
        // Get statistics predictions with full details
        $this->db->query('SELECT sp.*, m.start_time, m.status, m.player1_id, m.player2_id,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name, t.logo as tournament_logo
                         FROM statistics_predictions sp 
                         JOIN matches m ON sp.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON m.tournament_id = t.id
                         WHERE sp.user_id = :user_id 
                         ORDER BY sp.created_at DESC 
                         LIMIT :limit');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $statisticsPredictions = $this->db->resultSet();
        
        // Add type to statistics predictions
        foreach ($statisticsPredictions as &$prediction) {
            $prediction['type'] = 'statistics_prediction';
        }
        
        // Combine all predictions and sort by creation date
        $allActivity = array_merge($matchPredictions, $gamePredictions, $statisticsPredictions);
        
        // Sort by created_at descending
        usort($allActivity, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Return only the requested limit
        return array_slice($allActivity, 0, $limit);
    }

    /**
     * Grade all predictions for a match and update the 'correct' column
     * @param int $matchId
     * @param string $actualWinner ('player1' or 'player2')
     */
    public function gradeMatchPredictions($matchId, $actualWinner)
    {
        // Get all predictions for this match
        $this->db->query('SELECT id, prediction_data FROM predictions WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $predictions = $this->db->resultSet();

        foreach ($predictions as $prediction) {
            $data = json_decode($prediction['prediction_data'], true);
            $predictedWinner = $data['winner'] ?? null;
            $isCorrect = ($predictedWinner && $predictedWinner == $actualWinner) ? 1 : 0;

            $this->db->query('UPDATE predictions SET correct = :correct WHERE id = :id');
            $this->db->bind(':correct', $isCorrect);
            $this->db->bind(':id', $prediction['id']);
            $this->db->execute();
        }
    }
}