<?php
// Test file to verify prediction submission and retrieval
session_start();
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/GamePrediction.php';

echo "<h1>Prediction System Test</h1>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href='public/login.php'>login</a> first to test predictions.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Unknown';

echo "<p>Logged in as: $username (ID: $user_id)</p>";

// Test 1: Check if there are any matches
echo "<h2>Test 1: Available Matches</h2>";
try {
    $db = new Database();
    $db->query("SELECT m.*, p1.name as player1_name, p2.name as player2_name 
                FROM matches m 
                JOIN players p1 ON m.player1_id = p1.id 
                JOIN players p2 ON m.player2_id = p2.id 
                WHERE m.status = 'upcoming' 
                ORDER BY m.start_time ASC 
                LIMIT 5");
    $matches = $db->resultSet();
    
    if (count($matches) > 0) {
        echo "<p>Found " . count($matches) . " upcoming matches:</p>";
        foreach ($matches as $match) {
            echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
            echo "<strong>{$match['player1_name']} vs {$match['player2_name']}</strong><br>";
            echo "Match ID: {$match['id']}<br>";
            echo "Start Time: {$match['start_time']}<br>";
            echo "<a href='public/predictions.php?match_id={$match['id']}' target='_blank'>Test Predictions Page</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No upcoming matches found.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error loading matches: " . $e->getMessage() . "</p>";
}

// Test 2: Check existing predictions for this user
echo "<h2>Test 2: Existing Predictions</h2>";
try {
    $prediction = new Prediction();
    $userPredictions = $prediction->getUserPredictions($user_id);
    
    if (count($userPredictions) > 0) {
        echo "<p>Found " . count($userPredictions) . " existing predictions:</p>";
        foreach ($userPredictions as $pred) {
            echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
            echo "<strong>Match: {$pred['player1_name']} vs {$pred['player2_name']}</strong><br>";
            echo "Created: {$pred['created_at']}<br>";
            echo "Points: {$pred['points_awarded']}<br>";
            if ($pred['prediction_data']) {
                $data = json_decode($pred['prediction_data'], true);
                echo "Winner: {$data['winner']}<br>";
                echo "Sets: " . json_encode($data['sets']) . "<br>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No existing predictions found for this user.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error loading predictions: " . $e->getMessage() . "</p>";
}

// Test 3: Check game predictions
echo "<h2>Test 3: Game Predictions</h2>";
try {
    $gamePrediction = new GamePrediction();
    $gamePredictions = $gamePrediction->getGamePredictionsForUser($user_id);
    
    if (count($gamePredictions) > 0) {
        echo "<p>Found " . count($gamePredictions) . " game predictions:</p>";
        foreach ($gamePredictions as $pred) {
            echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
            echo "<strong>Match: {$pred['player1_name']} vs {$pred['player2_name']}</strong><br>";
            echo "Game: {$pred['game_number']}<br>";
            echo "Winner: {$pred['predicted_winner']}<br>";
            echo "Score: {$pred['predicted_score']}<br>";
            echo "Points: {$pred['points_awarded']}<br>";
            echo "</div>";
        }
    } else {
        echo "<p>No game predictions found for this user.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error loading game predictions: " . $e->getMessage() . "</p>";
}

// Test 4: Test API endpoints
echo "<h2>Test 4: API Endpoints</h2>";
if (count($matches) > 0) {
    $testMatchId = $matches[0]['id'];
    echo "<p>Testing API endpoints for match ID: $testMatchId</p>";
    
    // Test match predictions API
    echo "<h3>Match Predictions API</h3>";
    $url = "api/predictions.php?match_id=$testMatchId&user_id=$user_id";
    echo "<p>URL: $url</p>";
    
    // Simulate the API call
    $_GET['match_id'] = $testMatchId;
    $_GET['user_id'] = $user_id;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    ob_start();
    include 'api/predictions.php';
    $apiResponse = ob_get_clean();
    
    echo "<p>API Response:</p>";
    echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
    
    // Test game predictions API
    echo "<h3>Game Predictions API</h3>";
    $url = "api/game_predictions.php?match_id=$testMatchId&user_predictions=1";
    echo "<p>URL: $url</p>";
    
    // Simulate the API call
    $_GET['match_id'] = $testMatchId;
    $_GET['user_predictions'] = '1';
    unset($_GET['user_id']);
    
    ob_start();
    include 'api/game_predictions.php';
    $apiResponse = ob_get_clean();
    
    echo "<p>API Response:</p>";
    echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='public/index.php'>Go to Homepage</a> | <a href='public/predictions.php?match_id=1'>Test Predictions Page</a></p>";
?> 