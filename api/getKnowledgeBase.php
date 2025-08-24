<?php
header('Content-Type: application/json');

require_once 'authUser.php';  
require_once '../db/db.php';

if ($user->getRole() !== "manager") {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden – Managers only']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit();
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576)    return number_format($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)       return number_format($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

$size   = isset($_GET['size']) && ctype_digit($_GET['size']) ? (int)$_GET['size'] : 10;
$page   = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = $size * ($page - 1);

try {
    $countStmt = $db->prepare("SELECT COUNT(*) FROM document_chunks WHERE user_id = ?");
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countStmt->bind_result($total);
    $countStmt->fetch();
    $countStmt->close();

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
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $userId, $size, $offset);
    $stmt->execute();
    $res = $stmt->get_result();

    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['size_label'] = formatSize((int)$r['size_bytes']);
        $rows[] = $r;
    }
    $stmt->close();

    $has_more = ($offset + $size) < $total;

    echo json_encode([
        'files'    => $rows,
        'page'     => $page,
        'size'     => $size,
        'total'    => $total,
        'has_more' => $has_more,
        'total_pages' => ceil($total / $size)
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
