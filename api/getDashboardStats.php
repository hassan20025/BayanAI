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

$host = 'localhost';
$dbname = 'bayanai';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // (Your existing safety-creates for users/chats)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS chats (
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
    $currentUsers  = $pdo->query("SELECT COUNT(*) AS count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch(PDO::FETCH_ASSOC);
    $lastMonthUsers= $pdo->query("SELECT COUNT(*) AS count FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch(PDO::FETCH_ASSOC);
    $totalUsers    = $pdo->query("SELECT COUNT(*) AS count FROM users")->fetch(PDO::FETCH_ASSOC);

    // Chats
    $currentChats  = $pdo->query("SELECT COUNT(*) AS count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'")->fetch(PDO::FETCH_ASSOC);
    $lastMonthChats= $pdo->query("SELECT COUNT(*) AS count FROM chats WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'")->fetch(PDO::FETCH_ASSOC);
    $totalChats    = $pdo->query("SELECT COUNT(*) AS count FROM chats")->fetch(PDO::FETCH_ASSOC);

    // NEW: Knowledge base (document_chunks) + Sessions
    $kbTotal       = $pdo->query("SELECT COUNT(*) AS count FROM document_chunks")->fetch(PDO::FETCH_ASSOC);
    $sessionsTotal = $pdo->query("SELECT COUNT(*) AS count FROM sessions")->fetch(PDO::FETCH_ASSOC);
    // If you only want *active* sessions and you have a status column, use:
    // $sessionsTotal = $pdo->query("SELECT COUNT(*) AS count FROM sessions WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC);

    // Percent changes (same logic you had)
    $userChange = 0;
    if ($lastMonthUsers['count'] > 0) {
        $userChange = round((($currentUsers['count'] - $lastMonthUsers['count']) / $lastMonthUsers['count']) * 100);
    } else if ($currentUsers['count'] > 0) {
        $userChange = 100;
    }
    $userChange = max(0, $userChange);

    $chatChange = 0;
    if ($lastMonthChats['count'] > 0) {
        $chatChange = round((($currentChats['count'] - $lastMonthChats['count']) / $lastMonthChats['count']) * 100);
    } else if ($currentChats['count'] > 0) {
        $chatChange = 100;
    }
    $chatChange = max(0, $chatChange);

    echo json_encode([
        'success' => true,
        'users' => [
            'total' => (int)$totalUsers['count'],
            'change_percent' => $userChange,
            'change_direction' => 'increase'
        ],
        'chats' => [
            'total' => (int)$totalChats['count'],
            'change_percent' => $chatChange,
            'change_direction' => 'increase'
        ],
        // NEW blocks consumed by #card-kb and #card-sessions
        'knowledge_base' => [
            'total' => (int)$kbTotal['count']
        ],
        'sessions' => [
            'total' => (int)$sessionsTotal['count']
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
