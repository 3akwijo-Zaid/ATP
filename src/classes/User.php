<?php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function register($data) {
        $this->db->query('INSERT INTO users (username, password_hash) VALUES (:username, :password)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function login($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row['password_hash'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }

    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getScoreboard() {
        $this->db->query('SELECT id, username, points FROM users ORDER BY points DESC');
        return $this->db->resultSet();
    }

    public function getAllUsers() {
        $this->db->query('SELECT id, username, points, is_admin FROM users ORDER BY username ASC');
        return $this->db->resultSet();
    }

    public function promoteToAdmin($userId) {
        $this->db->query('UPDATE users SET is_admin = 1 WHERE id = :user_id');
        $this->db->bind(':user_id', $userId);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
} 