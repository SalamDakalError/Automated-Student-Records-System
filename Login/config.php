<?php
// Configuration placeholders. Fill these values before using Google login or email sending.

// Google OAuth
// Create credentials at https://console.developers.google.com/apis/credentials
define('GOOGLE_CLIENT_ID', '4602130300-crk5prbp89cnhtom33et628276j0povu.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/Automated-Student-Records-Processing-System-main/Login/google_callback.php');

// SMTP / Mail settings for PHPMailer (Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'johnpaulmanarang07@gmail.com');
define('SMTP_PASS', 'htcvszwnzeyzufcs');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// From address for reset emails
define('MAIL_FROM', 'johnpaulmanarang07@gmail.com');
define('MAIL_FROM_NAME', 'School Admin');


// Site URL (update for IIS deployment)
// Use '/' if deployed at root, or '/subfolder/' if in a subfolder
define('SITE_BASE', '/');

// Base URL variable for easy path management in PHP
$base_url = SITE_BASE;

// Note: Install dependencies: run
// composer require google/apiclient:^2.12 phpmailer/phpmailer

?>
