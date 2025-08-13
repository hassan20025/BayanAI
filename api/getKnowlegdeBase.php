<?php
// getKnowledgeFiles.php
header('Content-Type: application/json');

try {
    require_once '../db/db.php'; // $db = new mysqli(...)

    // Helper to format bytes
    function formatSize($bytes) {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // document_chunks may contain multiple rows per document
    // Aggregate by document_id if you have it, otherwise by file_name + type
    $sql = "
        SELECT 
            document_id,
            file_name,
            file_type,
            MAX(created_at) AS created_at,
            MAX(size) AS size_bytes
        FROM document_chunks
        GROUP BY document_id, file_name, file_type
        ORDER BY created_at DESC
    ";

    $res = $db->query($sql);
    if (!$res) {
        http_response_code(500);
        echo json_encode(['error' => 'DB error: ' . $db->error]);
        exit;
    }

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
