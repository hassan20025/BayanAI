<?php
// api/uploadFile.php
// Force clean JSON responses
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); ob_end_clean(); exit; }
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    require_once 'authUser.php'; 
    require_once '../db/db.php';   

    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded (field name "file").');
    }
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error code: ' . $_FILES['file']['error']);
    }

    $tmp  = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    $size = (int)$_FILES['file']['size'];

    // Detect MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($tmp) ?: ($_FILES['file']['type'] ?? 'application/octet-stream');

    // Save file to uploads directory
    $dir = dirname(__DIR__) . '/uploads/knowledge';
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        throw new Exception('Cannot create upload dir.');
    }
    $base   = pathinfo($name, PATHINFO_FILENAME);
    $ext    = pathinfo($name, PATHINFO_EXTENSION);
    $safe   = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $base);
    $stored = $safe . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
    if (!move_uploaded_file($tmp, "$dir/$stored")) {
        throw new Exception('Failed to move uploaded file.');
    }

    // Insert metadata into DB (with user_id)
    $stmt = $db->prepare("INSERT INTO document_chunks (file_name, file_type, size, user_id, created_at) 
                          VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) throw new Exception('Prepare failed: ' . $db->error);

    $stmt->bind_param('ssii', $name, $mime, $size, $userId);
    if (!$stmt->execute()) throw new Exception('Execute failed: ' . $stmt->error);

    $insertId = $stmt->insert_id;
    $stmt->close();

    // Success response
    ob_clean();
    echo json_encode([
        'success'   => true,
        'id'        => $insertId,
        'file_name' => $name,
        'file_type' => $mime,
        'size'      => $size,
        'user_id'   => $userId
    ]);
    exit;
} catch (Throwable $e) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
