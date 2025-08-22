<?php
require_once 'authUser.php'; 
ob_start();
require_once '../db/db.php';

// Manager’s own id
$currentUserId = $userId;

// Get only subordinates of this manager
$stmt = $db->prepare("SELECT id, username, email, department, role, can_upload
                      FROM users 
                      WHERE manager_id = ?");

$stmt->bind_param("i", $currentUserId);
$stmt->execute();

$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
ob_end_clean();

header('Content-Type: application/json');
echo json_encode($users);
?>
