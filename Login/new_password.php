<?php
require_once 'db.php';
session_start();

// If GET, show form; if POST, update password
$sessionUserId = $_SESSION['password_reset_user_id'] ?? null;
$sessionExpires = $_SESSION['password_reset_expires'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newpw = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // basic checks
    if (!$sessionUserId) {
        header('Location: forgot_password.php?error=' . urlencode('Session expired. Please request a new code.'));
        exit();
    }
    // check expiry
    if ($sessionExpires && new DateTime() > new DateTime($sessionExpires)) {
        unset($_SESSION['password_reset_user_id']);
        unset($_SESSION['password_reset_expires']);
        header('Location: forgot_password.php?error=' . urlencode('Session expired. Please request a new code.'));
        exit();
    }

    if ($newpw === '' || $confirm === '') {
        header('Location: new_password.php?email=' . urlencode($email) . '&error=' . urlencode('All fields are required'));
        exit();
    }

    // Enforce minimum 6 characters as requested
    if (strlen($newpw) < 6) {
        header('Location: new_password.php?email=' . urlencode($email) . '&error=' . urlencode('Password must be at least 6 characters'));
        exit();
    }
    if ($newpw !== $confirm) {
        header('Location: new_password.php?email=' . urlencode($email) . '&error=' . urlencode('Passwords do not match'));
        exit();
    }

    // fetch user by session id just to be safe
    $stmt = $pdo->prepare('SELECT id, email FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$sessionUserId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        unset($_SESSION['password_reset_user_id']);
        unset($_SESSION['password_reset_expires']);
        header('Location: forgot_password.php?error=' . urlencode('Invalid reset session'));
        exit();
    }

    // update password
    $pw_hash = password_hash($newpw, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $upd->execute([$pw_hash, $user['id']]);

    // remove used reset tokens
    $del = $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?');
    $del->execute([$user['id']]);

    // clear session markers
    unset($_SESSION['password_reset_user_id']);
    unset($_SESSION['password_reset_expires']);

    header('Location: ' . $base_url . 'Login/loginpage.php?success=' . urlencode('Password updated. You can now login.'));
    exit();
}

// GET
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Set New Password</title>
<<<<<<< HEAD
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Login/styleKKMLogin.css">
=======
  <?php require_once 'config.php'; ?>
  <link rel="stylesheet" href="<?= $base_url ?>Login/styleKKMLogin.css">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
  <style>
    .form-row { margin-top:8px; }
    .actions { margin-top:12px; display:flex; gap:8px; align-items:center; }
    .secondary-link { background:transparent; border:none; color:#0056b3; cursor:pointer; text-decoration:underline; padding:0; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-left">
<<<<<<< HEAD
      <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" class="school-logo" alt="School Logo">
=======
      <img src="<?= $base_url ?>assets/OIP.png" class="school-logo" alt="School Logo">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
      <h2>Set a new password</h2>
      <p class="subtitle">Enter a new password for the account shown below.</p>

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

<<<<<<< HEAD
      <form method="post" action="<?php echo $base_url . SITE_BASE; ?>Login/new_password.php">
=======
      <form method="post" action="<?= $base_url ?>Login/new_password.php">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
        <div class="form-row">
          <label>Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" readonly>
        </div>

        <div class="form-row">
          <label>New password (min 6 characters)</label>
          <input type="password" name="new_password" required minlength="6">
        </div>

        <div class="form-row">
          <label>Confirm password</label>
          <input type="password" name="confirm_password" required minlength="6">
        </div>

        <div class="actions">
          <button type="submit" class="signin-btn">Save new password</button>
<<<<<<< HEAD
          <button type="button" class="secondary-link" onclick="window.location.href='<?php echo $base_url . SITE_BASE; ?>Login/loginpage.php'">Back to sign in</button>
=======
          <button type="button" class="secondary-link" onclick="window.location.href='<?= $base_url ?>Login/loginpage.php'">Back to sign in</button>
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
        </div>
      </form>
    </div>

    <div class="login-right" style="background-color:#001f3f;"></div>
  </div>
</body>
</html>
