<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/User.php';
require_once '../src/classes/Player.php';

$user = new User();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'register':
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->username) || empty($data->password)) {
            echo json_encode(['message' => 'Username and password are required.']);
            break;
        }
        if ($user->findUserByUsername($data->username)) {
            echo json_encode(['message' => 'Username already exists.']);
            break;
        }
        $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
        $reg_data = ['username' => $data->username, 'password' => $hashed_password];
        if ($user->register($reg_data)) {
            // Auto-login after registration
            $loggedInUser = $user->login($data->username, $data->password);
            if ($loggedInUser) {
                session_start();
                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['username'] = $loggedInUser['username'];
                $_SESSION['is_admin'] = $loggedInUser['is_admin'];
            }
            echo json_encode(['message' => 'User registered successfully.', 'auto_login' => true]);
        } else {
            echo json_encode(['message' => 'User registration failed.']);
        }
        break;

    case 'login':
        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
            break;
        }
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            break;
        }
        $username = trim($data['username']);
        $password = $data['password'];
        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password cannot be empty.']);
            break;
        }
        $loggedInUser = $user->login($username, $password);
        if ($loggedInUser) {
            session_start();
            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['username'] = $loggedInUser['username'];
            $_SESSION['is_admin'] = $loggedInUser['is_admin'];
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'user' => $loggedInUser]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
        break;

    case 'scoreboard':
        $scoreboard = $user->getScoreboard();
        echo json_encode($scoreboard);
        break;

    case 'get_players':
        $player = new Player();
        echo json_encode($player->getAllPlayers());
        exit;

    case 'add_player':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['name']) || empty($data['country'])) {
                echo json_encode(['message' => 'Name and country are required.']);
                exit;
            }
            $player = new Player();
            $ok = $player->createPlayer($data);
            if ($ok) {
                echo json_encode(['message' => 'Player added successfully.']);
            } else {
                echo json_encode(['message' => 'Failed to add player.']);
            }
            exit;
        }
        echo json_encode(['message' => 'Invalid action.']);
        break;

    case 'edit_player':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['id']) || empty($data['name']) || empty($data['country'])) {
                echo json_encode(['message' => 'ID, name, and country are required.']);
                exit;
            }
            $player = new Player();
            $ok = $player->updatePlayer($data['id'], $data);
            if ($ok) {
                echo json_encode(['message' => 'Player updated successfully.']);
            } else {
                echo json_encode(['message' => 'Failed to update player.']);
            }
            exit;
        }
        echo json_encode(['message' => 'Invalid action.']);
        break;

    case 'delete_player':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['id'])) {
                echo json_encode(['message' => 'ID is required.']);
                exit;
            }
            $player = new Player();
            $ok = $player->deletePlayer($data['id']);
            if ($ok) {
                echo json_encode(['message' => 'Player deleted successfully.']);
            } else {
                echo json_encode(['message' => 'Failed to delete player.']);
            }
            exit;
        }
        echo json_encode(['message' => 'Invalid action.']);
        break;

    default:
        echo json_encode(['message' => 'Invalid action.']);
        break;
} 