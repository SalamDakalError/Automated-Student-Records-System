<?php
// Handles Excel file upload for teacher
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

if (empty($_SESSION['name'])) {
    echo json_encode(['success'=>false,'error'=>'Not logged in']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success'=>false,'error'=>'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$name = $file['name'];
$tmp = $file['tmp_name'];
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if (!in_array($ext, ['xls','xlsx'])) {
    echo json_encode(['success'=>false,'error'=>'Only Excel files (.xls, .xlsx) are allowed.']);
    exit;
}

$grade_section = isset($_POST['grade_section']) ? trim($_POST['grade_section']) : '';
$teacher = $_SESSION['name'];

// Save file to uploads/teacher_files/
$targetDir = __DIR__ . '/../uploads/teacher_files/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
$targetPath = $targetDir . basename($name);
if (!move_uploaded_file($tmp, $targetPath)) {
    echo json_encode(['success'=>false,'error'=>'Failed to save file']);
    exit;
}

// Insert record into teacher_files table
$stmt = $pdo->prepare("INSERT INTO teacher_files (teacher_name, grade_section, file_name, file_path, status, submitted_date) VALUES (:teacher, :grade, :fname, :fpath, 'pending', NOW())");
$stmt->execute([
    ':teacher' => $teacher,
    ':grade' => $grade_section,
    ':fname' => $name,
    ':fpath' => 'uploads/teacher_files/' . basename($name)
]);

echo json_encode(['success'=>true]);
