<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once '../config/config.php';
require_once '../src/classes/Database.php';

// Only allow admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $db->query('SELECT * FROM point_settings WHERE id = 1');
    $settings = $db->single();
    if ($settings) {
        echo json_encode(['success' => true, 'settings' => $settings]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Settings not found']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $fields = [
        'match_winner_points' => [0, 100],
        'set_winner_points' => [0, 50],
        'set_score_points' => [0, 50],
        'tiebreak_score_points' => [0, 30],
        'game_winner_points' => [0, 20],
        'game_score_points' => [0, 20],
        'exact_game_score_points' => [0, 50],
        'set1_complete_points' => [0, 100]
    ];
    foreach ($fields as $field => $range) {
        if (!isset($data[$field]) || !is_numeric($data[$field]) || $data[$field] < $range[0] || $data[$field] > $range[1]) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Invalid value for $field"]);
            exit;
        }
    }

    // Update settings
    $db->query('UPDATE point_settings SET 
        match_winner_points = :match_winner_points,
        set_winner_points = :set_winner_points,
        set_score_points = :set_score_points,
        tiebreak_score_points = :tiebreak_score_points,
        game_winner_points = :game_winner_points,
        game_score_points = :game_score_points,
        exact_game_score_points = :exact_game_score_points,
        set1_complete_points = :set1_complete_points
        WHERE id = 1');
    foreach ($fields as $field => $_) {
        $db->bind(":$field", $data[$field]);
    }
    if ($db->execute()) {
        echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']); 