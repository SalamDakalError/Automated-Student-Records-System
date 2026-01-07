<?php
// Forgot password page re-styled to match login layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>
  <?php require_once 'config.php'; ?>
  <link rel="stylesheet" href="<?= $base_url ?>Login/styleKKMLogin.css">
  <style>
    .forgot-actions { margin-top: 12px; display:flex; gap:8px; align-items:center; }
    .secondary-link { background:transparent; border:none; color:#0056b3; cursor:pointer; text-decoration:underline; padding:0; }
    .notice { font-size:13px; color:#444; margin-bottom:8px; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <img src="<?= $base_url ?>assets/OIP.png" class="school-logo" alt="School Logo">
      <h2>Forgot Password</h2>
      <p class="subtitle">Enter the email associated with your account. We'll send a verification code.</p>

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

      <form method="post" action="<?= $base_url ?>Login/send_reset.php">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" required>

        <div class="forgot-actions">
          <button type="submit" class="signin-btn">Send verification code</button>
          <button type="button" class="secondary-link" onclick="window.location.href='<?= $base_url ?>Login/loginpage.php'">Back to sign in</button>
        </div>
      </form>
    </div>

    <div class="login-right" style="background-color:#001f3f;"></div>
  </div>
</body>
</html>
