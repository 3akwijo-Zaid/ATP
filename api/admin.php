<?php
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
    case 'login':
        $data = json_decode(file_get_contents("php://input"));
        $adminUser = $admin->login($data->username, $data->password);
        if ($adminUser) {
            $_SESSION['user_id'] = $adminUser['id'];
            $_SESSION['username'] = $adminUser['username'];
            $_SESSION['is_admin'] = $adminUser['is_admin'];
            echo json_encode(['message' => 'Admin login successful.']);
        } else {
            echo json_encode(['message' => 'Invalid credentials.']);
        }
        break;

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
        
    case 'get_point_settings':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $settings = $admin->getPointSettings();
        echo json_encode($settings);
        break;

    case 'update_point_settings':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $data = json_decode(file_get_contents("php://input"), true);
        if ($admin->updatePointSettings($data)) {
            echo json_encode(['message' => 'Settings updated successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to update settings.']);
        }
        break;

    case 'get_all_users':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $users = $user->getAllUsers();
        echo json_encode($users);
        break;

    case 'promote_user':
        if (!isAdmin()) { echo json_encode(['message' => 'Unauthorized']); break; }
        $data = json_decode(file_get_contents("php://input"), true);
        if ($user->promoteToAdmin($data['user_id'])) {
            echo json_encode(['message' => 'User promoted to admin successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to promote user.']);
        }
        break;

    default:
        echo json_encode(['message' => 'Invalid admin action.']);
        break;
} 