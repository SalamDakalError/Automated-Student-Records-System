<?php
require_once __DIR__ . '/../Login/db.php';
header('Content-Type: text/html; charset=UTF-8');
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT id, file_name, teacher_name, submitted_date, status FROM teacher_files";
$params = [];
if ($q !== '') {
    $sql .= " WHERE file_name LIKE :q OR teacher_name LIKE :q OR status LIKE :q";
    $params[':q'] = '%' . $q . '%';
}
$sql .= " ORDER BY submitted_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo '<tr><td colspan="5" class="no-data">No files found</td></tr>';
    exit;
}
foreach ($rows as $r) {
    $fileName = htmlspecialchars($r['file_name']);
    $teacher = htmlspecialchars($r['teacher_name']);
    $submitted = $r['submitted_date'] ? htmlspecialchars(date('F d, Y', strtotime($r['submitted_date']))) : '-';
    $status = htmlspecialchars($r['status']);
    $statusClass = $status === 'pending' ? 'pending' : 'approve';
    echo '<tr>';
    echo '<td>' . $fileName . '</td>';
    echo '<td>' . $teacher . '</td>';
    echo '<td>' . $submitted . '</td>';
    echo '<td><span class="status ' . $statusClass . '">' . ucfirst($status) . '</span></td>';
    echo '<td class="actions-cell">';
    if ($status === 'pending') {
        echo '<button class="action-btn approve-btn" title="Approve">✓</button>';
        echo '<button class="action-btn reject-btn" title="Reject">✕</button>';
    } else {
        echo '<span class="no-action">—</span>';
    }
    echo '</td>';
    echo '</tr>';
}
