<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Available PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";

if (extension_loaded('pdo_mysql')) {
    echo "PDO MySQL extension is loaded!\n";
} else {
    echo "PDO MySQL extension is NOT loaded!\n";
}

if (extension_loaded('mysqli')) {
    echo "MySQLi extension is loaded!\n";
} else {
    echo "MySQLi extension is NOT loaded!\n";
}

// Try to connect using mysqli as fallback
if (extension_loaded('mysqli')) {
    echo "\nTrying MySQLi connection...\n";
    $mysqli = new mysqli('localhost', 'root', '', 'tennis_predictions');
    
    if ($mysqli->connect_error) {
        echo "MySQLi connection failed: " . $mysqli->connect_error . "\n";
    } else {
        echo "MySQLi connection successful!\n";
        
        // Check if predictions table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'predictions'");
        if ($result && $result->num_rows > 0) {
            echo "Predictions table exists!\n";
        } else {
            echo "Predictions table does not exist!\n";
        }
        
        $mysqli->close();
    }
}
?> 