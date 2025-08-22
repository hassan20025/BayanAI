<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ this gives you $db (mysqli)
require_once '../db/db.php';

try {
    // (Your existing safety-creates for users/chats)
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->query("CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        company_name VARCHAR(255),
        title VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_messeges INT DEFAULT 0,
        users_added INT DEFAULT 0,
        failed_responses INT DEFAULT 0
    )");

    // Month windows
    $currentMonth = date('Y-m');
    $lastMonth    = date('Y-m', strtotime('-1 month'));

    // Users
    $currentUsers   = $db->query("SELECT COUNT(*) AS count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch_assoc();
    $lastMonthUsers = $db->query("SELECT COUNT(*) AS count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch_assoc();
    $totalUsers     = $db->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc();

    // Chats
    $currentChats   = $db->query("SELECT COUNT(*) AS count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch_assoc();
    $lastMonthChats = $db->query("SELECT COUNT(*) AS count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch_assoc();
    $totalChats     = $db->query("SELECT COUNT(*) AS count FROM chats")->fetch_assoc();

    // Knowledge base + Sessions
    $kbTotal       = $db->query("SELECT COUNT(*) AS count FROM document_chunks")->fetch_assoc();
    $sessionsTotal = $db->query("SELECT COUNT(*) AS count FROM sessions")->fetch_assoc();

    // Percent changes
    $userChange = 0;
    if ($lastMonthUsers['count'] > 0) {
        $userChange = round((($currentUsers['count'] - $lastMonthUsers['count']) / $lastMonthUsers['count']) * 100);
    } else if ($currentUsers['count'] > 0) {
        $userChange = 100;
    }

    $chatChange = 0;
    if ($lastMonthChats['count'] > 0) {
        $chatChange = round((($currentChats['count'] - $lastMonthChats['count']) / $lastMonthChats['count']) * 100);
    } else if ($currentChats['count'] > 0) {
        $chatChange = 100;
    }

    echo json_encode([
        'success' => true,
        'users' => [
            'total' => (int)$totalUsers['count'],
            'change_percent' => $userChange,
            'change_direction' => $userChange >= 0 ? 'increase' : 'decrease'
        ],
        'chats' => [
            'total' => (int)$totalChats['count'],
            'change_percent' => $chatChange,
            'change_direction' => $chatChange >= 0 ? 'increase' : 'decrease'
        ],
        'knowledge_base' => [
            'total' => (int)$kbTotal['count']
        ],
        'sessions' => [
            'total' => (int)$sessionsTotal['count']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
