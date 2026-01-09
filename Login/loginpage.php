<?php
require_once 'db.php';
session_start();

// Ensure helper tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS user_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id DOUBLE,
  selector VARCHAR(32) NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  type VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id DOUBLE,
  code_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Auto-login using remember cookie removed — remember-me handled during login only.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Telabastagan Integrated School - Login</title>
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Login/styleKKMLogin.css">
</head>
<body>
  <div class="login-container">
    <!-- Left Side (Form Section) -->
    <div class="login-left">
      <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" class="school-logo" alt="School Logo">

      <h2>Welcome Back</h2>
      <p class="subtitle">Welcome back! Please enter your details</p>

        <?php
        if (!empty($_GET['error'])) {
          $msg = htmlspecialchars($_GET['error']);
          echo "<div class=\"alert alert-error\">" . $msg . "</div>";
        }
        if (!empty($_GET['success'])) {
          $msg = htmlspecialchars($_GET['success']);
          echo "<div class=\"alert alert-success\">" . $msg . "</div>";
        }
        ?>

      <form method="POST" action="<?php echo $base_url . SITE_BASE; ?>Login/login.php">
        <label>Email</label>
        <input type="email" name="txtEmail" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="txtPassword" placeholder="Enter your password" required>

        <div class="options">
          <label><input type="checkbox" name="chkRemember"> Remember me</label>
          <a href="<?php echo $base_url . SITE_BASE; ?>Login/forgot_password.php">Forgot password?</a>
        </div>

        <button type="submit" name="btnSignIn" class="signin-btn">Sign In</button>

        <button type="button" class="google-btn" onclick="window.location.href='<?php echo $base_url . SITE_BASE; ?>Login/google_login.php'">
          <img src="https://www.svgrepo.com/show/355037/google.svg" width="18" alt="">
          Sign in with Google
        </button>
      </form>
    </div>

    <!-- Right Side (Image Section) -->
    <div class="login-right" style="background-color: #001f3f;"></div>
  </div>
</body>
</html>
