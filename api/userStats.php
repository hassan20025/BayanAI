<?php
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// ini_set('display_errors', 0);

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

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

// DB connection (mysqli)
require_once "../db/db.php"; // gives you $db (mysqli)

$userId = get_authenticated_user_id();
if (!$userId) {
    respond(401, "error", ["message" => "User not authenticated"]);
}
$user = get_user_by_id($userId);

// Only managers can view stats
if (strtolower($user->getRole()) !== "manager") {
    respond(403, "error", ["message" => "Only managers can perform this action"]);
}

// Make sure we have a valid mysqli connection
if ($db->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $db->connect_error
    ]);
    exit();
}

// Safety-create tables if missing
$db->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$db->query("CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    company_name VARCHAR(255),
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_messages INT DEFAULT 0,
    users_added INT DEFAULT 0,
    failed_responses INT DEFAULT 0
)");

$db->query("CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Date windows
$currentMonth = date('Y-m');
$lastMonth    = date('Y-m', strtotime('-1 month'));

// === Users (scoped to manager_id) ===
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE manager_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $currentMonth);
$stmt->execute();
$stmt->bind_result($currentUsers);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE manager_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $lastMonth);
$stmt->execute();
$stmt->bind_result($lastMonthUsers);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE manager_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($totalUsers);
$stmt->fetch();
$stmt->close();

// === Chats (scoped to user_id) ===
$stmt = $db->prepare("SELECT COUNT(*) FROM chats WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $currentMonth);
$stmt->execute();
$stmt->bind_result($currentChats);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM chats WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $lastMonth);
$stmt->execute();
$stmt->bind_result($lastMonthChats);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM chats WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($totalChats);
$stmt->fetch();
$stmt->close();

// === Knowledge base ===
$stmt = $db->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $currentMonth);
$stmt->execute();
$stmt->bind_result($kbCurrent);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param("is", $userId, $lastMonth);
$stmt->execute();
$stmt->bind_result($kbLastMonth);
$stmt->fetch();
$stmt->close();

$stmt = $db->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($kbTotal);
$stmt->fetch();
$stmt->close();

// === Sessions (all) ===
$res = $db->query("SELECT COUNT(*) AS c FROM sessions");
$row = $res->fetch_assoc();
$sessionsTotal = (int)$row['c'];

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
