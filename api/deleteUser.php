<?php
require_once '../db/db.php'; // adjust path if needed
require_once __DIR__ . "/../utils/utils.php";
$userId = $_POST['id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user ID']);
    exit;
}

$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'User not found or not deleted']);
}
?>
