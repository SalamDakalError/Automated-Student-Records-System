<?php
require_once 'db.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . 'Login/forgot_password.php');
    exit();
}

$email = trim($_POST['email']);
$stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('Location: ' . $base_url . 'Login/forgot_password.php?error=' . urlencode('No user found with that email.'));
    exit();
}

// create password_resets table if missing
$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id DOUBLE,
    code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$code = random_int(100000, 999999);
$code_hash = password_hash($code, PASSWORD_DEFAULT);
$expires = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');

$ins = $pdo->prepare('INSERT INTO password_resets (user_id, code_hash, expires_at) VALUES (?, ?, ?)');
$ins->execute([$user['id'], $code_hash, $expires]);

$body = "Hello {$user['name']},<br><br>Your password reset code is: <strong>{$code}</strong><br>The code expires in 15 minutes.<br><br>If you didn't request this, ignore this email.";
$sent = send_mail($email, $user['name'], 'Password reset code', $body);
if ($sent) {
    header('Location: ' . $base_url . 'Login/verify_reset.php?email=' . urlencode($email) . '&success=' . urlencode('Verification code sent.'));
    exit();
} else {
    // Show more detail for debugging
    error_log("Failed to send reset email to $email");
    header('Location: ' . $base_url . 'Login/forgot_password.php?error=' . urlencode('Failed to send email. Check mail config and see error logs.'));
    exit();
}
