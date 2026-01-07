<?php
require_once __DIR__ . '/../Login/db.php';
require_once __DIR__ . '/../Login/config.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    if ($isAjax) {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        exit;
    }
    // non-AJAX fallback: redirect back
    $_SESSION['flash_create_user_error'] = 'Invalid request method';
    header('Location: ' . $base_url . 'Admin/adminDashboard.php');
    exit;
}

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$role = strtolower(trim($_POST['role'] ?? ''));
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
// advisory field from form (used for adviser)
$advisory = trim($_POST['advisory'] ?? '');

$allowed = ['teacher','adviser','principal','admin'];
if (!in_array($role, $allowed)) {
    if ($isAjax) { echo json_encode(['success' => false, 'error' => 'Invalid role']); exit; }
    $_SESSION['flash_create_user_error'] = 'Invalid role'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
}
if ($name === '' || $email === '' || $password === '' || $confirm === '') {
    if ($isAjax) { echo json_encode(['success' => false, 'error' => 'All fields are required']); exit; }
    $_SESSION['flash_create_user_error'] = 'All fields are required'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) { echo json_encode(['success' => false, 'error' => 'Invalid email address']); exit; }
    $_SESSION['flash_create_user_error'] = 'Invalid email address'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
}
if ($password !== $confirm) {
    if ($isAjax) { echo json_encode(['success' => false, 'error' => 'Passwords do not match']); exit; }
    $_SESSION['flash_create_user_error'] = 'Passwords do not match'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
}
if (strlen($password) < 6) {
    if ($isAjax) { echo json_encode(['success' => false, 'error' => 'Password too short']); exit; }
    $_SESSION['flash_create_user_error'] = 'Password too short'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
}

try{
    // check email uniqueness
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        if ($isAjax) { echo json_encode(['success'=>false,'error'=>'Email already in use']); exit; }
        $_SESSION['flash_create_user_error'] = 'Email already in use'; header('Location: ' . $base_url . 'Admin/adminDashboard.php'); exit;
    }

    // Follow the provided `users` table schema: insert only into columns that exist.
    // The `users` table has columns: id, name, email, password_hash, role, advisory
    // Map the incoming `advisory` into `advisory` column when appropriate:
    // - adviser: advisory = advisory (e.g., "10 - A")
    // - teacher: advisory = NULL (teachers don't have advisory here)
    // - others: advisory = NULL
    $pw = password_hash($password, PASSWORD_DEFAULT);
    $advisoryVal = null;
    if ($role === 'adviser') {
        $advisoryVal = $advisory ?: null;
    }

    $ins = $pdo->prepare('INSERT INTO users (email, password_hash, role, name, advisory) VALUES (?, ?, ?, ?, ?)');
    $ins->execute([$email, $pw, $role, $name, $advisoryVal]);

    if ($isAjax) {
        echo json_encode(['success'=>true]);
        exit;
    }
    // non-AJAX: set flash message and redirect back to admin page
    $_SESSION['flash_create_user'] = 'User created successfully';
    header('Location: adminDashboard.php');
    exit;
} catch (Exception $e){
    http_response_code(500);
    if ($isAjax) {
        echo json_encode(['success'=>false,'error'=>'Server error']);
    } else {
        $_SESSION['flash_create_user_error'] = 'Server error'; header('Location: adminDashboard.php');
    }
}

?>
