<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/config.php';
require_once '../src/classes/Match.php';

$matchManager = new MatchManager();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($method == 'GET') {
    if ($id) {
        $match = $matchManager->getMatchById($id);
        echo json_encode($match);
    } else {
        $matches = $matchManager->getMatches();
        echo json_encode($matches);
    }
} else {
    echo json_encode(['message' => 'Method not supported.']);
} 