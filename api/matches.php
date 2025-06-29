<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/Match.php';

$matchManager = new MatchManager();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $matchData = $matchManager->getMatchById($_GET['id']);
        echo json_encode($matchData);
    } elseif (isset($_GET['featured']) && $_GET['featured'] == '1') {
        // Return featured matches
        try {
            $matches = $matchManager->getFeaturedMatches();
            echo json_encode(['success' => true, 'matches' => $matches]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch featured matches.']);
        }
    } elseif (isset($_GET['grouped']) && $_GET['grouped'] == '1') {
        // Return matches grouped by day
        $tournamentId = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : null;
        $matches = $matchManager->getMatchesGroupedByDay($tournamentId);
        echo json_encode($matches);
    } else {
        // Return all matches
        $matches = $matchManager->getMatches();
        echo json_encode($matches);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Check if user is admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit();
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit();
    }
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Match ID is required']);
        exit();
    }
    
    $result = $matchManager->updateMatchWithResults($data);
    echo json_encode($result);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Method not supported']);
} 