<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Login/db.php';

$teacherOnly = isset($_GET['teacher']) && $_GET['teacher'] == '1';

function advisory_table_name(string $gradeSection): string {
  $raw = trim($gradeSection);
  $tableName = preg_replace('/[^A-Za-z0-9]+/', '_', strtoupper($raw));
  $tableName = trim($tableName, '_');
  if (empty($tableName)) $tableName = 'ADVISORY_IMPORT';
  return $tableName;
}

try {
    if ($teacherOnly) {
        if (empty($_SESSION['name'])) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
        $adviserName = $_SESSION['name'];
        
        // get adviser's assigned advisory
        $stmtAdv = $pdo->prepare('SELECT advisory FROM users WHERE name = :name LIMIT 1');
        $stmtAdv->execute([':name' => $adviserName]);
        $advRow = $stmtAdv->fetch(PDO::FETCH_ASSOC);
        $assignedAdvisory = $advRow['advisory'] ?? '';
        
        if (empty($assignedAdvisory)) {
            echo json_encode(['success' => true, 'file_count' => 0, 'student_count' => 0, 'honors_count' => 0]);
            exit;
        }
        
        $tbl = advisory_table_name($assignedAdvisory);
        
        // check table exists
        $stmtExists = $pdo->prepare("SHOW TABLES LIKE :t");
        $stmtExists->execute([':t' => $tbl]);
        $exists = $stmtExists->fetch(PDO::FETCH_NUM);
        if (!$exists) {
            echo json_encode(['success' => true, 'file_count' => 0, 'student_count' => 0, 'honors_count' => 0]);
            exit;
        }
        
        // check if advisory table has gwa column
        $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $tbl . "`");
        $colsStmt->execute();
        $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $gwaCol = null;
        if (in_array('gwa', $cols, true)) $gwaCol = 'gwa';
        elseif (in_array('GWA', $cols, true)) $gwaCol = 'GWA';
        
        // count distinct students in advisory table
        $sql = "SELECT COUNT(DISTINCT student_name) as cnt FROM `" . $tbl . "` WHERE student_name IS NOT NULL AND student_name != ''";
        $sstmt = $pdo->query($sql);
        $row = $sstmt ? $sstmt->fetch(PDO::FETCH_ASSOC) : ['cnt' => 0];
        $student_count = intval($row['cnt'] ?? 0);
        
        // count honors students (GWA > 90) from advisory table
        // GWA is computed from grades, not stored in advisory table
        $honors_count = 0;
        
        // get all distinct students from advisory
        $studentSql = "SELECT DISTINCT student_name FROM `" . $tbl . "` WHERE student_name IS NOT NULL AND student_name != ''";
        $studentStmt = $pdo->query($studentSql);
        $students = $studentStmt ? $studentStmt->fetchAll(PDO::FETCH_COLUMN, 0) : [];
        
        foreach ($students as $studentName) {
            $studentName = trim((string)$studentName);
            if ($studentName === '') continue;
            
            // compute GWA for this student from grade tables
            // get all tables that might contain grades for this student
            $dbName = null;
            try {
                $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
            } catch (Exception $e) {
                $dbName = 'school';
            }
            
            $gToken = preg_replace('/[^A-Z0-9]+/', '_', strtoupper(trim($assignedAdvisory)));
            $gToken = trim($gToken, '_');
            
            try {
                $tableStmt = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = :db AND table_name LIKE :like');
                $tableStmt->execute([':db' => $dbName, ':like' => '%' . $gToken . '%']);
                $gradeTables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (Exception $e) {
                $gradeTables = [];
            }
            
            $sumFinals = 0.0;
            $subjectCount = 0;
            
            foreach ($gradeTables as $gradeTable) {
                try {
                    // check for final column
                    $colStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $gradeTable . "`");
                    $colStmt->execute();
                    $tableCols = $colStmt->fetchAll(PDO::FETCH_COLUMN, 0);
                    $lc = array_map('strtolower', $tableCols);
                    
                    $studentCol = null;
                    if (in_array('student_name', $lc, true)) $studentCol = 'student_name';
                    elseif (in_array('student', $lc, true)) $studentCol = 'student';
                    elseif (in_array('name', $lc, true)) $studentCol = 'name';
                    
                    $finalCol = null;
                    foreach ($lc as $idx => $cname) { 
                        if ($cname === 'final_grade' || $cname === 'final' || $cname === 'finalgrade') { 
                            $finalCol = $tableCols[$idx]; 
                            break; 
                        } 
                    }
                    
                    if (!$studentCol || !$finalCol) continue;
                    
                    // find student in this table
                    $gradeSql = "SELECT `" . $finalCol . "` FROM `" . $gradeTable . "` WHERE `" . $studentCol . "` LIKE :name LIMIT 1";
                    $gradeStmt = $pdo->prepare($gradeSql);
                    $gradeStmt->execute([':name' => '%' . $studentName . '%']);
                    $gradeRow = $gradeStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($gradeRow) {
                        $finalVal = floatval($gradeRow[$finalCol] ?? 0);
                        if ($finalVal > 0) {
                            $sumFinals += $finalVal;
                            $subjectCount++;
                        }
                    }
                } catch (Exception $e) {
                    // skip tables that don't match structure
                    continue;
                }
            }
            
            if ($subjectCount > 0) {
                $gwa = $sumFinals / $subjectCount;
                if ($gwa >= 90) {
                    $honors_count++;
                }
            }
        }
        
        // file count for this teacher (all uploaded files)
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM teacher_files WHERE teacher_name = :tname");
        $stmt->execute([':tname' => $adviserName]);
        $fileCountRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $file_count = intval($fileCountRow['cnt'] ?? 0);

        echo json_encode(['success' => true, 'file_count' => $file_count, 'student_count' => $student_count, 'honors_count' => $honors_count]);
        exit;
    } else {
        // global counts - get all advisories and sum their students
        $file_count = 0;
        $student_count = 0;
        $honors_count = 0;
        
        // total files across all teachers
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM teacher_files");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $file_count = intval($row['cnt'] ?? 0);
        
        // get all unique advisories from users table
        $stmtAdvisories = $pdo->query("SELECT DISTINCT advisory FROM users WHERE advisory IS NOT NULL AND advisory != '' AND role = 'adviser'");
        $advisories = $stmtAdvisories->fetchAll(PDO::FETCH_COLUMN, 0);
        
        foreach ($advisories as $adv) {
            $tbl = advisory_table_name($adv);
            
            // check table exists
            $stmtExists = $pdo->prepare("SHOW TABLES LIKE :t");
            $stmtExists->execute([':t' => $tbl]);
            $exists = $stmtExists->fetch(PDO::FETCH_NUM);
            if (!$exists) continue;
            
            // check if advisory table has gwa column
            $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $tbl . "`");
            $colsStmt->execute();
            $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            $gwaCol = null;
            if (in_array('gwa', $cols, true)) $gwaCol = 'gwa';
            elseif (in_array('GWA', $cols, true)) $gwaCol = 'GWA';
            
            // count distinct students
            $sql = "SELECT COUNT(DISTINCT student_name) as cnt FROM `" . $tbl . "` WHERE student_name IS NOT NULL AND student_name != ''";
            $sstmt = $pdo->query($sql);
            $row = $sstmt ? $sstmt->fetch(PDO::FETCH_ASSOC) : ['cnt' => 0];
            $student_count += intval($row['cnt'] ?? 0);
            
            // count honors students (GWA >= 90) by computing from grade tables
            $studentSql = "SELECT DISTINCT student_name FROM `" . $tbl . "` WHERE student_name IS NOT NULL AND student_name != ''";
            $studentStmt = $pdo->query($studentSql);
            $students = $studentStmt ? $studentStmt->fetchAll(PDO::FETCH_COLUMN, 0) : [];
            
            foreach ($students as $studentName) {
                $studentName = trim((string)$studentName);
                if ($studentName === '') continue;
                
                // get all grade tables for this advisory
                $dbName = null;
                try {
                    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
                } catch (Exception $e) {
                    $dbName = 'school';
                }
                
                $gToken = preg_replace('/[^A-Z0-9]+/', '_', strtoupper(trim($adv)));
                $gToken = trim($gToken, '_');
                
                try {
                    $tableStmt = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = :db AND table_name LIKE :like');
                    $tableStmt->execute([':db' => $dbName, ':like' => '%' . $gToken . '%']);
                    $gradeTables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);
                } catch (Exception $e) {
                    $gradeTables = [];
                }
                
                $sumFinals = 0.0;
                $subjectCount = 0;
                
                foreach ($gradeTables as $gradeTable) {
                    try {
                        $colStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $gradeTable . "`");
                        $colStmt->execute();
                        $tableCols = $colStmt->fetchAll(PDO::FETCH_COLUMN, 0);
                        $lc = array_map('strtolower', $tableCols);
                        
                        $studentCol = null;
                        if (in_array('student_name', $lc, true)) $studentCol = 'student_name';
                        elseif (in_array('student', $lc, true)) $studentCol = 'student';
                        elseif (in_array('name', $lc, true)) $studentCol = 'name';
                        
                        $finalCol = null;
                        foreach ($lc as $idx => $cname) { 
                            if ($cname === 'final_grade' || $cname === 'final' || $cname === 'finalgrade') { 
                                $finalCol = $tableCols[$idx]; 
                                break; 
                            } 
                        }
                        
                        if (!$studentCol || !$finalCol) continue;
                        
                        $gradeSql = "SELECT `" . $finalCol . "` FROM `" . $gradeTable . "` WHERE `" . $studentCol . "` LIKE :name LIMIT 1";
                        $gradeStmt = $pdo->prepare($gradeSql);
                        $gradeStmt->execute([':name' => '%' . $studentName . '%']);
                        $gradeRow = $gradeStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($gradeRow) {
                            $finalVal = floatval($gradeRow[$finalCol] ?? 0);
                            if ($finalVal > 0) {
                                $sumFinals += $finalVal;
                                $subjectCount++;
                            }
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
                
                if ($subjectCount > 0) {
                    $gwa = $sumFinals / $subjectCount;
                    if ($gwa >= 90) {
                        $honors_count++;
                    }
                }
            }
        }
        
        echo json_encode(['success' => true, 'file_count' => $file_count, 'student_count' => $student_count, 'honors_count' => $honors_count]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

?>

