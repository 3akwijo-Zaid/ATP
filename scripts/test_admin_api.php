<?php
/**
 * Test script to verify admin API response format
 * This script tests the admin API endpoints to ensure they return proper JSON responses
 */

// Test configuration
$baseUrl = 'http://localhost/ATP/api/admin.php';

// Test cases
$tests = [
    [
        'name' => 'Test unauthorized access',
        'action' => 'get_all_users',
        'method' => 'GET',
        'data' => null,
        'expected_success' => false
    ],
    [
        'name' => 'Test invalid action',
        'action' => 'invalid_action',
        'method' => 'GET',
        'data' => null,
        'expected_success' => false
    ],
    [
        'name' => 'Test missing data for update_result',
        'action' => 'update_result',
        'method' => 'POST',
        'data' => ['invalid' => 'data'],
        'expected_success' => false
    ],
    [
        'name' => 'Test missing match_id for recalculate_points',
        'action' => 'recalculate_points',
        'method' => 'POST',
        'data' => ['other_field' => 'value'],
        'expected_success' => false
    ]
];

echo "=== Admin API Response Format Test ===\n\n";

foreach ($tests as $test) {
    echo "Testing: " . $test['name'] . "\n";
    
    // Prepare the request
    $url = $baseUrl . '?action=' . $test['action'];
    $options = [
        'http' => [
            'method' => $test['method'],
            'header' => 'Content-Type: application/json',
            'content' => $test['data'] ? json_encode($test['data']) : null
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "  ✗ Failed to get response\n";
        continue;
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    if ($data === null) {
        echo "  ✗ Invalid JSON response: " . $response . "\n";
        continue;
    }
    
    // Check if response has success field
    if (!isset($data['success'])) {
        echo "  ✗ Response missing 'success' field: " . $response . "\n";
        continue;
    }
    
    // Check if success matches expected
    if ($data['success'] === $test['expected_success']) {
        echo "  ✓ Success field correct (" . ($data['success'] ? 'true' : 'false') . ")\n";
    } else {
        echo "  ✗ Success field incorrect. Expected: " . ($test['expected_success'] ? 'true' : 'false') . 
             ", Got: " . ($data['success'] ? 'true' : 'false') . "\n";
    }
    
    // Check if response has message field
    if (!isset($data['message'])) {
        echo "  ✗ Response missing 'message' field\n";
        continue;
    }
    
    echo "  ✓ Message: " . $data['message'] . "\n";
    echo "\n";
}

echo "=== Test Complete ===\n";
echo "Note: These tests check the response format, not the actual functionality.\n";
echo "For full testing, you would need to be logged in as an admin user.\n";
?> 