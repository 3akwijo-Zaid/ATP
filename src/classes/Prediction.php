<?php
require_once 'Database.php';
require_once 'Match.php'; // Renamed to MatchManager, but file is Match.php
require_once 'Admin.php';

class Prediction {
    private $db;
    private $matchManager;
    private $admin;

    public function __construct() {
        $this->db = new Database;
        $this->matchManager = new MatchManager;
        $this->admin = new Admin;
    }

    public function submitPrediction($data) {
        $this->db->query('INSERT INTO predictions (user_id, match_id, prediction_data) VALUES (:user_id, :match_id, :prediction_data)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':match_id', $data['match_id']);
        $this->db->bind(':prediction_data', $data['prediction_data']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPredictionsForUser($userId) {
        $this->db->query('SELECT * FROM predictions WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function getPredictionsForMatch($matchId) {
        $this->db->query('SELECT * FROM predictions WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        return $this->db->resultSet();
    }

    public function calculatePoints($matchId) {
        // 1. Get actual match results and point settings
        $actualMatch = $this->matchManager->getMatchById($matchId);
        $actualSets = $this->matchManager->getMatchSets($matchId);
        $pointSettings = $this->admin->getPointSettings();

        // 2. Get all predictions for the match
        $predictions = $this->getPredictionsForMatch($matchId);

        foreach ($predictions as $prediction) {
            $predictionData = json_decode($prediction['prediction_data'], true);
            $pointsAwarded = 0;

            // 3. Compare match winner
            if ($predictionData['winner'] == $actualMatch['winner']) {
                $pointsAwarded += $pointSettings['match_winner_points'];
            }

            // 4. Compare sets
            foreach ($predictionData['sets'] as $index => $predictedSet) {
                if (!isset($actualSets[$index])) continue;
                $actualSet = $actualSets[$index];

                // Compare set score
                if ($predictedSet['player1_games'] == $actualSet['player1_games'] && $predictedSet['player2_games'] == $actualSet['player2_games']) {
                    $pointsAwarded += $pointSettings['set_score_points'];
                }

                // Compare tiebreak scores (if both predicted and actual have tiebreaks)
                if (isset($predictedSet['player1_tiebreak']) && isset($predictedSet['player2_tiebreak']) && 
                    isset($actualSet['player1_tiebreak_points']) && isset($actualSet['player2_tiebreak_points'])) {
                    if ($predictedSet['player1_tiebreak'] == $actualSet['player1_tiebreak_points'] && 
                        $predictedSet['player2_tiebreak'] == $actualSet['player2_tiebreak_points']) {
                        $pointsAwarded += $pointSettings['set_score_points']; // Bonus points for exact tiebreak
                    }
                }

                // Compare set winner
                $predictedSetWinner = ($predictedSet['player1_games'] > $predictedSet['player2_games']) ? $actualMatch['player1'] : $actualMatch['player2'];
                $actualSetWinner = ($actualSet['player1_games'] > $actualSet['player2_games']) ? $actualMatch['player1'] : $actualMatch['player2'];
                
                if ($predictedSetWinner == $actualSetWinner) {
                    $pointsAwarded += $pointSettings['set_winner_points'];
                }
            }

            // 5. Update points in DB
            $this->updatePredictionPoints($prediction['id'], $pointsAwarded);
            $this->updateUserPoints($prediction['user_id'], $pointsAwarded);
        }
        return true;
    }

    private function updatePredictionPoints($predictionId, $points) {
        $this->db->query('UPDATE predictions SET points_awarded = :points WHERE id = :id');
        $this->db->bind(':points', $points);
        $this->db->bind(':id', $predictionId);
        $this->db->execute();
    }

    private function updateUserPoints($userId, $points) {
        $this->db->query('UPDATE users SET points = points + :points WHERE id = :user_id');
        $this->db->bind(':points', $points);
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }
} 