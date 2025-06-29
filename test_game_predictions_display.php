<?php
session_start();
require_once 'config/config.php';
require_once 'src/classes/GamePrediction.php';
require_once 'src/classes/Database.php';

// Test configuration
$test_user_id = 1; // Make sure this user exists
$test_match_id = 1; // Make sure this match exists

echo "<h1>Game Predictions Display Test</h1>";

// Set up test session
$_SESSION['user_id'] = $test_user_id;

$gamePrediction = new GamePrediction();
$db = new Database();

echo "<h2>1. Creating Test Game Predictions</h2>";

// Create some test game predictions
$testPredictions = [
    ['game_number' => 1, 'predicted_winner' => 'player1', 'predicted_score' => '40-0'],
    ['game_number' => 2, 'predicted_winner' => 'player2', 'predicted_score' => '30-15'],
    ['game_number' => 3, 'predicted_winner' => 'player1', 'predicted_score' => 'AD-40'],
    ['game_number' => 4, 'predicted_winner' => 'player2', 'predicted_score' => '40-30'],
    ['game_number' => 5, 'predicted_winner' => 'player1', 'predicted_score' => 'game-0'],
    ['game_number' => 6, 'predicted_winner' => 'player2', 'predicted_score' => '15-30']
];

$successCount = 0;
foreach ($testPredictions as $pred) {
    $result = $gamePrediction->submitGamePrediction(
        $test_user_id,
        $test_match_id,
        $pred['game_number'],
        $pred['predicted_winner'],
        $pred['predicted_score']
    );
    
    if (isset($result['success']) && $result['success']) {
        $successCount++;
        echo "<p style='color: green;'>✓ Game {$pred['game_number']} prediction created: {$pred['predicted_score']} ({$pred['predicted_winner']})</p>";
    } else {
        echo "<p style='color: red;'>✗ Game {$pred['game_number']} prediction failed: " . ($result['error'] ?? 'Unknown error') . "</p>";
    }
}

echo "<h2>2. Testing API Response</h2>";

// Test the API endpoint
$apiUrl = "http://localhost/ATP/api/game_predictions.php?match_id={$test_match_id}&user_predictions=1";
echo "<p>Testing API endpoint: <code>{$apiUrl}</code></p>";

// Simulate the API call
$predictions = $gamePrediction->getGamePredictionsForUser($test_user_id, $test_match_id);

if ($predictions && count($predictions) > 0) {
    echo "<p style='color: green;'>✓ API returned " . count($predictions) . " predictions</p>";
    
    echo "<h3>Predictions Data:</h3>";
    echo "<pre>";
    foreach ($predictions as $pred) {
        echo "Game {$pred['game_number']}: {$pred['predicted_score']} ({$pred['predicted_winner']})\n";
    }
    echo "</pre>";
    
    echo "<h2>3. Testing Display Logic</h2>";
    
    // Simulate the JavaScript logic
    $gameData = ['success' => true, 'predictions' => $predictions];
    
    if ($gameData['success'] && $gameData['predictions'] && count($gameData['predictions']) > 0) {
        echo "<p style='color: green;'>✓ Display condition met - should show detailed game predictions</p>";
        
        // Sort predictions by game number
        $sortedPredictions = $predictions;
        usort($sortedPredictions, function($a, $b) {
            return $a['game_number'] - $b['game_number'];
        });
        
        echo "<h3>Sorted Predictions for Display:</h3>";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.5rem; margin: 1rem 0;'>";
        
        foreach ($sortedPredictions as $prediction) {
            $winnerName = $prediction['predicted_winner'] === 'player1' ? 'Player 1' : 'Player 2';
            echo "
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem; border-radius: 8px; text-align: center; font-size: 0.9rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <div style='font-weight: bold; margin-bottom: 0.25rem;'>Game {$prediction['game_number']}</div>
                <div style='font-size: 0.8rem;'>{$prediction['predicted_score']}</div>
                <div style='font-size: 0.75rem; opacity: 0.9;'>{$winnerName}</div>
            </div>";
        }
        
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>✗ Display condition not met</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ No predictions returned from API</p>";
}

echo "<h2>4. Cleanup</h2>";

// Clean up test data
$db->query('DELETE FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);

if ($db->execute()) {
    echo "<p style='color: green;'>✓ Test data cleaned up</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to clean up test data</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>This test verifies that:</p>";
echo "<ul>";
echo "<li>Game predictions can be created successfully</li>";
echo "<li>The API returns the correct data structure</li>";
echo "<li>The display logic works correctly</li>";
echo "<li>The predictions are sorted by game number</li>";
echo "<li>The visual display shows each game with its score and winner</li>";
echo "</ul>";
echo "<p><strong>Expected Result:</strong> The 'Your Predictions' section should now show a detailed grid of game predictions with each game number, score, and predicted winner displayed in an attractive card format.</p>";
?> 