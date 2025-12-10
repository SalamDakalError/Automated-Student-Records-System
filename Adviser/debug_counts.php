<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

$response = [
    'session_name' => $_SESSION['name'] ?? 'NOT SET',
    'session_role' => $_SESSION['role'] ?? 'NOT SET',
    'files_in_table' => 0,
    'files_for_teacher' => 0,
    'all_files' => []
];

try {
    // Check all files in table
    $stmt = $pdo->query("SELECT id, teacher_name, file_name, status FROM teacher_files");
    $allFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['files_in_table'] = count($allFiles);
    $response['all_files'] = $allFiles;
    
    // Check files for this teacher
    if (!empty($_SESSION['name'])) {
        $stmt2 = $pdo->prepare("SELECT id, teacher_name, file_name, status FROM teacher_files WHERE teacher_name = :tname");
        $stmt2->execute([':tname' => $_SESSION['name']]);
        $teacherFiles = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $response['files_for_teacher'] = count($teacherFiles);
        $response['teacher_files'] = $teacherFiles;
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
