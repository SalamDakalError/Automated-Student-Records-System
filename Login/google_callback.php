<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    die("Composer autoload not found. Please run in project root:\ncomposer require google/apiclient:^2.12 phpmailer/phpmailer\n");
}
require_once 'config.php';
require_once $autoload;
require_once 'db.php';

if (!class_exists('Google_Client')) {
    die("Google API Client not found. Install with:\ncomposer require google/apiclient:^2.12\n");
}

if (!isset($_GET['code'])) {
    header('Location: loginpage.php?error=' . urlencode('Google login failed.'));
    exit();
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    header('Location: loginpage.php?error=' . urlencode('Google login error.'));
    exit();
}

$client->setAccessToken($token);
$oauth2 = new Google_Service_Oauth2($client);
$userinfo = $oauth2->userinfo->get();

$email = $userinfo->email;
$name = $userinfo->name ?? $userinfo->email;

$stmt = $pdo->prepare('SELECT id, role, name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    session_start();
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $redirects = [
        'adviser'   => '../Adviser/adviserDashboard.php',
        'teacher'   => '../Teacher/teacher_Dashboard.php',
        'principal' => '../Principal/principalDashboard.php',
        'admin'     => '../Admin/adminDashboard.php'
    ];
    if (isset($redirects[$user['role']])) {
        header('Location: ' . $redirects[$user['role']]);
        exit();
    }
    header('Location: loginpage.php?error=' . urlencode('No dashboard mapped for role.'));
    exit();
} else {
    // No user with that email - do not auto-create for security. Show helpful message.
    header('Location: loginpage.php?error=' . urlencode('No account found for that Google account. Please contact administrator.'));
    exit();
}
