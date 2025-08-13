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
    
    // Check if users table exists, if not create it
    $checkUsersTable = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($checkUsersTable->rowCount() == 0) {
        $createUsersTable = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($createUsersTable);
    }
    
    // Check if chats table exists, if not create it
    $checkChatsTable = $pdo->query("SHOW TABLES LIKE 'chats'");
    if ($checkChatsTable->rowCount() == 0) {
        $createChatsTable = "CREATE TABLE chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            company_name VARCHAR(255),
            title VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_messeges INT DEFAULT 0,
            users_added INT DEFAULT 0,
            failed_responses INT DEFAULT 0
        )";
        $pdo->exec($createChatsTable);
    }
    
    // Get current month and last month dates
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));
    
    // Get user statistics
    $currentUsers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch(PDO::FETCH_ASSOC);
    $lastMonthUsers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC);
    
    // Get chat statistics
    $currentChats = $pdo->query("SELECT COUNT(*) as count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch(PDO::FETCH_ASSOC);
    $lastMonthChats = $pdo->query("SELECT COUNT(*) as count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch(PDO::FETCH_ASSOC);
    $totalChats = $pdo->query("SELECT COUNT(*) as count FROM chats")->fetch(PDO::FETCH_ASSOC);
    
    // Calculate percentage changes
    $userChange = 0;
    if ($lastMonthUsers['count'] > 0) {
        $userChange = round((($currentUsers['count'] - $lastMonthUsers['count']) / $lastMonthUsers['count']) * 100);
    } else if ($currentUsers['count'] > 0) {
        $userChange = 100; // If no users last month but users this month, it's 100% increase
    }
    
    // Ensure user percentage is always positive (growth)
    $userChange = max(0, $userChange);
    
    $chatChange = 0;
    if ($lastMonthChats['count'] > 0) {
        $chatChange = round((($currentChats['count'] - $lastMonthChats['count']) / $lastMonthChats['count']) * 100);
    } else if ($currentChats['count'] > 0) {
        $chatChange = 100; // If no chats last month but chats this month, it's 100% increase
    }
    
    // Ensure chat percentage is always positive (growth)
    $chatChange = max(0, $chatChange);
    
    echo json_encode([
        'success' => true,
        'users' => [
            'total' => (int)$totalUsers['count'],
            'change_percent' => $userChange,
            'change_direction' => 'increase' // Always show increase for users
        ],
        'chats' => [
            'total' => (int)$totalChats['count'],
            'change_percent' => $chatChange,
            'change_direction' => 'increase' // Always show increase for chats
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 