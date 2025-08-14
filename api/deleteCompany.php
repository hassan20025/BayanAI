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
        echo json_encode(['error' => 'Companies table does not exist']);
        exit;
    }
    
    // Check if company ID is provided
    if (empty($_POST['id'])) {
        echo json_encode(['error' => 'Company ID is required']);
        exit;
    }
    
    $companyId = intval($_POST['id']);
    
    // Check if company exists
    $stmt = $db->prepare("SELECT id FROM companies WHERE id = ?");
    $stmt->bind_param("i", $companyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(['error' => 'Company not found']);
        exit;
    }
    
    // Delete the company
    $stmt = $db->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->bind_param("i", $companyId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Company deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete company: ' . $db->error]);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

ob_end_flush();
?> 