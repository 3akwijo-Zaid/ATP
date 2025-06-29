<?php
session_start();
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/Database.php';

// Test configuration
$test_user_id = 1; // Make sure this user exists
$test_match_id = 1; // Make sure this match exists

echo "<h1>Simple Prediction Overwrite Test</h1>";

// Set up test session
$_SESSION['user_id'] = $test_user_id;

$prediction = new Prediction();
$db = new Database();

echo "<h2>Testing Prediction Overwrite</h2>";

// First submission
echo "<h3>1. Submitting first prediction...</h3>";
$firstResult = $prediction->submit($test_user_id, $test_match_id, 'player1', [
    ['player1' => 6, 'player2' => 4]
]);

if ($firstResult['success']) {
    echo "<p style='color: green;'>✓ First prediction submitted: " . $firstResult['message'] . "</p>";
    
    // Get the prediction from database
    $db->query('SELECT * FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $firstPrediction = $db->single();
    
    if ($firstPrediction) {
        $firstData = json_decode($firstPrediction['prediction_data'], true);
        echo "<p>First prediction winner: " . $firstData['winner'] . "</p>";
        echo "<p>First prediction ID: " . $firstPrediction['id'] . "</p>";
        
        // Second submission (should overwrite)
        echo "<h3>2. Submitting second prediction (should overwrite)...</h3>";
        $secondResult = $prediction->submit($test_user_id, $test_match_id, 'player2', [
            ['player1' => 4, 'player2' => 6]
        ]);
        
        if ($secondResult['success']) {
            echo "<p style='color: green;'>✓ Second prediction submitted: " . $secondResult['message'] . "</p>";
            
            // Get the prediction from database again
            $db->query('SELECT * FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
            $db->bind(':user_id', $test_user_id);
            $db->bind(':match_id', $test_match_id);
            $secondPrediction = $db->single();
            
            if ($secondPrediction) {
                $secondData = json_decode($secondPrediction['prediction_data'], true);
                echo "<p>Second prediction winner: " . $secondData['winner'] . "</p>";
                echo "<p>Second prediction ID: " . $secondPrediction['id'] . "</p>";
                
                // Check if overwrite worked
                if ($secondPrediction['id'] == $firstPrediction['id']) {
                    echo "<p style='color: green;'>✓ SUCCESS: Same ID - prediction was overwritten!</p>";
                    
                    if ($firstData['winner'] !== $secondData['winner']) {
                        echo "<p style='color: green;'>✓ SUCCESS: Winner changed from '{$firstData['winner']}' to '{$secondData['winner']}'</p>";
                    } else {
                        echo "<p style='color: red;'>✗ FAILED: Winner did not change</p>";
                    }
                } else {
                    echo "<p style='color: red;'>✗ FAILED: Different ID - prediction was not overwritten</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ FAILED: Could not retrieve second prediction</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ FAILED: Second prediction failed: " . $secondResult['message'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ FAILED: Could not retrieve first prediction</p>";
    }
} else {
    echo "<p style='color: red;'>✗ FAILED: First prediction failed: " . $firstResult['message'] . "</p>";
}

echo "<h2>3. Count Predictions</h2>";
$db->query('SELECT COUNT(*) as count FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$count = $db->single();

echo "<p>Total predictions for user $test_user_id and match $test_match_id: " . $count['count'] . "</p>";
if ($count['count'] == 1) {
    echo "<p style='color: green;'>✓ Correct: Only 1 prediction exists</p>";
} else {
    echo "<p style='color: red;'>✗ Wrong: " . $count['count'] . " predictions exist (should be 1)</p>";
}

echo "<h2>4. Cleanup</h2>";
$db->query('DELETE FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);

if ($db->execute()) {
    echo "<p style='color: green;'>✓ Test data cleaned up</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to clean up test data</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If you see 'SUCCESS' messages above, the overwrite functionality is working correctly.</p>";
echo "<p>If you see 'FAILED' messages, there may be an issue with:</p>";
echo "<ul>";
echo "<li>Database unique constraints</li>";
echo "<li>SQL syntax</li>";
echo "<li>Database connection</li>";
echo "<li>Data validation</li>";
echo "</ul>";
?> 