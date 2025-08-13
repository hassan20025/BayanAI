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
    
    // Check if companies table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'companies'");
    if ($tableExists->num_rows == 0) {
        // Create companies table if it doesn't exist
        $createTable = "CREATE TABLE companies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            industry VARCHAR(100) NOT NULL,
            plan VARCHAR(50) NOT NULL,
            next_due DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$db->query($createTable)) {
            echo json_encode(['error' => 'Failed to create companies table: ' . $db->error]);
            exit;
        }
        
        // Return empty array if table was just created
        echo json_encode([]);
        exit;
    }
    
    // Get all companies
    $result = $db->query("SELECT id, name, email, industry, plan, next_due FROM companies ORDER BY created_at DESC");
    
    if (!$result) {
        echo json_encode(['error' => 'Failed to fetch companies: ' . $db->error]);
        exit;
    }
    
    $companies = [];
    while ($row = $result->fetch_assoc()) {
        $companies[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'industry' => $row['industry'],
            'plan' => $row['plan'],
            'next_due' => $row['next_due']
        ];
    }
    
    echo json_encode($companies);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

ob_end_flush();
?> 