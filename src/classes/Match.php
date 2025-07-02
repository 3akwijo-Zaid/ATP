<?php
require_once 'Database.php';

class MatchManager {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createMatch($data) {
        $this->db->query('INSERT INTO matches (tournament_id, round, player1_id, player2_id, start_time, match_format, featured, game_predictions_enabled, statistics_predictions_enabled) VALUES (:tournament_id, :round, :player1_id, :player2_id, :start_time, :match_format, :featured, :game_predictions_enabled, :statistics_predictions_enabled)');
        $this->db->bind(':tournament_id', $data['tournament_id']);
        $this->db->bind(':round', $data['round']);
        $this->db->bind(':player1_id', $data['player1_id']);
        $this->db->bind(':player2_id', $data['player2_id']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':match_format', $data['match_format']);
        $this->db->bind(':featured', isset($data['featured']) ? $data['featured'] : 0);
        $this->db->bind(':game_predictions_enabled', isset($data['game_predictions_enabled']) ? $data['game_predictions_enabled'] : 1);
        $this->db->bind(':statistics_predictions_enabled', isset($data['statistics_predictions_enabled']) ? $data['statistics_predictions_enabled'] : 1);
        return $this->db->execute();
    }

    public function getMatches() {
        $this->db->query('SELECT m.*, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
            JOIN players p1 ON m.player1_id = p1.id 
            JOIN players p2 ON m.player2_id = p2.id 
            ORDER BY m.start_time DESC');
        return $this->db->resultSet();
    }

    public function getMatchById($id) {
        $this->db->query('SELECT m.*, t.name AS tournament_name, t.logo AS tournament_logo, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
            JOIN tournaments t ON m.tournament_id = t.id 
            JOIN players p1 ON m.player1_id = p1.id 
            JOIN players p2 ON m.player2_id = p2.id 
            WHERE m.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateMatchResult($data) {
        // Validate winner: must be a valid player ID or NULL
        $winner = $data['winner'];
        if (empty($winner) || !is_numeric($winner)) {
            $winner = null;
        } else {
            // Check if player exists
            $this->db->query('SELECT id FROM players WHERE id = :id');
            $this->db->bind(':id', $winner);
            $player = $this->db->single();
            if (!$player) {
                $winner = null;
            }
        }
        $this->db->query('UPDATE matches SET status = :status, winner_id = :winner, result_summary = :result_summary WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':winner', $winner);
        $this->db->bind(':result_summary', $data['result_summary']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateMatchWithResults($data) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Update match basic info
            $this->db->query('UPDATE matches SET status = :status, winner_id = :winner_id WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':winner_id', $data['winner_id'] ?: null);
            
            if (!$this->db->execute()) {
                throw new Exception('Failed to update match');
            }
            
            // Clear existing set results
            $this->db->query('DELETE FROM match_sets WHERE match_id = :match_id');
            $this->db->bind(':match_id', $data['id']);
            $this->db->execute();
            
            // Add new set results
            if (isset($data['sets']) && is_array($data['sets'])) {
                foreach ($data['sets'] as $set) {
                    $this->db->query('INSERT INTO match_sets (match_id, set_number, player1_games, player2_games) VALUES (:match_id, :set_number, :player1_games, :player2_games)');
                    $this->db->bind(':match_id', $data['id']);
                    $this->db->bind(':set_number', $set['set_number']);
                    $this->db->bind(':player1_games', $set['player1_games']);
                    $this->db->bind(':player2_games', $set['player2_games']);
                    
                    if (!$this->db->execute()) {
                        throw new Exception('Failed to add set result');
                    }
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            return ['success' => true, 'message' => 'Match results updated successfully'];
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return ['success' => false, 'message' => 'Error updating match results: ' . $e->getMessage()];
        }
    }

    public function getMatchSets($matchId) {
        $this->db->query('SELECT * FROM match_sets WHERE match_id = :match_id ORDER BY set_number ASC');
        $this->db->bind(':match_id', $matchId);
        return $this->db->resultSet();
    }

    public function addMatchSet($data) {
        $this->db->query('INSERT INTO match_sets (match_id, set_number, player1_games, player2_games, player1_tiebreak_points, player2_tiebreak_points) VALUES (:match_id, :set_number, :player1_games, :player2_games, :player1_tiebreak, :player2_tiebreak)');
        $this->db->bind(':match_id', $data['match_id']);
        $this->db->bind(':set_number', $data['set_number']);
        $this->db->bind(':player1_games', $data['player1_games']);
        $this->db->bind(':player2_games', $data['player2_games']);
        $this->db->bind(':player1_tiebreak', $data['player1_tiebreak']);
        $this->db->bind(':player2_tiebreak', $data['player2_tiebreak']);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function setFeatured($matchId, $featured) {
        $this->db->query('UPDATE matches SET featured = :featured WHERE id = :id');
        $this->db->bind(':featured', $featured);
        $this->db->bind(':id', $matchId);
        return $this->db->execute();
    }

    public function getFeaturedMatches() {
        $this->db->query('SELECT m.*, t.name AS competition_name, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
            JOIN tournaments t ON m.tournament_id = t.id 
            JOIN players p1 ON m.player1_id = p1.id 
            JOIN players p2 ON m.player2_id = p2.id 
            WHERE m.featured = 1 ORDER BY m.start_time ASC');
        return $this->db->resultSet();
    }

    public function getMatchesGroupedByDay($tournamentId = null) {
        $sql = 'SELECT m.*, t.name AS tournament_name, t.logo AS tournament_logo, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
                JOIN tournaments t ON m.tournament_id = t.id 
                JOIN players p1 ON m.player1_id = p1.id 
                JOIN players p2 ON m.player2_id = p2.id';
        $params = [];
        if ($tournamentId) {
            $sql .= ' WHERE m.tournament_id = :tid';
            $params[':tid'] = $tournamentId;
        }
        $sql .= ' ORDER BY m.start_time DESC';
        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        $matches = $this->db->resultSet();
        $grouped = [];
        foreach ($matches as $match) {
            $date = substr($match['start_time'], 0, 10);
            if (!isset($grouped[$date])) $grouped[$date] = [];
            $grouped[$date][] = $match;
        }
        $result = [];
        foreach ($grouped as $date => $matches) {
            $result[] = ['date' => $date, 'matches' => $matches];
        }
        return $result;
    }

    public function getUpcomingMatches($tournamentId = null) {
        $sql = 'SELECT m.*, t.name AS competition_name, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
                JOIN tournaments t ON m.tournament_id = t.id 
                JOIN players p1 ON m.player1_id = p1.id 
                JOIN players p2 ON m.player2_id = p2.id';
        $params = [];
        if ($tournamentId) {
            $sql .= ' WHERE m.tournament_id = :tid';
            $params[':tid'] = $tournamentId;
        }
        $sql .= ' ORDER BY m.start_time ASC';
        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }
} 