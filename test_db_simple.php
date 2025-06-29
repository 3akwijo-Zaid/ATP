<?php
echo "Testing basic database connectivity...\n";

// Try to connect to MySQL server first
$server_conn = mysqli_connect('localhost', 'root', '');

if (!$server_conn) {
    echo "Failed to connect to MySQL server: " . mysqli_connect_error() . "\n";
    exit;
}

echo "Connected to MySQL server successfully!\n";

// Check if database exists
$result = mysqli_query($server_conn, "SHOW DATABASES LIKE 'tennis_predictions'");
if (mysqli_num_rows($result) > 0) {
    echo "Database 'tennis_predictions' exists!\n";
    
    // Connect to the specific database
    $db_conn = mysqli_connect('localhost', 'root', '', 'tennis_predictions');
    
    if (!$db_conn) {
        echo "Failed to connect to tennis_predictions database: " . mysqli_connect_error() . "\n";
    } else {
        echo "Connected to tennis_predictions database successfully!\n";
        
        // Check if predictions table exists
        $result = mysqli_query($db_conn, "SHOW TABLES LIKE 'predictions'");
        if (mysqli_num_rows($result) > 0) {
            echo "Predictions table exists!\n";
            
            // Show table structure
            $result = mysqli_query($db_conn, "DESCRIBE predictions");
            echo "Predictions table structure:\n";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "- {$row['Field']}: {$row['Type']}\n";
            }
        } else {
            echo "Predictions table does not exist!\n";
        }
        
        mysqli_close($db_conn);
    }
} else {
    echo "Database 'tennis_predictions' does not exist!\n";
    echo "You need to create the database and run the schema.sql file.\n";
}

mysqli_close($server_conn);
?> 