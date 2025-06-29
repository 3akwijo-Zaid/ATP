<?php
require_once 'Database.php';

class Tournament {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createTournament($data) {
        $this->db->query('INSERT INTO tournaments (name, logo) VALUES (:name, :logo)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':logo', $data['logo']);
        return $this->db->execute();
    }

    public function getAllTournaments() {
        $this->db->query('SELECT * FROM tournaments ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function getTournamentById($id) {
        $this->db->query('SELECT * FROM tournaments WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getRounds($tournamentId) {
        $this->db->query('SELECT DISTINCT round FROM matches WHERE tournament_id = :tid ORDER BY start_time');
        $this->db->bind(':tid', $tournamentId);
        $rounds = $this->db->resultSet();
        return array_map(function($r) { return $r['round']; }, $rounds);
    }

    public function updateTournament($id, $data) {
        $this->db->query('UPDATE tournaments SET name = :name, logo = :logo WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':logo', $data['logo']);
        return $this->db->execute();
    }

    public function deleteTournament($id) {
        $this->db->query('DELETE FROM tournaments WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
} 