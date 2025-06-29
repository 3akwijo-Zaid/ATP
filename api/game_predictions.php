<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/GamePrediction.php';

$gamePrediction = new GamePrediction();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $data['user_id'] = $_SESSION['user_id'];
    
    if (!isset($data['match_id']) || !isset($data['game_number']) || 
        !isset($data['predicted_winner']) || !isset($data['predicted_score'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $result = $gamePrediction->submitGamePrediction(
        $data['user_id'],
        $data['match_id'],
        $data['game_number'],
        $data['predicted_winner'],
        $data['predicted_score']
    );
    
    if (isset($result['success']) && $result['success']) {
        echo json_encode(['success' => true, 'message' => $result['message']]);
    } else {
        echo json_encode(['success' => false, 'message' => $result['error']]);
    }
    
} elseif ($method == 'GET') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $userId = $_SESSION['user_id'];
    
    if (isset($_GET['match_id'])) {
        $matchId = $_GET['match_id'];
        
        if (isset($_GET['set_completion'])) {
            // Get set completion info
            $setCompletion = $gamePrediction->getSet1Completion($matchId);
            echo json_encode(['success' => true, 'set_completion' => $setCompletion]);
        } else {
            // Get predictions for match
            $predictions = $gamePrediction->getGamePredictionsForMatch($matchId, $userId);
            echo json_encode(['success' => true, 'predictions' => $predictions]);
        }
    } elseif (isset($_GET['user_predictions'])) {
        $matchId = isset($_GET['match_id']) ? $_GET['match_id'] : null;
        $predictions = $gamePrediction->getGamePredictionsForUser($userId, $matchId);
        echo json_encode(['success' => true, 'predictions' => $predictions]);
    } elseif (isset($_GET['stats'])) {
        $stats = $gamePrediction->getGamePredictionStats($userId);
        echo json_encode(['success' => true, 'stats' => $stats]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    
} elseif ($method == 'PUT') {
    // Admin only - for adding game results and set completion
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit();
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action']) && $data['action'] == 'set_completion') {
        // Handle set completion
        if (!isset($data['match_id']) || !isset($data['winner']) || 
            !isset($data['final_game']) || !isset($data['final_score'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields for set completion']);
            exit();
        }
        
        $result = $gamePrediction->setSet1Completion(
            $data['match_id'],
            $data['winner'],
            $data['final_game'],
            $data['final_score']
        );
        
        if ($result) {
            // Calculate points after setting completion
            $gamePrediction->calculateGamePoints($data['match_id']);
            echo json_encode(['success' => true, 'message' => 'Set completion recorded and points calculated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record set completion.']);
        }
        
    } elseif (isset($data['action']) && $data['action'] == 'calculate_points') {
        // Handle points calculation
        if (!isset($data['match_id'])) {
            echo json_encode(['success' => false, 'message' => 'Match ID required for points calculation']);
            exit();
        }
        
        $result = $gamePrediction->calculateGamePoints($data['match_id']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Points calculated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to calculate points.']);
        }
        
    } else {
        // Handle individual game result
        if (!isset($data['match_id']) || !isset($data['game_number']) || 
            !isset($data['winner']) || !isset($data['final_score'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }
        
        $result = $gamePrediction->addGameResult(
            $data['match_id'],
            $data['game_number'],
            $data['winner'],
            $data['final_score'],
            $data['game_duration'] ?? null
        );
        
        if ($result) {
            // Calculate points for this game
            $gamePrediction->calculateGamePoints($data['match_id']);
            echo json_encode(['success' => true, 'message' => 'Game result added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add game result.']);
        }
    }
    
} elseif ($method == 'DELETE') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $userId = $_SESSION['user_id'];
    $matchId = $_GET['match_id'] ?? null;
    
    if (!$matchId) {
        echo json_encode(['success' => false, 'message' => 'Match ID required']);
        exit();
    }
    
    $result = $gamePrediction->deleteGamePredictions($userId, $matchId);
    echo json_encode($result);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Method not supported']);
} 