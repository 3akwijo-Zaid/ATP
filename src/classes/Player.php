<?php
require_once 'Database.php';

class Player {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createPlayer($data) {
        try {
            $this->db->query('INSERT INTO players (name, image, country) VALUES (:name, :image, :country)');
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':image', $data['image']);
            $this->db->bind(':country', $data['country']);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Player creation error: " . $e->getMessage());
            throw new Exception("Failed to create player: " . $e->getMessage());
        }
    }

    public function getAllPlayers() {
        $this->db->query('SELECT * FROM players ORDER BY name ASC');
        return $this->db->resultSet();
    }

    public function getPlayerById($id) {
        $this->db->query('SELECT * FROM players WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getPlayersForTournament($tournamentId) {
        $this->db->query('SELECT DISTINCT p.* FROM players p JOIN matches m ON (p.id = m.player1_id OR p.id = m.player2_id) WHERE m.tournament_id = :tid');
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultSet();
    }

    public function updatePlayer($id, $data) {
        try {
            $this->db->query('UPDATE players SET name = :name, image = :image, country = :country WHERE id = :id');
            $this->db->bind(':id', $id);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':image', $data['image']);
            $this->db->bind(':country', $data['country']);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Player update error: " . $e->getMessage());
            throw new Exception("Failed to update player: " . $e->getMessage());
        }
    }

    public function deletePlayer($id) {
        try {
            $this->db->query('DELETE FROM players WHERE id = :id');
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Player deletion error: " . $e->getMessage());
            throw new Exception("Failed to delete player: " . $e->getMessage());
        }
    }
} 