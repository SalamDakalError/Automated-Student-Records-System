<?php
session_start();
header('Content-Type: application/json');

// require DB connection
require_once __DIR__ . '/../Login/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK){
    echo json_encode(['success'=>false,'error'=>'No file uploaded or upload error']);
    exit;
}

$uploaded = $_FILES['file'];
$originalName = $uploaded['name'];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, ['xls','xlsx'])){
    echo json_encode(['success'=>false,'error'=>'Only Excel files are allowed']);
    exit;
}

$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$grade_section = isset($_POST['grade_section']) ? trim($_POST['grade_section']) : '';
$teacher_name = isset($_POST['teacher_name']) ? trim($_POST['teacher_name']) : '';

// Fallback: try to parse from filename if fields empty
if (($subject === '' || $grade_section === '') && $originalName){
    $base = preg_replace('/\.[^.]+$/', '', $originalName);
    $parts = explode('-', $base);
    if (count($parts) >= 3){
        if ($subject === '') $subject = trim($parts[0]);
        if ($grade_section === '') $grade_section = trim($parts[1]) . ' - ' . trim(implode('-', array_slice($parts,2)));
    }
}

if ($teacher_name === '' && !empty($_SESSION['name'])){
    $teacher_name = $_SESSION['name'];
}

if ($subject === '' || $grade_section === ''){
    echo json_encode(['success'=>false,'error'=>'Unable to determine subject or grade/section from filename or form']);
    exit;
}

// Normalize common subject codes to desired display names
if ($subject !== '') {
    $subKey = strtoupper(trim($subject));
    // map of source code -> desired stored value
    $subjectMap = [
        'ESP'  => 'Edukasyon Sa Pagpapakatao',
        'ENG'  => 'English',
        'MATH' => 'Mathematics',
        'AP'   => 'Araling Panlipunan',
        'FIL'  => 'Filipino',
        'MAPEH'=> 'MAPEH',
        'SCI'  => 'Science',
        // For EPP user requested keep it as "EPP"
        'EPP'  => 'EPP',
        'GRMC' => 'GRMC'
    ];

    // If the submitted subject is exactly one of the codes, map it.
    if (isset($subjectMap[$subKey])) {
        $subject = $subjectMap[$subKey];
    } else {
        // Sometimes subject may be provided like "Eng - Grade 7" or contain extra text.
        // Try to extract the first token before any non-letter character and map that.
        if (preg_match('/^([A-Za-z]+)/', $subKey, $m)) {
            $first = $m[1];
            if (isset($subjectMap[$first])) {
                $subject = $subjectMap[$first];
            }
        }
    }
}

$uploadsDir = __DIR__ . '/../uploads/teacher_files';
if (!is_dir($uploadsDir)){
    if (!mkdir($uploadsDir, 0755, true)){
        echo json_encode(['success'=>false,'error'=>'Failed to create upload directory']);
        exit;
    }
}

$safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($originalName));
$targetName = $safeName;
$targetPath = $uploadsDir . '/' . $targetName;

if (!move_uploaded_file($uploaded['tmp_name'], $targetPath)){
    echo json_encode(['success'=>false,'error'=>'Failed to move uploaded file']);
    exit;
}

// Save relative path for DB (relative to project root)
$relativePath = 'uploads/teacher_files/' . $targetName;

try{
    $stmt = $pdo->prepare('INSERT INTO teacher_files (teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at) VALUES (:teacher_name, :subject, :grade_section, :file_name, :file_path, :status, NOW(), NULL, NOW())');
    $stmt->execute([
        ':teacher_name' => $teacher_name,
        ':subject' => $subject,
        ':grade_section' => $grade_section,
        ':file_name' => $originalName,
        ':file_path' => $relativePath,
        ':status' => 'pending'
    ]);
    echo json_encode(['success'=>true,'message'=>'File uploaded and record saved']);
} catch (Exception $e){
    // On DB error, try to remove the uploaded file to avoid orphan files
    if (file_exists($targetPath)) @unlink($targetPath);
    echo json_encode(['success'=>false,'error'=>'Database error: ' . $e->getMessage()]);
}

?>
