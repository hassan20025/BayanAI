<?php
// Suppress all output and errors
error_reporting(0);
ini_set('display_errors', 0);
ob_start(); // Start output buffering

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../db/db.php';
    
    // Check if admins table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'admins'");
    if ($tableExists->num_rows == 0) {
        // Create admins table if it doesn't exist
        $createTable = "CREATE TABLE admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$db->query($createTable)) {
            echo json_encode(['error' => 'Failed to create admins table: ' . $db->error]);
            exit;
        }
        
        // Return empty array if table was just created
        echo json_encode([]);
        exit;
    }
    
    $query = "SELECT id, name, email FROM admins ORDER BY created_at DESC";
    $result = $db->query($query);

    if (!$result) {
        ob_end_clean();
        echo json_encode(['error' => 'Query failed: ' . $db->error]);
        exit;
    }
    
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
    
    // Clear any output buffer and send JSON
    ob_end_clean();
    echo json_encode($admins);
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Ensure no additional output
ob_end_flush();
?> 