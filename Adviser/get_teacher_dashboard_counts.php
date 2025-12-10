<?php
session_start();
header('Content-Type: application/json');

// Ensure we have a fresh database connection
if (!isset($pdo)) {
    require_once __DIR__ . '/../Login/db.php';
}

try {
    // Debug: log session info
    error_log('get_teacher_dashboard_counts.php - SESSION: ' . json_encode($_SESSION));
    
    // Try to get teacher name from parameter first, then from session
    $teacherName = $_GET['teacher'] ?? $_POST['teacher'] ?? $_SESSION['name'] ?? null;
    
    if (empty($teacherName)) {
        error_log('get_teacher_dashboard_counts.php - Not logged in');
        echo json_encode(['success' => false, 'error' => 'Not logged in', 'student_count' => 0, 'file_count' => 0]);
        exit;
    }
    
    error_log('get_teacher_dashboard_counts.php - Teacher: ' . $teacherName);
    
    // Get total files uploaded by this teacher (all statuses)
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM teacher_files WHERE teacher_name = :tname");
    $stmt->execute([':tname' => $teacherName]);
    $fileCountRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $file_count = intval($fileCountRow['cnt'] ?? 0);
    error_log('get_teacher_dashboard_counts.php - Total files for teacher: ' . $file_count);
    
    // Get all files (both approved and pending) to get students from them
    $stmtFiles = $pdo->prepare("SELECT file_path, file_name, status FROM teacher_files WHERE teacher_name = :tname");
    $stmtFiles->execute([':tname' => $teacherName]);
    $files = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
    error_log('get_teacher_dashboard_counts.php - Files found: ' . count($files));
    
    $uniqueStudents = [];
    foreach ($files as $f) {
        $path = $f['file_path'] ?? $f['file_name'] ?? '';
        error_log('Processing file: ' . $path . ' (status: ' . ($f['status'] ?? 'unknown') . ')');
        
        // derive table name from filename: basename without extension, keep only letters/numbers/_
        $base = pathinfo($path, PATHINFO_BASENAME);
        $baseNoExt = preg_replace('/\.[^.]+$/', '', $base);
        $table = preg_replace('/[^A-Za-z0-9_]/', '_', $baseNoExt);
        if (!$table) {
            error_log('Could not derive table name from: ' . $base);
            continue;
        }
        error_log('Derived table name: ' . $table);
        
        // check table exists
        $stmtExists = $pdo->prepare("SHOW TABLES LIKE :t");
        $stmtExists->execute([':t' => $table]);
        $exists = $stmtExists->fetch(PDO::FETCH_NUM);
        if (!$exists) {
            error_log('Table does not exist: ' . $table);
            continue;
        }
        error_log('Table exists: ' . $table);
        
        // detect student name column
        $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "`");
        $colsStmt->execute();
        $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        error_log('Columns in table: ' . json_encode($cols));
        
        $studentCol = null;
        if (in_array('student_name', $cols, true)) $studentCol = 'student_name';
        elseif (in_array('name', $cols, true)) $studentCol = 'name';
        elseif (in_array('student', $cols, true)) $studentCol = 'student';
        
        if (!$studentCol) {
            error_log('No student column found in table: ' . $table);
            continue;
        }
        error_log('Using student column: ' . $studentCol);
        
        // get distinct student names from this table
        $sql = "SELECT DISTINCT `" . $studentCol . "` as s FROM `" . $table . "` WHERE `" . $studentCol . "` IS NOT NULL AND `" . $studentCol . "` != ''";
        error_log('Executing SQL: ' . $sql);
        $sstmt = $pdo->query($sql);
        $rows = $sstmt ? $sstmt->fetchAll(PDO::FETCH_COLUMN, 0) : [];
        error_log('Students found in table: ' . count($rows));
        
        foreach ($rows as $name) {
            $n = trim((string)$name);
            if ($n === '') continue;
            $uniqueStudents[$n] = true;
        }
    }
    
    $student_count = count($uniqueStudents);
    error_log('Total unique students: ' . $student_count);
    
    echo json_encode(['success' => true, 'student_count' => $student_count, 'file_count' => $file_count]);
    exit;
    
} catch (Exception $e) {
    error_log('get_teacher_dashboard_counts.php - Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'student_count' => 0, 'file_count' => 0]);
    exit;
}

?>
    exit;
}

?>
