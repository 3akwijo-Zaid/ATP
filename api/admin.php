<?php
ini_set('display_errors', 0); // Don't show errors in output
ini_set('log_errors', 1);     // Log errors to server logs
error_reporting(E_ALL);       // Report all errors

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

require_once '../config/config.php';
require_once '../src/classes/Admin.php';
require_once '../src/classes/Match.php';
require_once '../src/classes/Prediction.php';
require_once '../src/classes/User.php';

$admin = new Admin();
$matchManager = new MatchManager();
$prediction = new Prediction();
$user = new User();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Check if user is admin for protected routes
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

switch ($action) {
    case 'add_match':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $data = json_decode(file_get_contents("php://input"), true);
        if ($matchManager->createMatch($data)) {
            echo json_encode(['message' => 'Match created successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to create match.']);
        }
        break;

    case 'update_result':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['match_result']) || !isset($data['sets'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid or missing data for result update.']);
            break;
        }
        if ($matchManager->updateMatchResult($data['match_result'])) {
            foreach($data['sets'] as $set) {
                $matchManager->addMatchSet($set);
            }
            $prediction->calculatePoints($data['match_result']['id']);
            echo json_encode(['message' => 'Result updated and points calculated.']);
        } else {
            echo json_encode(['message' => 'Failed to update result.']);
        }
        break;
        
    case 'get_all_users':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $users = $user->getAllUsers();
        echo json_encode($users);
        break;

    case 'promote_user':
        if (!isAdmin()) { 
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
            break; 
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            break;
        }
        if ($user->promoteToAdmin($data['user_id'])) {
            echo json_encode(['success' => true, 'message' => 'User promoted to admin successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to promote user.']);
        }
        break;

    case 'revoke_admin':
        if (!isAdmin()) { 
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
            break; 
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            break;
        }
        if ($user->revokeAdmin($data['user_id'])) {
            echo json_encode(['success' => true, 'message' => 'Admin privileges revoked successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to revoke admin privileges.']);
        }
        break;

    case 'toggle_featured':
        // Always return a JSON object with a message property
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            break;
        }
        if (!is_array($data) || !isset($data['match_id']) || !isset($data['featured'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            break;
        }
        $result = $matchManager->setFeatured($data['match_id'], $data['featured']);
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => $data['featured'] ? 'Match marked as featured.' : 'Match unfeatured.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update featured status.'
            ]);
        }
        break;

    case 'update_match_date':
        if (!isAdmin()) { 
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
            break; 
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['match_id']) || !isset($data['new_date_time'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing match_id or new_date_time']);
            break;
        }
        $result = $matchManager->updateMatchDate($data['match_id'], $data['new_date_time']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Match date updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update match date.']);
        }
        break;

    case 'recalculate_points':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            break;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['match_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing match_id']);
            break;
        }
        $result = $prediction->calculatePoints($data['match_id']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Points recalculated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to recalculate points.']);
        }
        break;

    default:
        echo json_encode(['message' => 'Invalid admin action.']);
        break;
} 