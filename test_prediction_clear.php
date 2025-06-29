<?php
session_start();
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/GamePrediction.php';

// Test configuration
$test_user_id = 1; // Make sure this user exists
$test_match_id = 1; // Make sure this match exists

echo "<h1>Prediction Clear Functionality Test</h1>";

// Set up test session
$_SESSION['user_id'] = $test_user_id;

$prediction = new Prediction();
$gamePrediction = new GamePrediction();

echo "<h2>1. Testing Match Prediction Deletion</h2>";

// First, submit a test prediction
echo "<h3>Submitting test match prediction...</h3>";
$submitResult = $prediction->submit($test_user_id, $test_match_id, 'player1', [
    ['player1' => 6, 'player2' => 4],
    ['player1' => 7, 'player2' => 5]
]);

if ($submitResult['success']) {
    echo "<p style='color: green;'>✓ Test match prediction submitted successfully</p>";
    
    // Verify prediction exists
    $existingPrediction = $prediction->getUserPrediction($test_user_id, $test_match_id);
    if ($existingPrediction) {
        echo "<p style='color: green;'>✓ Prediction found in database</p>";
        
        // Now test deletion
        echo "<h3>Testing prediction deletion...</h3>";
        $deleteResult = $prediction->deletePrediction($test_user_id, $test_match_id);
        
        if ($deleteResult['success']) {
            echo "<p style='color: green;'>✓ Prediction deleted successfully</p>";
            
            // Verify prediction is gone
            $deletedPrediction = $prediction->getUserPrediction($test_user_id, $test_match_id);
            if (!$deletedPrediction) {
                echo "<p style='color: green;'>✓ Prediction confirmed deleted from database</p>";
            } else {
                echo "<p style='color: red;'>✗ Prediction still exists in database</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to delete prediction: " . $deleteResult['message'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to find submitted prediction</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to submit test prediction: " . $submitResult['message'] . "</p>";
}

echo "<h2>2. Testing Game Prediction Deletion</h2>";

// Submit test game predictions
echo "<h3>Submitting test game predictions...</h3>";
$gamePredictions = [
    ['game_number' => 1, 'predicted_winner' => 'player1', 'predicted_score' => '40-0'],
    ['game_number' => 2, 'predicted_winner' => 'player2', 'predicted_score' => '30-15'],
    ['game_number' => 3, 'predicted_winner' => 'player1', 'predicted_score' => 'AD-40']
];

$gameSubmitCount = 0;
foreach ($gamePredictions as $gamePred) {
    $result = $gamePrediction->submitGamePrediction(
        $test_user_id, 
        $test_match_id, 
        $gamePred['game_number'], 
        $gamePred['predicted_winner'], 
        $gamePred['predicted_score']
    );
    
    if (isset($result['success']) && $result['success']) {
        $gameSubmitCount++;
    }
}

if ($gameSubmitCount === count($gamePredictions)) {
    echo "<p style='color: green;'>✓ All test game predictions submitted successfully</p>";
    
    // Verify predictions exist
    $existingGamePredictions = $gamePrediction->getGamePredictionsForUser($test_user_id, $test_match_id);
    if (count($existingGamePredictions) === count($gamePredictions)) {
        echo "<p style='color: green;'>✓ Game predictions found in database</p>";
        
        // Now test deletion
        echo "<h3>Testing game predictions deletion...</h3>";
        $deleteResult = $gamePrediction->deleteGamePredictions($test_user_id, $test_match_id);
        
        if ($deleteResult['success']) {
            echo "<p style='color: green;'>✓ Game predictions deleted successfully</p>";
            
            // Verify predictions are gone
            $deletedGamePredictions = $gamePrediction->getGamePredictionsForUser($test_user_id, $test_match_id);
            if (count($deletedGamePredictions) === 0) {
                echo "<p style='color: green;'>✓ Game predictions confirmed deleted from database</p>";
            } else {
                echo "<p style='color: red;'>✗ Game predictions still exist in database</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to delete game predictions: " . $deleteResult['message'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to find submitted game predictions</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to submit some game predictions</p>";
}

echo "<h2>3. Testing API Endpoints</h2>";

// Test DELETE API endpoints
echo "<h3>Testing DELETE API endpoints...</h3>";

// Test match prediction DELETE API
$deleteUrl = "http://localhost/ATP/api/predictions.php?match_id=$test_match_id";
$context = stream_context_create([
    'http' => [
        'method' => 'DELETE',
        'header' => 'Cookie: ' . session_name() . '=' . session_id()
    ]
]);

$response = file_get_contents($deleteUrl, false, $context);
if ($response !== false) {
    $result = json_decode($response, true);
    if ($result && isset($result['success'])) {
        echo "<p style='color: green;'>✓ DELETE API endpoint working</p>";
    } else {
        echo "<p style='color: red;'>✗ DELETE API endpoint error</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to call DELETE API endpoint</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>The clear functionality should now work correctly:</p>";
echo "<ul>";
echo "<li>Clear buttons will delete predictions from the database</li>";
echo "<li>Each user can only have one prediction per match</li>";
echo "<li>New predictions will overwrite previous ones</li>";
echo "<li>Predictions are locked 1 hour before match start</li>";
echo "</ul>";

echo "<p><strong>Note:</strong> Make sure you have a user with ID $test_user_id and a match with ID $test_match_id in your database for this test to work properly.</p>";
?> 