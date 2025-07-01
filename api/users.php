<?php
// --- Error reporting and headers ---
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

require_once '../config/config.php';
require_once '../src/classes/User.php';
require_once '../src/classes/Player.php';

$user = new User();

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'register': {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
            break;
        }
        $username = isset($data['username']) ? trim($data['username']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            break;
        }
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username must be 3-20 characters, letters/numbers/underscores only.']);
            break;
        }
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            break;
        }
        if ($user->findUserByUsername($username)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Username already exists.']);
            break;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $reg_data = ['username' => $username, 'password' => $hashed_password];
        if ($user->register($reg_data)) {
            $loggedInUser = $user->login($username, $password);
            if ($loggedInUser) {
                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['username'] = $loggedInUser['username'];
                $_SESSION['is_admin'] = $loggedInUser['is_admin'];
            }
            echo json_encode(['success' => true, 'message' => 'User registered successfully.', 'auto_login' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'User registration failed.']);
        }
        break;
    }
    case 'login': {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
            break;
        }
        $username = isset($data['username']) ? trim($data['username']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            break;
        }
        $loggedInUser = $user->login($username, $password);
        if ($loggedInUser) {
            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['username'] = $loggedInUser['username'];
            $_SESSION['is_admin'] = $loggedInUser['is_admin'];
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'user' => $loggedInUser]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
        break;
    }
    case 'logout': {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        break;
    }
    case 'scoreboard': {
        $scoreboard = $user->getScoreboard();
        echo json_encode($scoreboard);
        break;
    }
    case 'get_players': {
        $player = new Player();
        echo json_encode($player->getAllPlayers());
        break;
    }
    case 'add_player': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
                    break;
                }
                
                if (empty($data['name']) || empty($data['country'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Name and country are required.']);
                    break;
                }
                
                // Handle image data - if it's a data URL, we might want to process it
                $imageUrl = isset($data['image']) ? trim($data['image']) : '';
                
                // If it's a data URL and too long, we might want to reject it or process it
                if (strlen($imageUrl) > 1000000) { // 1MB limit for data URLs
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Image data too large. Please use a smaller image or provide a URL instead.']);
                    break;
                }
                
                $player = new Player();
                $playerData = [
                    'name' => trim($data['name']),
                    'image' => $imageUrl,
                    'country' => trim($data['country'])
                ];
                
                $ok = $player->createPlayer($playerData);
                if ($ok) {
                    echo json_encode(['success' => true, 'message' => 'Player added successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add player to database.']);
                }
            } catch (Exception $e) {
                error_log("Error adding player: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
        break;
    }
    case 'edit_player': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
                    break;
                }
                
                if (empty($data['id']) || empty($data['name']) || empty($data['country'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID, name, and country are required.']);
                    break;
                }
                
                $player = new Player();
                $ok = $player->updatePlayer($data['id'], $data);
                if ($ok) {
                    echo json_encode(['success' => true, 'message' => 'Player updated successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update player.']);
                }
            } catch (Exception $e) {
                error_log("Error updating player: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
        break;
    }
    case 'delete_player': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
                    break;
                }
                
                if (empty($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID is required.']);
                    break;
                }
                
                $player = new Player();
                $ok = $player->deletePlayer($data['id']);
                if ($ok) {
                    echo json_encode(['success' => true, 'message' => 'Player deleted successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete player.']);
                }
            } catch (Exception $e) {
                error_log("Error deleting player: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
        break;
    }
    default: {
        echo json_encode(['message' => 'Invalid action.']);
        break;
    }
}