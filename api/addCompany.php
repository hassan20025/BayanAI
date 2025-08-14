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

require_once 'authAdmin.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../db/db.php';
    
    // Check if companies table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'companies'");
    if ($tableExists->num_rows == 0) {
        // Create companies table if it doesn't exist
        $createTable = "CREATE TABLE companies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            industry VARCHAR(100) NOT NULL,
            plan VARCHAR(50) NOT NULL,
            next_due DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$db->query($createTable)) {
            echo json_encode(['error' => 'Failed to create companies table: ' . $db->error]);
            exit;
        }
    }
    
    // Check if required fields are provided
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['industry']) || empty($_POST['plan']) || empty($_POST['next_due'])) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $industry = trim($_POST['industry']);
    $plan = trim($_POST['plan']);
    $nextDue = trim($_POST['next_due']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM companies WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }
    
    // Insert new company
    $stmt = $db->prepare("INSERT INTO companies (name, email, industry, plan, next_due) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $industry, $plan, $nextDue);
    
    if ($stmt->execute()) {
        $companyId = $db->insert_id;
        echo json_encode([
            'success' => true,
            'id' => $companyId,
            'name' => $name,
            'email' => $email,
            'industry' => $industry,
            'plan' => $plan,
            'next_due' => $nextDue
        ]);
    } else {
        echo json_encode(['error' => 'Failed to add company: ' . $db->error]);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

ob_end_flush();
?> 