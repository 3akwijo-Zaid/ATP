<?php

class TennisMatch {
    private $conn;
    private $table_name = "matches";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($player1, $player2, $tournament, $match_date, $odds1, $odds2) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (player1, player2, tournament, match_date, set1, set2, set3, set4, set5, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'upcoming')";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$player1, $player2, $tournament, $match_date, $odds1, $odds2]);
    }

    public function getUpcomingMatches() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'upcoming' 
                  ORDER BY match_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompletedMatches() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'completed' 
                  ORDER BY match_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateResult($id, $winner, $score) {
        $query = "UPDATE " . $this->table_name . " 
                  SET winner = ?, score = ?, set1 = ?, set2 = ?, set3 = ?, set4 = ?, set5 = ?, status = 'completed' 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$winner, $score, $id]);
    }

    public function getMatchById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>