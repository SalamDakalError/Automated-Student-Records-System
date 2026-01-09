<?php

require_once 'db.php';
require_once 'config.php';


$redirects = [
    'adviser'   => $base_url . 'Adviser/adviserDashboard.php',
    'teacher'   => $base_url . 'Teacher/teacher_dashboard.php',
    'principal' => $base_url . 'Principal/principalDashboard.php',
    'admin'     => $base_url . 'Admin/adminDashboard.php'
];


if (isset($_POST['btnSignIn'])) {
    $email = trim($_POST['txtEmail']);
    $password = $_POST['txtPassword'];

    if (empty($email) || empty($password)) {
        header("Location: {$base_url}Login/loginpage.php?error=" . urlencode("Email and password are required!"));
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, password_hash, role, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // Handle remember me
        if (!empty($_POST['chkRemember'])) {
            // create tokens table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS user_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id DOUBLE,
                selector VARCHAR(32) NOT NULL,
                token_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                type VARCHAR(32) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $selector = bin2hex(random_bytes(8));
            $token = bin2hex(random_bytes(33));
            $token_hash = hash('sha256', $token);
            $expires = (new DateTime('+30 days'))->format('Y-m-d H:i:s');

            $ins = $pdo->prepare('INSERT INTO user_tokens (user_id, selector, token_hash, expires_at, type) VALUES (?, ?, ?, ?, "remember")');
            $ins->execute([$user['id'], $selector, $token_hash, $expires]);

            setcookie('remember', $selector . ':' . $token, time() + 60*60*24*30, '/', '', false, true);
        }

        if (isset($redirects[$user['role']])) {
            header("Location: " . $redirects[$user['role']]);
            exit();
        } else {
            header("Location: {$base_url}Login/loginpage.php?error=" . urlencode("Invalid role! Contact administrator."));
            exit();
        }
    } else {
        header("Location: {$base_url}loginpage.php?error=" . urlencode("Invalid email or password!"));
        exit();
    }
} else {
    // If someone tries to access login.php directly
    header("Location: loginpage.php");
    exit();
}

?>
