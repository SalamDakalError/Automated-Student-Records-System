<?php
// Search logs by filename, teacher name, or status
require_once __DIR__ . '/../Login/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 100; // Limit results to first 100 for faster loading

try {
    if (empty($query)) {
        // If no search query, return limited logs
        $stmt = $pdo->query("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files ORDER BY created_at DESC LIMIT {$limit}");
    } else {
        // Search by filename, teacher name, or status with LIMIT
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("SELECT id, teacher_name, subject, grade_section, file_name, file_path, status, submitted_date, approve_date FROM teacher_files WHERE file_name LIKE ? OR teacher_name LIKE ? OR status LIKE ? ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    }
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If a query was provided, also search the filesystem directories (Admin and Teacher)
    if (!empty($query)) {
        $dirsToSearch = [
            realpath(__DIR__ . '/../Teacher'),
            realpath(__DIR__ . '/../Admin')
        ];

        foreach ($dirsToSearch as $d) {
            if (!$d || !is_dir($d)) continue;

            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, RecursiveDirectoryIterator::SKIP_DOTS));
            $excludedExts = ['php','js','css','html','htm','json','md','ts','map','lock','ini','env','gitignore','bat','sh','xml','yml','yaml','sql','log','md','txt'];
            foreach ($it as $file) {
                if ($file->isFile()) {
                    $fname = $file->getFilename();
                    $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                    if (in_array($ext, $excludedExts)) continue;
                    if (stripos($fname, $query) !== false) {
                        $relative = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $file->getPathname());
                        $rows[] = [
                            'id' => null,
                            'teacher_name' => basename($d),
                            'subject' => '-',
                            'grade_section' => '-',
                            'file_name' => $fname,
                            'file_path' => $relative,
                            'status' => 'local-file',
                            'submitted_date' => date('Y-m-d H:i:s', $file->getMTime()),
                            'approve_date' => null,
                            'created_at' => $file->getMTime()
                        ];
                    }
                }
            }
        }

        // Sort combined results by created_at / file modification time desc
        usort($rows, function ($a, $b) {
            $ta = isset($a['created_at']) ? (int)$a['created_at'] : 0;
            $tb = isset($b['created_at']) ? (int)$b['created_at'] : 0;
            return $tb <=> $ta;
        });

        // Trim to limit
        if (count($rows) > $limit) {
            $rows = array_slice($rows, 0, $limit);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo '<tr><td colspan="6">Error searching logs</td></tr>';
    exit;
}

if (!$rows) {
    echo '<tr><td colspan="6" class="no-data">No logs found</td></tr>';
    exit;
}

$idx = 1;
foreach ($rows as $r) {
    $fileName = htmlspecialchars($r['file_name']);
    $teacher = htmlspecialchars($r['teacher_name']);
    $status = htmlspecialchars($r['status']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['submitted_date']))) : '-';
    $approve = $r['approve_date'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['approve_date']))) : '-';

    echo "<tr>";
    echo "<td>{$idx}</td>";
    echo "<td>{$fileName}</td>";
    echo "<td>" . ucfirst($status) . "</td>";
    echo "<td>{$submitted}</td>";
    echo "<td>{$teacher}</td>";
    echo "<td>{$approve}</td>";
    echo "</tr>";

    $idx++;
}

if (count($rows) >= $limit) {
    echo '<tr><td colspan="6" style="text-align: center; padding: 10px; color: #999; font-size: 0.9em;">Showing first ' . $limit . ' results. Refine search to see more.</td></tr>';
}

?>
