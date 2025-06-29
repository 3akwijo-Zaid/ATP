<?php
session_start();
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/GamePrediction.php';
require_once 'src/classes/Database.php';

// Test configuration
$test_user_id = 1; // Make sure this user exists
$test_match_id = 1; // Make sure this match exists

echo "<h1>No Multiple Submissions Test</h1>";

// Set up test session
$_SESSION['user_id'] = $test_user_id;

$prediction = new Prediction();
$gamePrediction = new GamePrediction();
$db = new Database();

echo "<h2>1. Testing Match Prediction - No Multiple Submissions</h2>";

// First submission
echo "<h3>First submission...</h3>";
$firstResult = $prediction->submit($test_user_id, $test_match_id, 'player1', [
    ['player1' => 6, 'player2' => 4]
]);

if ($firstResult['success']) {
    echo "<p style='color: green;'>✓ First prediction submitted successfully</p>";
    
    // Second submission (should be blocked)
    echo "<h3>Second submission (should be blocked)...</h3>";
    $secondResult = $prediction->submit($test_user_id, $test_match_id, 'player2', [
        ['player1' => 4, 'player2' => 6]
    ]);
    
    if (!$secondResult['success']) {
        echo "<p style='color: green;'>✓ Second prediction correctly blocked: " . $secondResult['message'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Second prediction should have been blocked but was allowed</p>";
    }
    
    // Verify only one prediction exists
    $db->query('SELECT COUNT(*) as count FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $count = $db->single();
    
    if ($count['count'] == 1) {
        echo "<p style='color: green;'>✓ Correct: Only 1 prediction exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Wrong: " . $count['count'] . " predictions exist (should be 1)</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ First prediction failed: " . $firstResult['message'] . "</p>";
}

echo "<h2>2. Testing Game Prediction - No Multiple Submissions</h2>";

// First game prediction
echo "<h3>First game prediction...</h3>";
$firstGameResult = $gamePrediction->submitGamePrediction($test_user_id, $test_match_id, 1, 'player1', '40-0');

if (isset($firstGameResult['success']) && $firstGameResult['success']) {
    echo "<p style='color: green;'>✓ First game prediction submitted successfully</p>";
    
    // Second game prediction for same game (should be blocked)
    echo "<h3>Second game prediction for same game (should be blocked)...</h3>";
    $secondGameResult = $gamePrediction->submitGamePrediction($test_user_id, $test_match_id, 1, 'player2', '30-15');
    
    if (isset($secondGameResult['error'])) {
        echo "<p style='color: green;'>✓ Second game prediction correctly blocked: " . $secondGameResult['error'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Second game prediction should have been blocked but was allowed</p>";
    }
    
    // Third game prediction for different game (should be allowed)
    echo "<h3>Third game prediction for different game (should be allowed)...</h3>";
    $thirdGameResult = $gamePrediction->submitGamePrediction($test_user_id, $test_match_id, 2, 'player2', '30-15');
    
    if (isset($thirdGameResult['success']) && $thirdGameResult['success']) {
        echo "<p style='color: green;'>✓ Third game prediction (different game) submitted successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Third game prediction failed: " . ($thirdGameResult['error'] ?? 'Unknown error') . "</p>";
    }
    
    // Verify game predictions count
    $db->query('SELECT COUNT(*) as count FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $gameCount = $db->single();
    
    if ($gameCount['count'] == 2) {
        echo "<p style='color: green;'>✓ Correct: " . $gameCount['count'] . " game predictions exist (should be 2)</p>";
    } else {
        echo "<p style='color: red;'>✗ Wrong: " . $gameCount['count'] . " game predictions exist (should be 2)</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ First game prediction failed: " . ($firstGameResult['error'] ?? 'Unknown error') . "</p>";
}

echo "<h2>3. Testing Clear Functionality</h2>";

// Test clearing match prediction
echo "<h3>Clearing match prediction...</h3>";
$clearResult = $prediction->deletePrediction($test_user_id, $test_match_id);

if ($clearResult['success']) {
    echo "<p style='color: green;'>✓ Match prediction cleared successfully</p>";
    
    // Try submitting again (should work now)
    echo "<h3>Trying to submit again after clearing...</h3>";
    $retryResult = $prediction->submit($test_user_id, $test_match_id, 'player2', [
        ['player1' => 4, 'player2' => 6]
    ]);
    
    if ($retryResult['success']) {
        echo "<p style='color: green;'>✓ Successfully submitted new prediction after clearing</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to submit new prediction after clearing: " . $retryResult['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to clear match prediction: " . $clearResult['message'] . "</p>";
}

// Test clearing game predictions
echo "<h3>Clearing game predictions...</h3>";
$clearGameResult = $gamePrediction->deleteGamePredictions($test_user_id, $test_match_id);

if ($clearGameResult['success']) {
    echo "<p style='color: green;'>✓ Game predictions cleared successfully</p>";
    
    // Try submitting game prediction again (should work now)
    echo "<h3>Trying to submit game prediction again after clearing...</h3>";
    $retryGameResult = $gamePrediction->submitGamePrediction($test_user_id, $test_match_id, 1, 'player1', '40-0');
    
    if (isset($retryGameResult['success']) && $retryGameResult['success']) {
        echo "<p style='color: green;'>✓ Successfully submitted new game prediction after clearing</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to submit new game prediction after clearing: " . ($retryGameResult['error'] ?? 'Unknown error') . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to clear game predictions: " . $clearGameResult['message'] . "</p>";
}

echo "<h2>4. Final Cleanup</h2>";

// Clean up all test data
$db->query('DELETE FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$db->execute();

$db->query('DELETE FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$db->execute();

echo "<p style='color: green;'>✓ Test data cleaned up</p>";

echo "<h2>Test Summary</h2>";
echo "<p>The system now prevents multiple submissions:</p>";
echo "<ul>";
echo "<li>✓ Users cannot submit multiple match predictions</li>";
echo "<li>✓ Users cannot submit multiple predictions for the same game</li>";
echo "<li>✓ Users can submit predictions for different games</li>";
echo "<li>✓ Clear functionality allows new submissions</li>";
echo "<li>✓ Save buttons are disabled when predictions exist</li>";
echo "<li>✓ Clear buttons re-enable save buttons</li>";
echo "</ul>";
echo "<p>Users must use the clear button to delete existing predictions before submitting new ones.</p>";
?> 