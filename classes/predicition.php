<?php

class Prediction {
    private $conn;
    private $table_name = "predictions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($match_id, $user_name, $predicted_winner, $set1, $set2, $set3, $set4, $set5, $confidence) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (match_id, user_name, predicted_winner, set1, set2, set3, set4, set5, confidence, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ? NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$match_id, $user_name, $predicted_winner, $set1, $set2, $set3, $set4, $set5, $confidence]);
    }

    public function getPredictionsByMatch($match_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE match_id = ? 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$match_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccuracyStats($user_name) {
        $query = "SELECT 
                COUNT(*) as total_predictions,
                SUM(CASE WHEN p.predicted_winner = m.winner THEN 1 ELSE 0 END) as correct_predictions,
                SUM(
                CASE 
                    WHEN p.set1 = m.set1 
                     AND p.set2 = m.set2 
                     AND p.set3 = m.set3 
                     AND (p.set4 = m.set4 OR (p.set4 IS NULL AND m.set4 IS NULL))
                     AND (p.set5 = m.set5 OR (p.set5 IS NULL AND m.set5 IS NULL))
                    THEN 1 ELSE 0 
                END
                ) as correct_sets_predictions
              FROM " . $this->table_name . " p
              JOIN matches m ON p.match_id = m.id
              WHERE p.user_name = ? AND m.status = 'completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>