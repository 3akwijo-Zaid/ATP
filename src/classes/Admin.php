<?php
require_once 'Database.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function login($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username AND is_admin = 1');
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        if($row) {
            $hashed_password = $row['password_hash'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }

    public function getPointSettings() {
        $this->db->query('SELECT * FROM point_settings WHERE id = 1');
        return $this->db->single();
    }

    public function updatePointSettings($data) {
        $this->db->query('UPDATE point_settings SET 
                         match_winner_points = :match_winner, 
                         match_score_points = :match_score, 
                         set_score_points = :set_score,
                         tiebreak_score_points = :tiebreak_score,
                         game_winner_points = :game_winner,
                         game_score_points = :game_score,
                         exact_game_score_points = :exact_game_score,
                         set1_complete_points = :set1_complete
                         WHERE id = 1');
        $this->db->bind(':match_winner', $data['match_winner_points']);
        $this->db->bind(':match_score', $data['match_score_points']);
        $this->db->bind(':set_score', $data['set_score_points']);
        $this->db->bind(':tiebreak_score', $data['tiebreak_score_points'] ?? 0);
        $this->db->bind(':game_winner', $data['game_winner_points'] ?? 2);
        $this->db->bind(':game_score', $data['game_score_points'] ?? 5);
        $this->db->bind(':exact_game_score', $data['exact_game_score_points'] ?? 10);
        $this->db->bind(':set1_complete', $data['set1_complete_points'] ?? 20);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
} 