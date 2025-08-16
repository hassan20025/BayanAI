<?php
require_once "./sessions/SessionService.php";
require_once "../utils/utils.php";
require_once "./users/UserService.php";

// Start/continue the session
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     session_start();
// }

// // Not logged in
// if (empty($_SESSION['user_id'])) {
//     http_response_code(401);
//     header('Content-Type: application/json');
//     echo json_encode(['error' => 'Unauthorized']);
//     exit();
// }

// require_once '../db/db.php';

// // Look up the current user’s role
// $stmt = $db->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
// $stmt->bind_param("i", $_SESSION['user_id']);
// $stmt->execute();
// $res = $stmt->get_result();
// $user = $res->fetch_assoc();
// $stmt->close();

// if (!$user) {
//     http_response_code(401);
//     header('Content-Type: application/json');
//     echo json_encode(['error' => 'Unauthorized']);
//     exit();
// }

// // Only Admins may proceed
// if ($user['role'] !== 'Admin') {
//     http_response_code(403);
//     header('Content-Type: application/json');
//     echo json_encode(['error' => 'Forbidden']);
//     exit();
// }

$userId = get_authenticated_user_id();
$user  = get_user_by_id($userId);

if ($user->getRole() != "admin") {
    respond(403, "error", ["message" => "Only admins can perform this action"]);
}

// Optional CSRF check (recommended for cookie‑based sessions)
// if (empty($_SERVER['HTTP_X_CSRF_TOKEN']) || empty($_SESSION['csrf_token']) ||
//     !hash_equals($_SESSION['csrf_token'], $_SERVER['HTTP_X_CSRF_TOKEN'])) {
//     respond(419, "error", ["message" => "Invalid CSRF token"]);
// }
