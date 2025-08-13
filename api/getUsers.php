<?php
ob_start(); // Start output buffering
require_once '../db/db.php'; // adjust path if needed

$query = "SELECT id, username, email, department, role FROM users";
$result = $db->query($query);

$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

ob_end_clean(); // Clear any output buffer
header('Content-Type: application/json');
echo json_encode($users);
?>
