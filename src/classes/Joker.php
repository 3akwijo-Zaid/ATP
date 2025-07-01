<?php
require_once 'Database.php';

class Joker {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Set a joker prediction for a user in a specific tournament round
     */
    public function setJoker($userId, $tournamentId, $round, $matchId, $predictionType) {
        // Check if user already has a joker for this tournament round
        $this->db->query('SELECT id FROM joker_predictions WHERE user_id = :user_id AND tournament_id = :tournament_id AND round = :round');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':tournament_id', $tournamentId);
        $this->db->bind(':round', $round);
        $existingJoker = $this->db->single();
        
        if ($existingJoker) {
            // Update existing joker
            $this->db->query('UPDATE joker_predictions SET match_id = :match_id, prediction_type = :prediction_type WHERE id = :id');
            $this->db->bind(':match_id', $matchId);
            $this->db->bind(':prediction_type', $predictionType);
            $this->db->bind(':id', $existingJoker['id']);
        } else {
            // Create new joker
            $this->db->query('INSERT INTO joker_predictions (user_id, tournament_id, round, match_id, prediction_type) VALUES (:user_id, :tournament_id, :round, :match_id, :prediction_type)');
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':tournament_id', $tournamentId);
            $this->db->bind(':round', $round);
            $this->db->bind(':match_id', $matchId);
            $this->db->bind(':prediction_type', $predictionType);
        }
        
        return $this->db->execute();
    }

    /**
     * Get joker prediction for a user in a specific tournament round
     */
    public function getJoker($userId, $tournamentId, $round) {
        $this->db->query('SELECT j.*, m.player1_id, m.player2_id, m.start_time, m.status,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name
                         FROM joker_predictions j
                         JOIN matches m ON j.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON j.tournament_id = t.id
                         WHERE j.user_id = :user_id AND j.tournament_id = :tournament_id AND j.round = :round');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':tournament_id', $tournamentId);
        $this->db->bind(':round', $round);
        return $this->db->single();
    }

    /**
     * Get all joker predictions for a user
     */
    public function getUserJokers($userId) {
        $this->db->query('SELECT j.*, m.player1_id, m.player2_id, m.start_time, m.status,
                         p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name
                         FROM joker_predictions j
                         JOIN matches m ON j.match_id = m.id
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON j.tournament_id = t.id
                         WHERE j.user_id = :user_id
                         ORDER BY m.start_time DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Check if a user has used their joker for a specific tournament round
     */
    public function hasJokerUsed($userId, $tournamentId, $round) {
        $this->db->query('SELECT id FROM joker_predictions WHERE user_id = :user_id AND tournament_id = :tournament_id AND round = :round');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':tournament_id', $tournamentId);
        $this->db->bind(':round', $round);
        return $this->db->single() !== false;
    }

    /**
     * Get available matches for joker selection in a specific tournament round
     */
    public function getAvailableMatches($tournamentId, $round) {
        $this->db->query('SELECT m.*, p1.name as player1_name, p2.name as player2_name,
                         t.name as tournament_name
                         FROM matches m
                         JOIN players p1 ON m.player1_id = p1.id
                         JOIN players p2 ON m.player2_id = p2.id
                         JOIN tournaments t ON m.tournament_id = t.id
                         WHERE m.tournament_id = :tournament_id AND m.round = :round
                         ORDER BY m.start_time ASC');
        $this->db->bind(':tournament_id', $tournamentId);
        $this->db->bind(':round', $round);
        return $this->db->resultSet();
    }

    /**
     * Apply joker multiplier to points calculation
     * This method should be called after regular points calculation
     */
    public function applyJokerMultiplier($matchId) {
        // Get all joker predictions for this match
        $this->db->query('SELECT * FROM joker_predictions WHERE match_id = :match_id');
        $this->db->bind(':match_id', $matchId);
        $jokers = $this->db->resultSet();
        
        foreach ($jokers as $joker) {
            $userId = $joker['user_id'];
            $predictionType = $joker['prediction_type'];
            
            // Get the points already awarded for this prediction type and match
            $points = $this->getPointsForPredictionType($userId, $matchId, $predictionType);
            
            if ($points > 0) {
                // Double the points (joker effect): add the same amount again
                $this->updateUserPoints($userId, $points);
            }
        }
    }

    /**
     * Get points for a specific prediction type and match
     * Returns the actual points_awarded (not recalculated)
     */
    private function getPointsForPredictionType($userId, $matchId, $predictionType) {
        switch ($predictionType) {
            case 'match':
                $this->db->query('SELECT points_awarded FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':match_id', $matchId);
                $result = $this->db->single();
                return $result ? (int)$result['points_awarded'] : 0;
            case 'game':
                $this->db->query('SELECT SUM(points_awarded) as total_points FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':match_id', $matchId);
                $result = $this->db->single();
                return $result && isset($result['total_points']) ? (int)$result['total_points'] : 0;
            case 'statistics':
                $this->db->query('SELECT SUM(points_awarded) as total_points FROM statistics_predictions WHERE user_id = :user_id AND match_id = :match_id');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':match_id', $matchId);
                $result = $this->db->single();
                return $result && isset($result['total_points']) ? (int)$result['total_points'] : 0;
            default:
                return 0;
        }
    }

    /**
     * Update user points
     */
    private function updateUserPoints($userId, $points) {
        $this->db->query('UPDATE users SET points = points + :points WHERE id = :user_id');
        $this->db->bind(':points', $points);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Remove joker prediction
     */
    public function removeJoker($userId, $tournamentId, $round) {
        $this->db->query('DELETE FROM joker_predictions WHERE user_id = :user_id AND tournament_id = :tournament_id AND round = :round');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':tournament_id', $tournamentId);
        $this->db->bind(':round', $round);
        return $this->db->execute();
    }
} 