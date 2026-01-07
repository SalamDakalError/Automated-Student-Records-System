<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $code = trim($_POST['code']);

  $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user) {
    header('Location: verify_reset.php?error=' . urlencode('Invalid email'));
    exit();
  }

  $pr = $pdo->prepare('SELECT id, code_hash, expires_at FROM password_resets WHERE user_id = ? ORDER BY id DESC LIMIT 1');
  $pr->execute([$user['id']]);
  $row = $pr->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    header('Location: verify_reset.php?error=' . urlencode('No reset request found'));
    exit();
  }

  if (new DateTime() > new DateTime($row['expires_at'])) {
    header('Location: verify_reset.php?error=' . urlencode('Code expired'));
    exit();
  }

  if (!password_verify($code, $row['code_hash'])) {
    header('Location: verify_reset.php?error=' . urlencode('Invalid code'));
    exit();
  }

  // verification succeeded: store user id in session for password reset
  $_SESSION['password_reset_user_id'] = $user['id'];
  $_SESSION['password_reset_expires'] = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');

  header('Location: new_password.php?email=' . urlencode($email));
  exit();
}

$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enter Verification Code</title>
  <link rel="stylesheet" href="styleKKMLogin.css">
  <style>
    .form-row { margin-top:8px; }
    .actions { margin-top:12px; display:flex; gap:8px; align-items:center; }
    .secondary-link { background:transparent; border:none; color:#0056b3; cursor:pointer; text-decoration:underline; padding:0; }
    .hint { font-size:13px; color:#666; margin-top:6px; }
    input.code-input { letter-spacing:4px; font-size:18px; text-align:center; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <img src="<?= $base_url ?>assets/OIP.png" class="school-logo" alt="School Logo">
      <h2>Reset your password</h2>
      <p class="subtitle">Enter the 6-digit verification code we sent to your email.</p>

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

      <form method="post" action="verify_reset.php" id="verifyForm">
        <div class="form-row">
          <label>Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" readonly aria-readonly="true">
        </div>

        <div class="form-row">
          <label>Verification code</label>
          <input type="text" name="code" class="code-input" id="codeInput" inputmode="numeric" pattern="\d{6}" maxlength="6" placeholder="123456" required aria-label="6 digit verification code">
          <div class="hint">Enter the 6-digit code we emailed you. It expires in 15 minutes.</div>
        </div>

        <div class="actions">
          <button type="submit" class="signin-btn">Verify code</button>
          <button type="button" class="secondary-link" onclick="window.location.href='<?= $base_url ?>Login/loginpage.php'">Back to sign in</button>
        </div>
      </form>
    </div>

      <div class="login-right" style="background-color:#001f3f;"></div>
  </div>
    <script>
      // small client-side validation for the 6-digit code input
      (function(){
        const code = document.getElementById('codeInput');
        const form = document.getElementById('verifyForm');
        if (code) {
          // allow only digits
          code.addEventListener('input', (e)=>{
            const cleaned = e.target.value.replace(/\D/g,'').slice(0,6);
            if (e.target.value !== cleaned) e.target.value = cleaned;
          });
          // autofocus code when page loads
          window.addEventListener('load', ()=>{ setTimeout(()=>{ code.focus(); }, 50); });
        }
        if (form) {
          form.addEventListener('submit', (e)=>{
            if (!code) return;
            if (code.value.length !== 6) {
              e.preventDefault();
              alert('Please enter the full 6-digit verification code.');
              code.focus();
            }
          });
        }
      })();
    </script>
</body>
</html>
