<?php
/**
 * Debug script to check why points aren't being calculated for retired match 26
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/classes/Prediction.php';
require_once __DIR__ . '/../src/classes/Match.php';

echo "Debugging retired match 26...\n\n";

$matchId = 26;
$prediction = new Prediction();
$matchManager = new MatchManager();

// Check match status
echo "1. Checking match status:\n";
$match = $matchManager->getMatchById($matchId);
if ($match) {
    echo "   Match ID: " . $match['id'] . "\n";
    echo "   Status: " . $match['status'] . "\n";
    echo "   Winner ID: " . ($match['winner_id'] ?? 'NULL') . "\n";
    echo "   Result Summary: " . ($match['result_summary'] ?? 'NULL') . "\n";
} else {
    echo "   Match not found!\n";
}

// Check if match is considered retired
echo "\n2. Checking if match is considered retired:\n";
$isRetired = false;
if ($match && isset($match['status']) && 
    ($match['status'] === 'retired_player1' || $match['status'] === 'retired_player2')) {
    $isRetired = true;
}
echo "   Is Retired: " . ($isRetired ? 'YES' : 'NO') . "\n";

// Check predictions
echo "\n3. Checking predictions:\n";
$predictions = $prediction->getMatchPredictions($matchId);
echo "   Number of predictions: " . count($predictions) . "\n";
foreach ($predictions as $pred) {
    echo "   - User ID: " . $pred['user_id'] . ", Points: " . $pred['points_awarded'] . "\n";
    $predData = json_decode($pred['prediction_data'], true);
    echo "     Winner: " . $predData['winner'] . "\n";
}

// Check match sets
echo "\n4. Checking match sets:\n";
$actualSets = $matchManager->getMatchSets($matchId);
echo "   Number of sets: " . count($actualSets) . "\n";
foreach ($actualSets as $set) {
    echo "   - Set " . $set['set_number'] . ": " . $set['player1_games'] . "-" . $set['player2_games'] . "\n";
}

// Check point settings
echo "\n5. Checking point settings:\n";
$db = new Database();
$db->query('SELECT * FROM point_settings WHERE id = 1');
$settings = $db->single();
if ($settings) {
    echo "   Match Winner Points: " . $settings['match_winner_points'] . "\n";
    echo "   Set Score Points: " . $settings['set_score_points'] . "\n";
    echo "   Match Score Points: " . $settings['match_score_points'] . "\n";
} else {
    echo "   No point settings found!\n";
}

// Try to calculate points manually
echo "\n6. Trying to calculate points:\n";
$result = $prediction->calculatePoints($matchId);
echo "   Calculate Points Result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

// Check predictions again after calculation
echo "\n7. Checking predictions after calculation:\n";
$predictionsAfter = $prediction->getMatchPredictions($matchId);
foreach ($predictionsAfter as $pred) {
    echo "   - User ID: " . $pred['user_id'] . ", Points: " . $pred['points_awarded'] . "\n";
}

echo "\nDebug complete!\n";
?> 