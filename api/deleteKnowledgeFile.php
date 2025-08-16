<?php
// api/deleteKnowledgeFile.php
ob_start();
require_once __DIR__ . "/../utils/utils.php";
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); ob_end_clean(); exit; }

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    require_once '../db/db.php'; // must create $db = new mysqli(...)

    // Prefer delete by numeric id
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id) {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            respond(405, "error", "Method unsupported");
        }
        $id = $_POST['id'] ?? null;
        if (!$id) {
            respond(400, "error", "Missing data.");
        }
        $id = escapeshellcmd(htmlspecialchars($id));
    }

    if (!$id) {
        // fallback: allow delete by file_name if you didn’t include id in your table rows
        $name = isset($_POST['file_name']) ? trim($_POST['file_name']) : '';
        if ($name === '') throw new Exception('Missing id or file_name');
        $stmt = $db->prepare('DELETE FROM document_chunks WHERE file_name = ? LIMIT 1');
        $stmt->bind_param('s', $name);
    } else {
        $stmt = $db->prepare('DELETE FROM document_chunks WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
    }

    if (!$stmt->execute()) {
        throw new Exception('DB error: '.$stmt->error);
    }

    echo json_encode(['success' => true]);
    ob_end_flush();
} catch (Throwable $e) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
