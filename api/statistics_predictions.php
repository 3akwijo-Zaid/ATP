<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/StatisticsPrediction.php';

$statisticsPrediction = new StatisticsPrediction();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    $data['user_id'] = $_SESSION['user_id'];
    
    if (!isset($data['match_id']) || !isset($data['player_type']) || 
        !isset($data['aces_predicted']) || !isset($data['double_faults_predicted'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $result = $statisticsPrediction->submitStatisticsPrediction(
        $data['user_id'],
        $data['match_id'],
        $data['player_type'],
        $data['aces_predicted'],
        $data['double_faults_predicted']
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
        
        if (isset($_GET['user_predictions'])) {
            // Get user's predictions for this match
            $predictions = $statisticsPrediction->getStatisticsPredictionsForMatch($matchId, $userId);
            echo json_encode(['success' => true, 'predictions' => $predictions]);
        } elseif (isset($_GET['results'])) {
            // Get statistics results for this match
            $results = $statisticsPrediction->getStatisticsResultsForMatch($matchId);
            echo json_encode(['success' => true, 'results' => $results]);
        } else {
            // Get all predictions for this match (admin view)
            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                $predictions = $statisticsPrediction->getStatisticsPredictionsForMatch($matchId);
            } else {
                // Regular user - only their predictions
                $predictions = $statisticsPrediction->getStatisticsPredictionsForMatch($matchId, $userId);
            }
            echo json_encode(['success' => true, 'predictions' => $predictions]);
        }
    } elseif (isset($_GET['user_predictions'])) {
        $matchId = isset($_GET['match_id']) ? $_GET['match_id'] : null;
        $predictions = $statisticsPrediction->getStatisticsPredictionsForUser($userId, $matchId);
        echo json_encode(['success' => true, 'predictions' => $predictions]);
    } elseif (isset($_GET['stats'])) {
        $stats = $statisticsPrediction->getStatisticsPredictionStats($userId);
        echo json_encode(['success' => true, 'stats' => $stats]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    
} elseif ($method == 'PUT') {
    // Admin only - for adding statistics results
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit();
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action']) && $data['action'] == 'calculate_points') {
        // Handle points calculation
        if (!isset($data['match_id'])) {
            echo json_encode(['success' => false, 'message' => 'Match ID required for points calculation']);
            exit();
        }
        
        $result = $statisticsPrediction->calculateStatisticsPoints($data['match_id']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Points calculated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to calculate points.']);
        }
        
    } else {
        // Handle individual statistics result
        if (!isset($data['match_id']) || !isset($data['player_type']) || 
            !isset($data['aces_actual']) || !isset($data['double_faults_actual'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }
        
        $result = $statisticsPrediction->addStatisticsResult(
            $data['match_id'],
            $data['player_type'],
            $data['aces_actual'],
            $data['double_faults_actual']
        );
        
        if ($result) {
            // Calculate points for this match
            $statisticsPrediction->calculateStatisticsPoints($data['match_id']);
            echo json_encode(['success' => true, 'message' => 'Statistics result added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add statistics result.']);
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
    
    $result = $statisticsPrediction->deleteStatisticsPredictions($userId, $matchId);
    echo json_encode($result);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Method not supported']);
} 