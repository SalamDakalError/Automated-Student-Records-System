<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

try {
    // Get total files submitted
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM teacher_files");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_files = intval($row['cnt'] ?? 0);
    
    // Get total pending files
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM teacher_files WHERE status = 'pending'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_pending = intval($row['cnt'] ?? 0);
    
    echo json_encode(['success' => true, 'total_files' => $total_files, 'total_pending' => $total_pending]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

?>
