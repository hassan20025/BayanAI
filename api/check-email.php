<?php
header('Content-Type: application/json');

require_once '../db/db.php';

$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(['error' => 'Email parameter required']);
    exit;
}

$stmt = $db->prepare("SELECT id, username, email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'exists' => true,
        'user' => $user
    ]);
} else {
    echo json_encode([
        'exists' => false
    ]);
}

$stmt->close();
?> 