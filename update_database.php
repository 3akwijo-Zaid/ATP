<?php
require_once 'config/config.php';
require_once 'src/classes/Database.php';

$db = new Database();

echo "Updating database schema...\n";

// Add match_format column if it doesn't exist
try {
    $db->query("ALTER TABLE matches ADD COLUMN match_format ENUM('best_of_3','best_of_5') DEFAULT 'best_of_5'");
    $db->execute();
    echo "✓ Added match_format column to matches table\n";
} catch (Exception $e) {
    echo "ℹ match_format column already exists or error: " . $e->getMessage() . "\n";
}

// Add competition_name column if it doesn't exist
try {
    $db->query("ALTER TABLE matches ADD COLUMN competition_name VARCHAR(100) DEFAULT 'Tennis Tournament'");
    $db->execute();
    echo "✓ Added competition_name column to matches table\n";
} catch (Exception $e) {
    echo "ℹ competition_name column already exists or error: " . $e->getMessage() . "\n";
}

// Update existing matches to have a default format
try {
    $db->query("UPDATE matches SET match_format = 'best_of_5' WHERE match_format IS NULL");
    $db->execute();
    echo "✓ Updated existing matches with default format\n";
} catch (Exception $e) {
    echo "ℹ Error updating existing matches: " . $e->getMessage() . "\n";
}

// Update existing matches to have a default competition name
try {
    $db->query("UPDATE matches SET competition_name = 'Tennis Tournament' WHERE competition_name IS NULL");
    $db->execute();
    echo "✓ Updated existing matches with default competition name\n";
} catch (Exception $e) {
    echo "ℹ Error updating existing matches: " . $e->getMessage() . "\n";
}

echo "\nDatabase update complete!\n";
echo "You can now use the admin panel to update match results and manage users.\n";
?> 