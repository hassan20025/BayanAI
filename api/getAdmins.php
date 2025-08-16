<?php
// Quiet output and set JSON/CORS headers
// error_reporting(0);
// ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

require_once 'authAdmin.php';


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    require_once '../db/db.php'; 


    $sql = "SELECT id, username AS name, email
            FROM users
            WHERE role = 'Admin'
            ORDER BY id DESC"; 

    $res = $db->query($sql);
    if (!$res) {
        echo json_encode(['error' => 'Query failed: ' . $db->error]);
        exit;
    }

    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }

    echo json_encode($rows);
} catch (Throwable $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
