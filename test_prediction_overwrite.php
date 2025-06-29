<?php
session_start();
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/Database.php';

// Test configuration
$test_user_id = 1; // Make sure this user exists
$test_match_id = 1; // Make sure this match exists

echo "<h1>Prediction Overwrite Debug Test</h1>";

// Set up test session
$_SESSION['user_id'] = $test_user_id;

$prediction = new Prediction();
$db = new Database();

echo "<h2>1. Checking Database Schema</h2>";

// Check if unique constraint exists
$db->query("SHOW CREATE TABLE predictions");
$result = $db->single();
if ($result) {
    echo "<pre>" . $result['Create Table'] . "</pre>";
} else {
    echo "<p style='color: red;'>✗ Could not get table structure</p>";
}

echo "<h2>2. Testing Prediction Submission and Overwrite</h2>";

// First submission
echo "<h3>First submission...</h3>";
$firstResult = $prediction->submit($test_user_id, $test_match_id, 'player1', [
    ['player1' => 6, 'player2' => 4],
    ['player1' => 7, 'player2' => 5]
]);

if ($firstResult['success']) {
    echo "<p style='color: green;'>✓ First prediction submitted successfully</p>";
    
    // Check what's in the database
    $db->query('SELECT * FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $firstPrediction = $db->single();
    
    if ($firstPrediction) {
        echo "<p>First prediction data: " . $firstPrediction['prediction_data'] . "</p>";
        echo "<p>First prediction ID: " . $firstPrediction['id'] . "</p>";
        echo "<p>First prediction created_at: " . $firstPrediction['created_at'] . "</p>";
    }
    
    // Second submission (should overwrite)
    echo "<h3>Second submission (should overwrite)...</h3>";
    $secondResult = $prediction->submit($test_user_id, $test_match_id, 'player2', [
        ['player1' => 4, 'player2' => 6],
        ['player1' => 5, 'player2' => 7]
    ]);
    
    if ($secondResult['success']) {
        echo "<p style='color: green;'>✓ Second prediction submitted successfully</p>";
        
        // Check what's in the database now
        $db->query('SELECT * FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
        $db->bind(':user_id', $test_user_id);
        $db->bind(':match_id', $test_match_id);
        $secondPrediction = $db->single();
        
        if ($secondPrediction) {
            echo "<p>Second prediction data: " . $secondPrediction['prediction_data'] . "</p>";
            echo "<p>Second prediction ID: " . $secondPrediction['id'] . "</p>";
            echo "<p>Second prediction created_at: " . $secondPrediction['created_at'] . "</p>";
            
            // Check if it actually overwrote
            if ($secondPrediction['id'] == $firstPrediction['id']) {
                echo "<p style='color: green;'>✓ Prediction was overwritten (same ID)</p>";
                
                $firstData = json_decode($firstPrediction['prediction_data'], true);
                $secondData = json_decode($secondPrediction['prediction_data'], true);
                
                if ($firstData['winner'] !== $secondData['winner']) {
                    echo "<p style='color: green;'>✓ Winner changed from {$firstData['winner']} to {$secondData['winner']}</p>";
                } else {
                    echo "<p style='color: red;'>✗ Winner did not change</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Prediction was not overwritten (different ID)</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ Second prediction failed: " . $secondResult['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ First prediction failed: " . $firstResult['message'] . "</p>";
}

echo "<h2>3. Manual SQL Test</h2>";

// Test the SQL directly
echo "<h3>Testing SQL directly...</h3>";

$testData1 = json_encode(['winner' => 'player1', 'sets' => [['player1' => 6, 'player2' => 4]]]);
$testData2 = json_encode(['winner' => 'player2', 'sets' => [['player1' => 4, 'player2' => 6]]]);

// First insert
$db->query('INSERT INTO predictions (user_id, match_id, prediction_data) 
            VALUES (:user_id, :match_id, :prediction_data)
            ON DUPLICATE KEY UPDATE 
            prediction_data = VALUES(prediction_data)');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$db->bind(':prediction_data', $testData1);

if ($db->execute()) {
    echo "<p style='color: green;'>✓ Manual SQL first insert successful</p>";
    
    // Check result
    $db->query('SELECT prediction_data FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $result1 = $db->single();
    echo "<p>First manual insert data: " . $result1['prediction_data'] . "</p>";
    
    // Second insert (should update)
    $db->query('INSERT INTO predictions (user_id, match_id, prediction_data) 
                VALUES (:user_id, :match_id, :prediction_data)
                ON DUPLICATE KEY UPDATE 
                prediction_data = VALUES(prediction_data)');
    $db->bind(':user_id', $test_user_id);
    $db->bind(':match_id', $test_match_id);
    $db->bind(':prediction_data', $testData2);
    
    if ($db->execute()) {
        echo "<p style='color: green;'>✓ Manual SQL second insert successful</p>";
        
        // Check result
        $db->query('SELECT prediction_data FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
        $db->bind(':user_id', $test_user_id);
        $db->bind(':match_id', $test_match_id);
        $result2 = $db->single();
        echo "<p>Second manual insert data: " . $result2['prediction_data'] . "</p>";
        
        if ($result2['prediction_data'] === $testData2) {
            echo "<p style='color: green;'>✓ Manual SQL overwrite successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Manual SQL overwrite failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Manual SQL second insert failed</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Manual SQL first insert failed</p>";
}

echo "<h2>4. Count Predictions</h2>";

// Count how many predictions exist for this user/match
$db->query('SELECT COUNT(*) as count FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$count = $db->single();

echo "<p>Total predictions for user $test_user_id and match $test_match_id: " . $count['count'] . "</p>";

if ($count['count'] == 1) {
    echo "<p style='color: green;'>✓ Correct number of predictions (should be 1)</p>";
} else {
    echo "<p style='color: red;'>✗ Wrong number of predictions (should be 1, got " . $count['count'] . ")</p>";
}

echo "<h2>5. Cleanup</h2>";

// Clean up test data
$db->query('DELETE FROM predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);

if ($db->execute()) {
    echo "<p style='color: green;'>✓ Test data cleaned up</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to clean up test data</p>";
}

echo "<h2>6. Testing Game Predictions Button State</h2>";

// Test that the button should be enabled when no game predictions exist
echo "<h3>Testing button state with no game predictions...</h3>";

// First, ensure no game predictions exist for this user/match
$db->query('DELETE FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$db->execute();

// Check if any game predictions exist
$db->query('SELECT COUNT(*) as count FROM game_predictions WHERE user_id = :user_id AND match_id = :match_id');
$db->bind(':user_id', $test_user_id);
$db->bind(':match_id', $test_match_id);
$gameCount = $db->single();

if ($gameCount['count'] == 0) {
    echo "<p style='color: green;'>✓ No game predictions exist (button should be enabled)</p>";
} else {
    echo "<p style='color: red;'>✗ Game predictions still exist (button would be disabled)</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>This test will help identify why predictions aren't being overwritten.</p>";
echo "<p>Check the output above to see:</p>";
echo "<ul>";
echo "<li>If the unique constraint exists</li>";
echo "<li>If the SQL is executing correctly</li>";
echo "<li>If the data is actually changing</li>";
echo "<li>If there are multiple records instead of one</li>";
echo "<li>If the game predictions button state is correct</li>";
echo "</ul>";
echo "<p><strong>Note:</strong> The game predictions submit button should now show as 'Save Game Predictions' (enabled) when no predictions exist, and 'Game Predictions Submitted' (disabled) only when predictions actually exist.</p>";
?> 