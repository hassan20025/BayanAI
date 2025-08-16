<?php
// Start output buffering to prevent any unwanted output
// ob_start();

require_once "users/UserService.php";
require_once __DIR__ . "/../utils/utils.php";

header('Content-Type: application/json');

// try {
//     require_once '../db/db.php'; 

//     // Get POST data
//     $name = $_POST['name'] ?? '';
//     $email = $_POST['email'] ?? '';
//     $department = $_POST['department'] ?? '';
//     $role = $_POST['role'] ?? 'User'; // default to User

//     // Basic validation
//     if (empty($name) || empty($email) || empty($department) || empty($role)) {
//         ob_end_clean(); // Clear any output buffer
//         http_response_code(400);
//         echo json_encode(['error' => 'All fields are required']);
//         exit;
//     }

//     // Check if email already exists
//     $check_stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
//     $check_stmt->bind_param("s", $email);
//     $check_stmt->execute();
//     $result = $check_stmt->get_result();
    
//     if ($result->num_rows > 0) {
//         ob_end_clean(); // Clear any output buffer
//         http_response_code(400);
//         echo json_encode(['error' => 'Email already exists']);
//         $check_stmt->close();
//         exit;
//     }
//     $check_stmt->close();

//     // Insert into users table (with department & role)
//     $stmt = $db->prepare("INSERT INTO users (username, email, department, role, password, created_at) VALUES (?, ?, ?, ?, '', NOW())");
    
//     if (!$stmt) {
//         throw new Exception("Database prepare failed: " . $db->error);
//     }
    
//     $stmt->bind_param("ssss", $name, $email, $department, $role);
    
//     if (!$stmt->execute()) {
//         throw new Exception("Database execute failed: " . $stmt->error);
//     }

//     $user_id = $stmt->insert_id;
//     $stmt->close();

//     // Clear any output buffer before sending JSON
//     ob_end_clean();
    
//     // Send back new row data
//     echo json_encode([
//         'success' => true,
//         'id' => $user_id,
//         'name' => $name,
//         'email' => $email,
//         'department' => $department,
//         'role' => $role
//     ]);

// } catch (Exception $e) {
//     ob_end_clean(); // Clear any output buffer
//     http_response_code(500);
//     echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
// }

$username   = isset($_POST['username']) ? trim($_POST['username']) : trim($_POST['name'] ?? '');
$email      = isset($_POST['email'])    ? trim($_POST['email'])    : '';
$password   = isset($_POST['password']) ? $_POST['password']       : '';
$department = isset($_POST['department']) ? trim($_POST["department"]) : null;
$role = isset($_POST['role']) ? strtolower(trim($_POST["role"])) : 'user';
$can_upload = isset($_POST["can_upload"]) ?? null;
$allowed_roles = ["manager", "admin", "user"];

if ($username === '' || $email === '' || $password === '') {
    respond(400, "error", ["message" => "Missing data."]);
}

if (!in_array($role, $allowed_roles)) {
    respond(400, "error", ["message" => "Invalid role."]);
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
$safe_department = escapeshellcmd(htmlspecialchars($username,  ENT_QUOTES, "UTF-8"));
$safe_role = escapeshellcmd(htmlspecialchars($role, ENT_QUOTES, "UTF-8"));
$safe_can_upload = escapeshellcmd(htmlspecialchars($can_upload, ENT_QUOTES, "UTF-8"));

respond(201, "success", create_user($safe_username, $safe_email, $safe_password, $safe_role, (bool) $safe_can_upload, $safe_department));