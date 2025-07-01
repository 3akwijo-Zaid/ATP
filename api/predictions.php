<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require_once '../config/config.php';
require_once '../src/classes/Prediction.php';

$prediction = new Prediction();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in first', 'error_code' => 'AUTH_REQUIRED']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Submit prediction
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $matchId = $data['match_id'] ?? null;
    $winner = $data['winner'] ?? null;
    $sets = $data['sets'] ?? null;
    
    if (!$matchId || !$winner || !$sets) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: match_id, winner, or sets']);
        exit;
    }
    
    // Validate that sets is an array
    if (!is_array($sets)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Sets must be an array']);
        exit;
    }
    
    $result = $prediction->submit($userId, $matchId, $winner, $sets);
    echo json_encode($result);
    
} elseif ($method === 'GET') {
    if (isset($_GET['user_id'])) {
        // Return only the current user's prediction for the match
        $userId = intval($_GET['user_id']);
        $matchId = intval($_GET['match_id']);
        $prediction = $prediction->getUserPrediction($userId, $matchId);
        echo json_encode([
            'success' => true,
            'prediction' => $prediction
        ]);
        exit;
    } else if (isset($_GET['match_id'])) {
        // Return all predictions for the match
        $matchId = intval($_GET['match_id']);
        $predictions = $prediction->getMatchPredictions($matchId);
        echo json_encode([
            'success' => true,
            'predictions' => $predictions
        ]);
        exit;
    } else {
        // Get all user predictions for the logged-in user
        $userId = $_SESSION['user_id'];
        $userPredictions = $prediction->getUserPredictions($userId);
        echo json_encode(['success' => true, 'predictions' => $userPredictions]);
    }
    
} elseif ($method === 'PUT') {
    // Admin only - for calculating points
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    if (isset($data['action']) && $data['action'] == 'calculate_points') {
        if (!isset($data['match_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Match ID required for points calculation']);
            exit;
        }
        
        $result = $prediction->calculatePoints($data['match_id']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Points calculated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to calculate points or match not finished.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} elseif ($method === 'DELETE') {
    // Delete prediction
    $userId = $_SESSION['user_id'];
    $matchId = $_GET['match_id'] ?? null;
    
    if (!$matchId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Match ID required']);
        exit;
    }
    
    $result = $prediction->deletePrediction($userId, $matchId);
    echo json_encode($result);
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} 