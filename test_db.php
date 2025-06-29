<?php
require_once 'config/config.php';
require_once 'src/classes/Database.php';

echo "Testing database connection...\n";

try {
    $db = new Database();
    echo "Database connection successful!\n";
    
    // Test if predictions table exists
    $db->query("SHOW TABLES LIKE 'predictions'");
    $result = $db->single();
    
    if ($result) {
        echo "Predictions table exists!\n";
        
        // Check table structure
        $db->query("DESCRIBE predictions");
        $columns = $db->resultSet();
        
        echo "Predictions table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']}\n";
        }
        
        // Check if unique constraint exists
        $db->query("SHOW INDEX FROM predictions WHERE Key_name = 'unique_user_match'");
        $index = $db->single();
        
        if ($index) {
            echo "Unique constraint exists!\n";
        } else {
            echo "WARNING: Unique constraint missing!\n";
        }
        
    } else {
        echo "ERROR: Predictions table does not exist!\n";
    }
    
    // Test if we can insert a test record
    echo "\nTesting insert...\n";
    $db->query("INSERT INTO predictions (user_id, match_id, prediction_data) VALUES (1, 1, '{\"test\": true}')");
    if ($db->execute()) {
        echo "Insert test successful!\n";
        
        // Clean up test data
        $db->query("DELETE FROM predictions WHERE user_id = 1 AND match_id = 1");
        $db->execute();
        echo "Test data cleaned up.\n";
    } else {
        echo "Insert test failed!\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?> 