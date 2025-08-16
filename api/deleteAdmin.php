<?php
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . "/../utils/utils.php";
require_once 'authAdmin.php';

try {
    require_once '../db/db.php';
    
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        respond(405, "error", "Method unsupported");
    }
    $id = $_POST['id'] ?? null;
    if (!$id) {
        respond(400, "error", "Missing data.");
    }
    $safe_admin_id = escapeshellcmd(htmlspecialchars($id));
    if (empty($safe_admin_id)) {
        echo json_encode(['error' => 'Admin ID is required']);
        exit;
    }
    
    $adminId = (int)$safe_admin_id;
    
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