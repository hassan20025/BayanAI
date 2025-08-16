<?php
require_once "users/UserService.php";
require_once __DIR__ . "/../utils/utils.php";
// error_reporting(0);
// ini_set('display_errors', 0);
// ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

require_once 'authAdmin.php'; 

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }

// try {
//     require_once '../db/db.php';

//     $username   = isset($_POST['username']) ? trim($_POST['username']) : trim($_POST['name'] ?? '');
//     $email      = isset($_POST['email'])    ? trim($_POST['email'])    : '';
//     $password   = isset($_POST['password']) ? $_POST['password']       : '';

//     if ($username === '' || $email === '' || $password === '') {
//         echo json_encode(['error' => 'Name, email, and password are required']);
//         ob_end_flush();
//         exit();
//     }

//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         echo json_encode(['error' => 'Invalid email format']);
//         ob_end_flush();
//         exit();
//     }

//     if (strlen($password) < 8) {
//         echo json_encode(['error' => 'Password must be at least 8 characters']);
//         ob_end_flush();
//         exit();
//     }

//     // Duplicate email check
//     if (!($stmt = $db->prepare("SELECT id FROM users WHERE email = ?"))) {
//         echo json_encode(['error' => 'DB prepare failed']);
//         ob_end_flush();
//         exit();
//     }
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $dup = $stmt->get_result();
//     if ($dup && $dup->num_rows > 0) {
//         echo json_encode(['error' => 'Email already exists']);
//         $stmt->close();
//         ob_end_flush();
//         exit();
//     }
//     $stmt->close();

//     // Hash password
//     $hashed = password_hash($password, PASSWORD_DEFAULT);
//     if ($hashed === false) {
//         echo json_encode(['error' => 'Failed to hash password']);
//         ob_end_flush();
//         exit();
//     }

//     // Insert admin (no department for admins)
//     if (!($stmt = $db->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, 'Admin', ?)"))) {
//         echo json_encode(['error' => 'DB prepare failed']);
//         ob_end_flush();
//         exit();
//     }
//     $stmt->bind_param("sss", $username, $email, $hashed);

//     if (!$stmt->execute()) {
//         echo json_encode(['error' => 'Failed to add admin']);
//         $stmt->close();
//         ob_end_flush();
//         exit();
//     }

//     $newId = $db->insert_id;
//     $stmt->close();

//     echo json_encode([
//         'success'  => true,
//         'id'       => $newId,
//         'username' => $username,
//         'email'    => $email,
//         'role'     => 'Admin'
//     ]);

// } catch (Throwable $e) {
//     ob_end_clean();
//     echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
//     exit();
// }

// ob_end_flush();

    $username   = isset($_POST['username']) ? trim($_POST['username']) : trim($_POST['name'] ?? '');
    $email      = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password   = isset($_POST['password']) ? $_POST['password']       : '';

    if ($username === '' || $email === '' || $password === '') {
        respond(400, "error", ["message" => "Missing data."]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(400, "error", ["message" => "Invalid email."]);
    }

    if (strlen($password) < 8) {
        respond(400, "error", ["message" => "Password too short."]);
    }

    $safe_email = escapeshellcmd(htmlspecialchars($email,  ENT_QUOTES, "UTF-8"));
    $safe_password = escapeshellcmd(htmlspecialchars($password,  ENT_QUOTES, "UTF-8"));
    $safe_username = escapeshellcmd(htmlspecialchars($username,  ENT_QUOTES, "UTF-8"));

    respond(201, "success", create_user($safe_username, $safe_email, $safe_password, "admin"));