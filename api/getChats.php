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
    
    // Check if chats table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'chats'");
    if ($tableExists->num_rows == 0) {
        // Create chats table if it doesn't exist
        $createTable = "CREATE TABLE chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_messeges INT DEFAULT 0,
            users_added INT DEFAULT 0,
            failed_responses INT DEFAULT 0
        )";
        
        if (!$db->query($createTable)) {
            echo json_encode(['error' => 'Failed to create chats table: ' . $db->error]);
            exit;
        }
        
        // Return empty array if table was just created
        echo json_encode([]);
        exit;
    }
    
    // Get all chats
    $result = $db->query("SELECT id, user_id, company_name, title, created_at, total_messeges, users_added, failed_responses FROM chats ORDER BY created_at DESC");
    
    if (!$result) {
        echo json_encode(['error' => 'Failed to fetch chats: ' . $db->error]);
        exit;
    }
    
    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chats[] = [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'company_name' => $row['company_name'],
            'title' => $row['title'],
            'created_at' => $row['created_at'],
            'total_messeges' => $row['total_messeges'],
            'users_added' => $row['users_added'],
            'failed_responses' => $row['failed_responses']
        ];
    }
    
    echo json_encode($chats);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

ob_end_flush();
?> 