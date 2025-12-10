<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET'){
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

$table = isset($_GET['table']) ? $_GET['table'] : '';
$student = isset($_GET['student_name']) ? $_GET['student_name'] : '';
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
if (empty($table) || !preg_match('/^[A-Za-z0-9_]+$/', $table) || $student === ''){
    echo json_encode(['success'=>false,'error'=>'Missing parameters']);
    exit;
}

try {
    // get columns
    $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "`");
    $colsStmt->execute();
    $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!$cols) { echo json_encode(['success'=>false,'error'=>'Table not found or empty']); exit; }

    $lc = array_map('strtolower', $cols);
    // pick student col
    $studentCol = null;
    foreach (['student_name','student','name'] as $cand) { if (($p = array_search($cand, $lc, true)) !== false) { $studentCol = $cols[$p]; break; } }
    if (!$studentCol) { echo json_encode(['success'=>false,'error'=>'No student column']); exit; }

    // pick q columns and final
    $qCols = [];
    for ($i=1;$i<=4;$i++) {
        $found = null;
        foreach ($lc as $idx => $cname) { if (strpos($cname, 'q' . $i) !== false) { $found = $cols[$idx]; break; } }
        $qCols[$i] = $found;
    }
    $finalCol = null;
    foreach ($lc as $idx => $cname) { if ($cname === 'final_grade' || $cname === 'final' || $cname === 'finalgrade') { $finalCol = $cols[$idx]; break; } }

    // build select
    $select = ['`' . str_replace('`','``',$studentCol) . '` as student_name'];
    for ($i=1;$i<=4;$i++) $select[] = $qCols[$i] ? ('`' . str_replace('`','``',$qCols[$i]) . '` as q' . $i) : ('NULL as q' . $i);
    $select[] = $finalCol ? ('`' . str_replace('`','``',$finalCol) . '` as final') : 'NULL as final';

    $sql = 'SELECT ' . implode(', ', $select) . ' FROM `' . str_replace('`','``',$table) . '` WHERE `' . str_replace('`','``',$studentCol) . '` LIKE :stu LIMIT 1';
    $st = $pdo->prepare($sql);
    $st->execute([':stu' => '%' . $student . '%']);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        // try scanning all rows to match normalized
        $scan = $pdo->query('SELECT ' . implode(', ', $select) . ' FROM `' . str_replace('`','``',$table) . '`');
        $all = $scan->fetchAll(PDO::FETCH_ASSOC);
        $normSearch = preg_replace('/[^a-z0-9]/','', strtolower($student));
        $found = null;
        foreach ($all as $r) {
            $dbVal = strtolower((string)($r['student_name'] ?? ''));
            $normDb = preg_replace('/[^a-z0-9]/','', $dbVal);
            if ($normDb !== '' && (strpos($normDb, $normSearch) !== false || strpos($normSearch, $normDb) !== false)) { $found = $r; break; }
        }
        if (!$found) { echo json_encode(['success'=>false,'message'=>'Student not found']); exit; }
        $row = $found;
    }

    // Try to find the actual subject from teacher_files table using the table name
    // The table name is derived from filename, so we try to match it
    if (empty($subject)) {
        try {
            $sfStmt = $pdo->prepare('SELECT subject FROM teacher_files WHERE file_path LIKE ? LIMIT 1');
            // Try to match by filename: if table is "filename_with_underscores", look for "filename with underscores" in file_path
            $searchPattern = '%' . str_replace('_', ' ', $table) . '%';
            $sfStmt->execute([$searchPattern]);
            $sfRow = $sfStmt->fetch(PDO::FETCH_ASSOC);
            if ($sfRow && !empty($sfRow['subject'])) {
                $subject = $sfRow['subject'];
            } else {
                // Fallback: derive from table name (replace underscores with spaces)
                $subject = trim(str_replace('_', ' ', $table));
            }
        } catch (Exception $e) {
            // If teacher_files lookup fails, fallback to deriving from table name
            $subject = trim(str_replace('_', ' ', $table));
        }
    }

    // normalize numeric fields
    for ($i=1;$i<=4;$i++) {
        $k = 'q' . $i;
        if (isset($row[$k]) && is_numeric($row[$k])) $row[$k] = round(floatval($row[$k]),2);
        else $row[$k] = null;
    }
    if (isset($row['final']) && is_numeric($row['final'])) $row['final'] = round(floatval($row['final']),2);
    else $row['final'] = null;

    echo json_encode(['success'=>true,'row'=>$row, 'subject'=>$subject]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}

?>
