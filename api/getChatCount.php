<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'bayanai';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if chats table exists, if not create it
    $checkTable = $pdo->query("SHOW TABLES LIKE 'chats'");
    if ($checkTable->rowCount() == 0) {
        $createTable = "CREATE TABLE chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            company_name VARCHAR(255),
            title VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_messeges INT DEFAULT 0,
            users_added INT DEFAULT 0,
            failed_responses INT DEFAULT 0
        )";
        $pdo->exec($createTable);
    }
    
    // Get total count of chats
    $stmt = $pdo->query("SELECT COUNT(*) as total_chats FROM chats");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'total_chats' => (int)$result['total_chats']
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 