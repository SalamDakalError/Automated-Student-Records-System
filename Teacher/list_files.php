<?php
// Returns HTML table rows for teacher_files
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If teacher=1 is provided, show only files for the logged-in teacher
$teacherOnly = isset($_GET['teacher']) && $_GET['teacher'] == '1';
$isDashboard = isset($_GET['dashboard']) && $_GET['dashboard'] == '1';

// Optional search query (q) to filter filenames, teacher_name or subject
$queryParam = isset($_GET['q']) ? trim($_GET['q']) : '';

try{
    // Build query depending on teacher filter and optional search
    if ($teacherOnly && !empty($_SESSION['name'])){
        if ($queryParam !== '') {
            $searchTerm = '%' . $queryParam . '%';
            $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at FROM teacher_files WHERE teacher_name = :tname AND (file_name LIKE :q OR teacher_name LIKE :q OR subject LIKE :q) ORDER BY created_at DESC");
            $stmt->execute([':tname' => $_SESSION['name'], ':q' => $searchTerm]);
        } else {
            $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at FROM teacher_files WHERE teacher_name = :tname ORDER BY created_at DESC");
            $stmt->execute([':tname' => $_SESSION['name']]);
        }
    } else {
        if ($queryParam !== '') {
            $searchTerm = '%' . $queryParam . '%';
            $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at FROM teacher_files WHERE file_name LIKE :q OR teacher_name LIKE :q OR subject LIKE :q ORDER BY created_at DESC");
            $stmt->execute([':q' => $searchTerm]);
        } else {
            $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date, created_at FROM teacher_files ORDER BY created_at DESC");
        }
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Optional debug info: when ?debug=1 is present, emit an HTML comment with session and query info
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        $sessName = !empty($_SESSION['name']) ? $_SESSION['name'] : '(none)';
        $count = is_array($rows) ? count($rows) : 0;
        $q = htmlspecialchars($_SERVER['QUERY_STRING'] ?? '');
        echo "<!--DEBUG: session_name={$sessName}; rows={$count}; query={$q} -->\n";
    }
} catch (Exception $e){
    http_response_code(500);
    echo '<tr><td colspan="5">Error loading files</td></tr>';
    exit;
}

if (!$rows){
    echo '<tr><td colspan="5" class="no-data">No files found</td></tr>';
    exit;
}

foreach($rows as $r){
    $fileName = htmlspecialchars($r['file_name']);
    $teacher = htmlspecialchars($r['teacher_name']);
    $subject = htmlspecialchars($r['subject']);
    $grade = htmlspecialchars($r['grade_section']);
    $status = htmlspecialchars($r['status']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['submitted_date']))) : '-';
    $approve = $r['approve_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['approve_date']))) : '-';
    $filePath = htmlspecialchars($r['file_path']);
    $fileId = intval($r['id']);

    // link to file (pages are in subfolders so prefix with ../)
    $href = '../' . $filePath;

    echo "<tr>";
    // Show plain text filename on dashboard, link on files page
    if ($isDashboard) {
        echo "<td>{$fileName}</td>";
    } else {
        echo "<td><a href=\"{$href}\" download>{$fileName}</a></td>";
    }
    echo "<td>{$teacher}</td>";
    echo "<td>{$submitted}</td>";
    echo "<td>" . ucfirst($status) . "</td>";
    echo "<td>{$approve}</td>";
    echo "</tr>";
}

?>
