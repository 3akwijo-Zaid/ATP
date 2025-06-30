<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../src/classes/User.php';
require_once '../src/classes/Prediction.php';


if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user = new User();
$prediction = new Prediction();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        try {
            $profile = $user->getProfile($userId);
            $profile['username'] = $username;
            $stats = $user->getStats($userId);
            $badges = $user->getBadges($userId);
            $activity = $prediction->getRecentActivity($userId, 3);
            ob_clean();
            echo json_encode([
                'success' => true,
                'profile' => $profile,
                'stats' => $stats,
                'badges' => $badges,
                'activity' => $activity
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
        break;
    case 'POST':
        try {
            // Handle avatar upload if present
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/assets/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileTmp = $_FILES['avatar']['tmp_name'];
                $fileName = basename($_FILES['avatar']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($fileExt, $allowed)) {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
                    exit;
                }
                $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) {
                    // Prepare data for update
                    $updateData = ['avatar' => $newFileName];
                    
                    // Add other form fields if present
                    if (isset($_POST['country']) && $_POST['country'] !== '') {
                        $updateData['country'] = $_POST['country'];
                    }
                    if (isset($_POST['password']) && $_POST['password'] !== '') {
                        $updateData['password'] = $_POST['password'];
                    }
                    
                    // Save to DB
                    $result = $user->updateProfile($userId, $updateData);
                    ob_clean();
                    if ($result['success']) {
                        echo json_encode(['success' => true, 'avatar' => 'assets/img/' . $newFileName, 'message' => 'Profile updated successfully']);
                    } else {
                        echo json_encode($result);
                    }
                } else {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Failed to upload avatar.']);
                }
            } else {
                // Handle JSON update for other fields (or avatar as string)
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
                    exit;
                }
                
                $result = $user->updateProfile($userId, $data);
                ob_clean();
                echo json_encode($result);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
        break;
    default:
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Invalid method']);
}