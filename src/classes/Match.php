<?php
require_once 'Database.php';

class MatchManager {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createMatch($data) {
        $this->db->query('INSERT INTO matches (competition_name, player1, player2, start_time, match_format) VALUES (:competition_name, :player1, :player2, :start_time, :match_format)');
        $this->db->bind(':competition_name', $data['competition_name']);
        $this->db->bind(':player1', $data['player1']);
        $this->db->bind(':player2', $data['player2']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':match_format', $data['match_format']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getMatches() {
        $this->db->query('SELECT * FROM matches ORDER BY start_time ASC');
        return $this->db->resultSet();
    }

    public function getMatchById($id) {
        $this->db->query('SELECT * FROM matches WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateMatchResult($data) {
        $this->db->query('UPDATE matches SET status = :status, winner = :winner, result_summary = :result_summary WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':winner', $data['winner']);
        $this->db->bind(':result_summary', $data['result_summary']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
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
} 