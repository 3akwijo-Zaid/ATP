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
        $this->db->bind(':start_time', (new DateTime($data['start_time'], new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i:s'));
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
            ORDER BY m.start_time ASC');
        $matches = $this->db->resultSet();
        foreach ($matches as &$match) {
            $match['game_predictions_enabled'] = (int)$match['game_predictions_enabled'];
            $match['statistics_predictions_enabled'] = (int)$match['statistics_predictions_enabled'];
        }
        return $matches;
    }

    public function getMatchById($id) {
        $this->db->query('SELECT m.*, t.name AS tournament_name, t.logo AS tournament_logo, p1.name AS player1_name, p1.image AS player1_image, p1.country AS player1_country, p2.name AS player2_name, p2.image AS player2_image, p2.country AS player2_country FROM matches m 
            JOIN tournaments t ON m.tournament_id = t.id 
            JOIN players p1 ON m.player1_id = p1.id 
            JOIN players p2 ON m.player2_id = p2.id 
            WHERE m.id = :id');
        $this->db->bind(':id', $id);
        $match = $this->db->single();
        if ($match) {
            $match['game_predictions_enabled'] = (int)$match['game_predictions_enabled'];
            $match['statistics_predictions_enabled'] = (int)$match['statistics_predictions_enabled'];
        }
        return $match;
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
        // Backend validation: if marking as retired, require at least one set
        if (isset($data['status']) && 
            ($data['status'] === 'retired_player1' || $data['status'] === 'retired_player2')) {
            if (!isset($data['sets']) || !is_array($data['sets']) || count($data['sets']) < 1) {
                return ['success' => false, 'message' => 'You must enter at least one set for a retired match.'];
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

            // Grade predictions for this match
            require_once __DIR__ . '/Prediction.php';
            $prediction = new \Prediction();
            // Determine actual winner as 'player1' or 'player2'
            $actualWinner = null;
            if (!empty($data['winner_id'])) {
                // Get player1_id and player2_id for this match
                $this->db->query('SELECT player1_id, player2_id FROM matches WHERE id = :id');
                $this->db->bind(':id', $data['id']);
                $row = $this->db->single();
                if ($row) {
                    if ($data['winner_id'] == $row['player1_id']) $actualWinner = 'player1';
                    elseif ($data['winner_id'] == $row['player2_id']) $actualWinner = 'player2';
                }
            }
            if ($actualWinner) {
                $prediction->gradeMatchPredictions($data['id'], $actualWinner);
            }

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
        $this->db->bind(':player1_tiebreak', $data['player1_tiebreak'] === '' ? null : $data['player1_tiebreak']);
        $this->db->bind(':player2_tiebreak', $data['player2_tiebreak'] === '' ? null : $data['player2_tiebreak']);
        
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
        $sql .= ' ORDER BY m.start_time ASC';
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
            // Cast enabled fields to int for each match
            foreach ($matches as &$match) {
                $match['game_predictions_enabled'] = (int)$match['game_predictions_enabled'];
                $match['statistics_predictions_enabled'] = (int)$match['statistics_predictions_enabled'];
            }
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

    /**
     * Update the date/time of a match
     * @param int $matchId
     * @param string $newDateTime (Y-m-d H:i:s)
     * @return bool
     */
    public function updateMatchDate($matchId, $newDateTime) {
        $this->db->query('UPDATE matches SET start_time = :start_time WHERE id = :id');
        $this->db->bind(':start_time', (new DateTime($newDateTime, new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i:s'));
        $this->db->bind(':id', $matchId);
        return $this->db->execute();
    }

    /**
     * Set a match as finished
     * @param int $matchId
     * @return bool
     */
    public function setMatchFinished($matchId) {
        $this->db->query('UPDATE matches SET status = "finished" WHERE id = :id');
        $this->db->bind(':id', $matchId);
        return $this->db->execute();
    }
} 