<?php
// Returns JSON for imported student rows with optional search for teacher
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET'){
    echo json_encode(['success'=>false,'error'=>'Invalid request method']);
    exit;
}

$table = isset($_GET['table']) ? $_GET['table'] : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (empty($table) || !preg_match('/^[A-Za-z0-9_]+$/', $table)){
    echo json_encode(['success'=>false,'error'=>'Invalid table name']);
    exit;
}

try{
    // Check table exists
    $stmtExists = $pdo->prepare("SHOW TABLES LIKE :t");
    $stmtExists->execute([':t' => $table]);
    $exists = $stmtExists->fetch(PDO::FETCH_NUM);
    if (!$exists) {
        echo json_encode(['success'=>false,'error'=>'Table not found']);
        exit;
    }

    // Get columns
    $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "`");
    $colsStmt->execute();
    $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Determine which grade columns are present
    $colMap = [];
    $colMap['student_name'] = in_array('student_name', $cols) ? 'student_name' : (in_array('name', $cols) ? 'name' : null);
    $colMap['gender'] = in_array('gender', $cols) ? 'gender' : null;
    $colMap['q1'] = in_array('q1_grade', $cols) ? 'q1_grade' : (in_array('q1', $cols) ? 'q1' : null);
    $colMap['q2'] = in_array('q2_grade', $cols) ? 'q2_grade' : (in_array('q2', $cols) ? 'q2' : null);
    $colMap['q3'] = in_array('q3_grade', $cols) ? 'q3_grade' : (in_array('q3', $cols) ? 'q3' : null);
    $colMap['q4'] = in_array('q4_grade', $cols) ? 'q4_grade' : (in_array('q4', $cols) ? 'q4' : null);
    $colMap['final'] = in_array('final_grade', $cols) ? 'final_grade' : (in_array('final', $cols) ? 'final' : null);

    // Build select list
    $selectCols = [];
    if ($colMap['student_name']) $selectCols[] = "`" . $colMap['student_name'] . "` as student_name";
    if ($colMap['gender']) $selectCols[] = "`" . $colMap['gender'] . "` as gender";
    if ($colMap['q1']) $selectCols[] = "`" . $colMap['q1'] . "` as q1";
    if ($colMap['q2']) $selectCols[] = "`" . $colMap['q2'] . "` as q2";
    if ($colMap['q3']) $selectCols[] = "`" . $colMap['q3'] . "` as q3";
    if ($colMap['q4']) $selectCols[] = "`" . $colMap['q4'] . "` as q4";
    if ($colMap['final']) $selectCols[] = "`" . $colMap['final'] . "` as final";

    if (empty($selectCols)) {
        echo json_encode(['success'=>false,'error'=>'No usable columns found']);
        exit;
    }

    $where = '';
    $params = [];
    if ($q && $colMap['student_name']) {
        $where = " WHERE `" . $colMap['student_name'] . "` LIKE :q ";
        $params[':q'] = '%' . $q . '%';
    }
    $sql = "SELECT " . implode(', ', $selectCols) . " FROM `" . $table . "`" . $where . " ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true,'rows'=>$rows,'cols'=>array_values($colMap)]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
