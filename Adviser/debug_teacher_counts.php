<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

$response = [
    'session_name' => $_SESSION['name'] ?? 'NOT SET',
    'session_email' => $_SESSION['email'] ?? 'NOT SET',
    'session_role' => $_SESSION['role'] ?? 'NOT SET',
];

try {
    // Check all teachers in the database
    $stmt = $pdo->query("SELECT DISTINCT teacher_name FROM teacher_files");
    $allTeachers = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $response['all_teachers_in_db'] = $allTeachers;
    
    // Check files for current session user
    if (!empty($_SESSION['name'])) {
        $stmt2 = $pdo->prepare("SELECT id, teacher_name, file_name, status FROM teacher_files WHERE teacher_name = :tname");
        $stmt2->execute([':tname' => $_SESSION['name']]);
        $teacherFiles = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $response['files_for_session_teacher'] = $teacherFiles;
        $response['count_for_session_teacher'] = count($teacherFiles);
    }
    
    // Check all files count
    $stmtAll = $pdo->query("SELECT COUNT(*) as cnt FROM teacher_files");
    $allFilesCount = $stmtAll->fetch(PDO::FETCH_ASSOC);
    $response['total_files_in_db'] = $allFilesCount['cnt'] ?? 0;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
