<?php
session_start();
require_once '../src/classes/Joker.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$joker = new Joker();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['tournament_id']) && isset($_GET['round'])) {
            // Get joker for specific tournament round
            $tournamentId = intval($_GET['tournament_id']);
            $round = $_GET['round'];
            $userId = $_SESSION['user_id'];
            
            $jokerData = $joker->getJoker($userId, $tournamentId, $round);
            echo json_encode(['success' => true, 'joker' => $jokerData]);
        } elseif (isset($_GET['available_matches'])) {
            // Get available matches for joker selection
            $tournamentId = intval($_GET['tournament_id']);
            $round = $_GET['round'];
            
            $matches = $joker->getAvailableMatches($tournamentId, $round);
            echo json_encode(['success' => true, 'matches' => $matches]);
        } elseif (isset($_GET['user_jokers'])) {
            // Get all jokers for the user
            $userId = $_SESSION['user_id'];
            $jokers = $joker->getUserJokers($userId);
            echo json_encode(['success' => true, 'jokers' => $jokers]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['tournament_id']) || !isset($data['round']) || 
            !isset($data['match_id']) || !isset($data['prediction_type'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $tournamentId = intval($data['tournament_id']);
        $round = $data['round'];
        $matchId = intval($data['match_id']);
        $predictionType = $data['prediction_type'];
        
        // Validate prediction type
        if (!in_array($predictionType, ['match', 'game', 'statistics'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid prediction type']);
            exit;
        }
        
        $result = $joker->setJoker($userId, $tournamentId, $round, $matchId, $predictionType);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Joker set successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to set joker']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['tournament_id']) && isset($_GET['round'])) {
            $userId = $_SESSION['user_id'];
            $tournamentId = intval($_GET['tournament_id']);
            $round = $_GET['round'];
            
            $result = $joker->removeJoker($userId, $tournamentId, $round);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Joker removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove joker']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
} 