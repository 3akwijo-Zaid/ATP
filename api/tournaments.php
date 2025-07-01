<?php
// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'error'=>'Server error: ' . $e->getMessage()]);
    exit;
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'error'=>"PHP error [$errno]: $errstr in $errfile on line $errline"]);
    exit;
});

session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../src/classes/Tournament.php';
require_once '../config/config.php';


$method = $_SERVER['REQUEST_METHOD'];
$tournament = new Tournament();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $tournament->getTournamentById($id);
            sendJsonResponse($result);
        } elseif (isset($_GET['rounds']) && isset($_GET['tournament_id'])) {
            $tid = intval($_GET['tournament_id']);
            $result = $tournament->getRounds($tid);
            sendJsonResponse($result);
        } else {
            $result = $tournament->getAllTournaments();
            sendJsonResponse($result);
        }
        break;
    case 'POST':
        if (!isAuthenticated()) {
            sendJsonResponse(['success'=>false, 'error'=>'Not authenticated']);
        }
        if (!isAdmin()) {
            sendJsonResponse(['success'=>false, 'error'=>'Admin only']);
        }
        $data = getJsonInput();
        // Validate required fields
        if (empty($data['name'])) {
            sendJsonResponse(['success'=>false, 'error'=>'Name is required']);
        }
        $ok = $tournament->createTournament($data);
        if ($ok) {
            sendJsonResponse(['success'=>true]);
        } else {
            sendJsonResponse(['success'=>false, 'error'=>'Database error or invalid input']);
        }
        break;
    case 'PUT':
        if (!isAuthenticated()) {
            sendJsonResponse(['success'=>false, 'error'=>'Not authenticated']);
        }
        if (!isAdmin()) {
            sendJsonResponse(['success'=>false, 'error'=>'Admin only']);
        }
        parse_str($_SERVER['QUERY_STRING'], $params);
        if (empty($params['id'])) {
            sendJsonResponse(['success'=>false, 'error'=>'Tournament ID required']);
        }
        $id = intval($params['id']);
        $data = getJsonInput();
        if (empty($data['name'])) {
            sendJsonResponse(['success'=>false, 'error'=>'Name is required']);
        }
        $ok = $tournament->updateTournament($id, $data);
        if ($ok) {
            sendJsonResponse(['success'=>true]);
        } else {
            sendJsonResponse(['success'=>false, 'error'=>'Database error or invalid input']);
        }
        break;
    case 'DELETE':
        if (!isAuthenticated()) {
            sendJsonResponse(['success'=>false, 'error'=>'Not authenticated']);
        }
        if (!isAdmin()) {
            sendJsonResponse(['success'=>false, 'error'=>'Admin only']);
        }
        parse_str($_SERVER['QUERY_STRING'], $params);
        if (empty($params['id'])) {
            sendJsonResponse(['success'=>false, 'error'=>'Tournament ID required']);
        }
        $id = intval($params['id']);
        $ok = $tournament->deleteTournament($id);
        if ($ok) {
            sendJsonResponse(['success'=>true]);
        } else {
            sendJsonResponse(['success'=>false, 'error'=>'Database error or invalid input']);
        }
        break;
    default:
        http_response_code(405);
        sendJsonResponse(['success'=>false, 'error'=>'Method not allowed']);
}

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}
function sendJsonResponse($data) {
    echo json_encode($data);
    exit;
}
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
} 