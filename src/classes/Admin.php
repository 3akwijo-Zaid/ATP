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
        $this->db->query('UPDATE point_settings SET match_winner_points = :match_winner, set_winner_points = :set_winner, set_score_points = :set_score WHERE id = 1');
        $this->db->bind(':match_winner', $data['match_winner_points']);
        $this->db->bind(':set_winner', $data['set_winner_points']);
        $this->db->bind(':set_score', $data['set_score_points']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
} 