<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/Prediction.php';

$prediction = new Prediction();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $data['user_id'] = $_SESSION['user_id'];
    
    if ($prediction->submitPrediction($data)) {
        echo json_encode(['message' => 'Prediction submitted successfully.']);
    } else {
        echo json_encode(['message' => 'Failed to submit prediction.']);
    }
} elseif ($method == 'GET') {
    if (!isset($_GET['user_id'])) {
        echo json_encode(['message' => 'User ID is required.']);
        exit();
    }
    $userId = $_GET['user_id'];
    $predictions = $prediction->getPredictionsForUser($userId);
    echo json_encode($predictions);
} else {
    echo json_encode(['message' => 'Method not supported.']);
} 