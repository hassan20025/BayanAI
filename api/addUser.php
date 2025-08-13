<?php
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

    // Basic validation
    if (empty($name) || empty($email) || empty($department) || empty($role)) {
        ob_end_clean(); // Clear any output buffer
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    // Check if email already exists
    $check_stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        ob_end_clean(); // Clear any output buffer
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Insert into users table (with department & role)
    $stmt = $db->prepare("INSERT INTO users (username, email, department, role, password, created_at) VALUES (?, ?, ?, ?, '', NOW())");
    
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $db->error);
    }
    
    $stmt->bind_param("ssss", $name, $email, $department, $role);
    
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
        'role' => $role
    ]);

} catch (Exception $e) {
    ob_end_clean(); // Clear any output buffer
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
