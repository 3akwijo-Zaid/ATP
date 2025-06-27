<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/config.php';
require_once '../src/classes/User.php';

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
            echo json_encode(['message' => 'User registered successfully.']);
        } else {
            echo json_encode(['message' => 'User registration failed.']);
        }
        break;

    case 'login':
        $data = json_decode(file_get_contents("php://input"));
        $loggedInUser = $user->login($data->username, $data->password);
        if ($loggedInUser) {
            session_start();
            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['username'] = $loggedInUser['username'];
            $_SESSION['is_admin'] = $loggedInUser['is_admin'];
            echo json_encode(['message' => 'Login successful.', 'user' => $loggedInUser]);
        } else {
            echo json_encode(['message' => 'Invalid credentials.']);
        }
        break;

    case 'scoreboard':
        $scoreboard = $user->getScoreboard();
        echo json_encode($scoreboard);
        break;

    default:
        echo json_encode(['message' => 'Invalid action.']);
        break;
} 