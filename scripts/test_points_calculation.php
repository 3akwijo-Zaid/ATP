<?php
/**
 * Test script to verify points calculation after saving match results
 * Run this script to test the points calculation functionality
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/classes/Prediction.php';
require_once __DIR__ . '/../src/classes/GamePrediction.php';
require_once __DIR__ . '/../src/classes/StatisticsPrediction.php';
require_once __DIR__ . '/../src/classes/Match.php';

echo "=== Points Calculation Test ===\n\n";

// Initialize classes
$prediction = new Prediction();
$gamePrediction = new GamePrediction();
$statisticsPrediction = new StatisticsPrediction();
$matchManager = new MatchManager();

// Test 1: Check if point settings exist
echo "1. Checking point settings...\n";
$db = new Database();
$db->query('SELECT * FROM point_settings WHERE id = 1');
$settings = $db->single();
if ($settings) {
    echo "   ✓ Point settings found:\n";
    echo "   - Match winner points: " . $settings['match_winner_points'] . "\n";
    echo "   - Match score points: " . $settings['match_score_points'] . "\n";
    echo "   - Set score points: " . $settings['set_score_points'] . "\n";
    echo "   - Game winner points: " . $settings['game_winner_points'] . "\n";
    echo "   - Game score points: " . $settings['game_score_points'] . "\n";
    echo "   - Exact game score points: " . $settings['exact_game_score_points'] . "\n";
} else {
    echo "   ✗ No point settings found!\n";
    exit(1);
}

// Test 2: Check for finished matches with predictions
echo "\n2. Checking for finished matches with predictions...\n";
$db->query('SELECT m.*, COUNT(p.id) as prediction_count 
            FROM matches m 
            LEFT JOIN predictions p ON m.id = p.match_id 
            WHERE m.status = "finished" 
            GROUP BY m.id 
            HAVING prediction_count > 0 
            ORDER BY m.start_time DESC 
            LIMIT 5');
$matches = $db->resultSet();

if (empty($matches)) {
    echo "   ✗ No finished matches with predictions found!\n";
} else {
    echo "   ✓ Found " . count($matches) . " finished matches with predictions:\n";
    foreach ($matches as $match) {
        echo "   - Match ID: " . $match['id'] . " (" . $match['prediction_count'] . " predictions)\n";
    }
}

// Test 3: Test points calculation on a specific match
if (!empty($matches)) {
    $testMatchId = $matches[0]['id'];
    echo "\n3. Testing points calculation for match ID: " . $testMatchId . "\n";
    
    // Get user points before calculation
    $db->query('SELECT u.id, u.username, u.points FROM users u 
                JOIN predictions p ON u.id = p.user_id 
                WHERE p.match_id = :match_id 
                LIMIT 3');
    $db->bind(':match_id', $testMatchId);
    $usersBefore = $db->resultSet();
    
    echo "   User points before calculation:\n";
    foreach ($usersBefore as $user) {
        echo "   - " . $user['username'] . ": " . $user['points'] . " points\n";
    }
    
    // Calculate points
    $result = $prediction->calculatePoints($testMatchId);
    echo "   Points calculation result: " . ($result ? "✓ Success" : "✗ Failed") . "\n";
    
    // Get user points after calculation
    $db->query('SELECT u.id, u.username, u.points FROM users u 
                JOIN predictions p ON u.id = p.user_id 
                WHERE p.match_id = :match_id 
                LIMIT 3');
    $db->bind(':match_id', $testMatchId);
    $usersAfter = $db->resultSet();
    
    echo "   User points after calculation:\n";
    foreach ($usersAfter as $user) {
        echo "   - " . $user['username'] . ": " . $user['points'] . " points\n";
    }
    
    // Check prediction points
    $db->query('SELECT p.*, u.username FROM predictions p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.match_id = :match_id');
    $db->bind(':match_id', $testMatchId);
    $predictions = $db->resultSet();
    
    echo "   Prediction points awarded:\n";
    foreach ($predictions as $pred) {
        echo "   - " . $pred['username'] . ": " . $pred['points_awarded'] . " points\n";
    }
}

// Test 4: Check for any users with negative points (potential issue)
echo "\n4. Checking for users with negative points...\n";
$db->query('SELECT id, username, points FROM users WHERE points < 0');
$negativeUsers = $db->resultSet();

if (empty($negativeUsers)) {
    echo "   ✓ No users with negative points found.\n";
} else {
    echo "   ⚠ Found " . count($negativeUsers) . " users with negative points:\n";
    foreach ($negativeUsers as $user) {
        echo "   - " . $user['username'] . ": " . $user['points'] . " points\n";
    }
}

// Test 5: Check for matches with set results
echo "\n5. Checking for matches with set results...\n";
$db->query('SELECT m.id, m.status, COUNT(ms.id) as set_count 
            FROM matches m 
            LEFT JOIN match_sets ms ON m.id = ms.match_id 
            WHERE m.status = "finished" 
            GROUP BY m.id 
            HAVING set_count > 0 
            ORDER BY m.start_time DESC 
            LIMIT 5');
$matchesWithSets = $db->resultSet();

if (empty($matchesWithSets)) {
    echo "   ✗ No finished matches with set results found!\n";
} else {
    echo "   ✓ Found " . count($matchesWithSets) . " finished matches with set results:\n";
    foreach ($matchesWithSets as $match) {
        echo "   - Match ID: " . $match['id'] . " (" . $match['set_count'] . " sets)\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "If you see any issues above, please check the database and point calculation logic.\n";
?> 