<?php
ob_start(); // Start output buffering
header('Content-Type: application/json');
require_once 'authAdmin.php';

try {
    require_once '../db/db.php';
    
    // Check if ID is provided
    if (empty($_POST['id'])) {
        echo json_encode(['error' => 'Admin ID is required']);
        exit;
    }
    
    $adminId = (int)$_POST['id'];
    
    // Check if admin exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Admin not found']);
        exit;
    }
    
    // Delete admin
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete admin']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 