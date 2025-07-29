<?php
    header('Content-Type: application/json'); 
    function respond($statusCode, $status, $data) {
        http_response_code($statusCode);

        echo json_encode([
            'status' => $status,
            'data' => $data
        ]);
        exit;
    }

    function validateEmail ($email) {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }