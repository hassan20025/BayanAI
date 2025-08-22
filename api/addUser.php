<?php
require_once 'authUser.php'; // Check user authentication first
// Start output buffering to prevent any unwanted output
ob_start();

header('Content-Type: application/json');

try {
    require_once '../db/db.php'; 

    // Get POST data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';
    $role = $_POST['role'] ?? 'User'; // default to User
    $password = $_POST['password'] ?? '';
    $can_upload = isset($_POST['can_upload']) && $_POST['can_upload'] == 1 ? 1 : 0;


    // Basic validation
    if (empty($name) || empty($email) || empty($department) || empty($role) || empty($password)) {
        ob_end_clean(); // Clear any output buffer
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['error' => 'Password must be at least 8 characters']);
        ob_end_flush();
        exit();
    }

    // Check if email already exists
    $check_stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        ob_end_clean(); // Clear any output buffer
        echo json_encode(['error' => 'Email already exists']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Check if username already exists
    $check_stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        ob_end_clean(); // Clear any output buffer
        echo json_encode(['error' => 'Username already exists']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);
if ($hashed === false) {
    echo json_encode(['error' => 'Failed to hash password']);
    ob_end_flush();
    exit();
}

// Prepare INSERT with manager_id
$stmt = $db->prepare("INSERT INTO users 
    (username, email, department, role, password, can_upload, manager_id, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

$stmt->bind_param("sssssii", 
    $name, 
    $email, 
    $department, 
    $role, 
    $hashed, 
    $can_upload, 
    $userId 
);

if (!$stmt) {
    throw new Exception("Database prepare failed: " . $db->error);
}

if (!$stmt->execute()) {
    throw new Exception("Database execute failed: " . $stmt->error);
}

$user_id = $stmt->insert_id;
$stmt->close();


    // Clear any output buffer before sending JSON
    ob_end_clean();
    
    // Send back new row data
    echo json_encode([
        'success' => true,
        'id' => $user_id,
        'name' => $name,
        'email' => $email,
        'department' => $department,
        'role' => $role,
        'password' => $password,
        'can_upload' => $can_upload
    ]);
    
    

} catch (Exception $e) {
    ob_end_clean(); // Clear any output buffer
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
