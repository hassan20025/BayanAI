<?php
header("Content-Type: application/json"); 

function respond($statusCode, $status, $data) {
    http_response_code($statusCode);
    echo json_encode([
        "status" => $status,
        "data" => $data
    ]);
    exit;
}