<?php
header('Content-Type: application/json');

require_once '../db/db.php';

$stmt = $db->prepare("SELECT id, username, email, department, role FROM users ORDER BY id");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    'count' => count($users),
    'users' => $users
]);

$stmt->close();
?> 