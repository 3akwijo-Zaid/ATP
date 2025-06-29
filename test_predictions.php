<?php
// Test file to verify prediction system functionality
require_once 'config/config.php';
require_once 'src/classes/Prediction.php';
require_once 'src/classes/GamePrediction.php';
require_once 'src/classes/Match.php';

echo "<h1>Prediction System Test</h1>";

// Test 1: Check if classes can be instantiated
echo "<h2>Test 1: Class Instantiation</h2>";
try {
    $prediction = new Prediction();
    $gamePrediction = new GamePrediction();
    $matchManager = new MatchManager();
    echo "✓ All classes instantiated successfully<br>";
} catch (Exception $e) {
    echo "✗ Error instantiating classes: " . $e->getMessage() . "<br>";
}

// Test 2: Check database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $db = new Database();
    echo "✓ Database connection successful<br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check if tables exist
echo "<h2>Test 3: Database Tables</h2>";
try {
    $db = new Database();
    
    $tables = ['predictions', 'game_predictions', 'matches', 'match_sets', 'users', 'players', 'tournaments'];
    
    foreach ($tables as $table) {
        $db->query("SHOW TABLES LIKE '$table'");
        $result = $db->single();
        if ($result) {
            echo "✓ Table '$table' exists<br>";
        } else {
            echo "✗ Table '$table' missing<br>";
        }
    }
} catch (Exception $e) {
    echo "✗ Error checking tables: " . $e->getMessage() . "<br>";
}

// Test 4: Check if there are any matches
echo "<h2>Test 4: Available Matches</h2>";
try {
    $matchManager = new MatchManager();
    $matches = $matchManager->getMatches();
    echo "✓ Found " . count($matches) . " matches in database<br>";
    
    if (count($matches) > 0) {
        echo "Sample match: " . $matches[0]['player1_name'] . " vs " . $matches[0]['player2_name'] . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Error loading matches: " . $e->getMessage() . "<br>";
}

// Test 5: Check if there are any users
echo "<h2>Test 5: Available Users</h2>";
try {
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM users");
    $result = $db->single();
    echo "✓ Found " . $result['count'] . " users in database<br>";
} catch (Exception $e) {
    echo "✗ Error loading users: " . $e->getMessage() . "<br>";
}

// Test 6: Check point settings
echo "<h2>Test 6: Point Settings</h2>";
try {
    $db = new Database();
    $db->query("SELECT * FROM point_settings WHERE id = 1");
    $settings = $db->single();
    if ($settings) {
        echo "✓ Point settings found:<br>";
        echo "- Match winner points: " . $settings['match_winner_points'] . "<br>";
        echo "- Set winner points: " . $settings['set_winner_points'] . "<br>";
        echo "- Set score points: " . $settings['set_score_points'] . "<br>";
        if (isset($settings['game_winner_points'])) {
            echo "- Game winner points: " . $settings['game_winner_points'] . "<br>";
            echo "- Game score points: " . $settings['game_score_points'] . "<br>";
            echo "- Exact game score points: " . $settings['exact_game_score_points'] . "<br>";
        }
    } else {
        echo "✗ No point settings found<br>";
    }
} catch (Exception $e) {
    echo "✗ Error loading point settings: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If all tests pass, the prediction system should be working correctly.</p>";
echo "<p><a href='public/index.php'>Go to Homepage</a> | <a href='admin/dashboard.php'>Go to Admin Panel</a></p>";
?> 