<?php
// getKnowledgeFiles.php
header('Content-Type: application/json');

require_once 'authUser.php';   // ✅ gives you $userId and $user
require_once '../db/db.php';

// Only managers allowed
if ($user->getRole() !== "manager") {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden – Managers only']);
    exit();
}

// Helper to format bytes
function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576)    return number_format($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)       return number_format($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

try {
    // ✅ fetch only files uploaded by this manager
    $stmt = $db->prepare("
        SELECT 
            id,
            document_id,
            file_name,
            file_type,
            size AS size_bytes,
            created_at
        FROM document_chunks
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();

    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['size_label'] = formatSize((int)$r['size_bytes']);
        $rows[] = $r;
    }

    echo json_encode($rows);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
