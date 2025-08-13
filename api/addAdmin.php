<?php
// Suppress all output and errors
error_reporting(0);
ini_set('display_errors', 0);
ob_start(); // Start output buffering

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../db/db.php';
    
    // Check if admins table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'admins'");
    if ($tableExists->num_rows == 0) {
        // Create admins table if it doesn't exist
        $createTable = "CREATE TABLE admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$db->query($createTable)) {
            echo json_encode(['error' => 'Failed to create admins table: ' . $db->error]);
            exit;
        }
    }
    
    // Check if required fields are provided
    if (empty($_POST['name']) || empty($_POST['email'])) {
        echo json_encode(['error' => 'Name and email are required']);
        exit;
    }
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }
    
    // Insert new admin
    $stmt = $db->prepare("INSERT INTO admins (name, email) VALUES (?, ?)");

    $stmt->bind_param("ss", $name, $email);
    
    if ($stmt->execute()) {
        $adminId = $db->insert_id;
        echo json_encode([
            'success' => true,
            'id' => $adminId,
            'name' => $name,
            'email' => $email
        ]);
    } else {
        echo json_encode(['error' => 'Failed to add admin']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Ensure no additional output
ob_end_flush();
?> 