<?php
session_start();
header('Content-Type: application/json');

// Safer CORS
header('Access-Control-Allow-Origin: http://localhost'); // adjust to your frontend domain
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get current user ID from session
require_once "./sessions/SessionService.php";
require_once "../utils/utils.php";
require_once "./users/UserService.php";

$userId = get_authenticated_user_id();
if (!$userId) {
    respond(401, "error", ["message" => "User not authenticated"]);
}
$user = get_user_by_id($userId);

// Only managers can view stats
if (strtolower($user->getRole()) !== "manager") {
    respond(403, "error", ["message" => "Only managers can perform this action"]);
}


$host = 'localhost';
$dbname = 'bayanai';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure tables exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        manager_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        company_name VARCHAR(255),
        title VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_messages INT DEFAULT 0,
        users_added INT DEFAULT 0,
        failed_responses INT DEFAULT 0
    )");

    // Optional: safety-create sessions table if missing
    $pdo->exec("CREATE TABLE IF NOT EXISTS sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Date windows
    $currentMonth = date('Y-m');
    $lastMonth    = date('Y-m', strtotime('-1 month'));

    // Users (scoped to manager_id)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE manager_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$current_user_id, $currentMonth]);
    $currentUsers = (int) $stmt->fetchColumn();

    $stmt->execute([$current_user_id, $lastMonth]);
    $lastMonthUsers = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE manager_id = ?");
    $stmt->execute([$current_user_id]);
    $totalUsers = (int) $stmt->fetchColumn();

    // Chats (scoped to user_id)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM chats WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$current_user_id, $currentMonth]);
    $currentChats = (int) $stmt->fetchColumn();

    $stmt->execute([$current_user_id, $lastMonth]);
    $lastMonthChats = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM chats WHERE user_id = ?");
    $stmt->execute([$current_user_id]);
    $totalChats = (int) $stmt->fetchColumn();

    // Knowledge base
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$current_user_id, $currentMonth]);
    $kbCurrent = (int) $stmt->fetchColumn();

    $stmt->execute([$current_user_id, $lastMonth]);
    $kbLastMonth = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ?");
    $stmt->execute([$current_user_id]);
    $kbTotal = (int) $stmt->fetchColumn();

    // Sessions
    $sessionsTotal = (int) $pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn();

    // % changes
    $userChange = $lastMonthUsers > 0 ? round((($currentUsers - $lastMonthUsers) / $lastMonthUsers) * 100) : ($currentUsers > 0 ? 100 : 0);
    $chatChange = $lastMonthChats > 0 ? round((($currentChats - $lastMonthChats) / $lastMonthChats) * 100) : ($currentChats > 0 ? 100 : 0);
    $kbChange   = $kbLastMonth > 0 ? round((($kbCurrent - $kbLastMonth) / $kbLastMonth) * 100) : ($kbCurrent > 0 ? 100 : 0);

    echo json_encode([
        'success' => true,
        'users' => [
            'total' => $totalUsers,
            'change_percent' => $userChange,
            'change_direction' => $userChange >= 0 ? 'increase' : 'decrease'
        ],
        'chats' => [
            'total' => $totalChats,
            'change_percent' => $chatChange,
            'change_direction' => $chatChange >= 0 ? 'increase' : 'decrease'
        ],
        'knowledge_base' => [
            'total' => $kbTotal,
            'change_percent' => $kbChange,
            'change_direction' => $kbChange >= 0 ? 'increase' : 'decrease'
        ],
        'sessions' => [
            'total' => $sessionsTotal
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
