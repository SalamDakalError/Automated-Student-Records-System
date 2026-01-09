<?php
// ensure session is started before any output so session cookie and vars are available
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="../assets/OIP.png" alt="Logo">
    <h1>Admin Dashboard</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="menu">
        <a href="adminDashboard.php" id="userTab" class="active">
          <img src="../assets/dashboard.png" alt="Dashboard">
          Users
        </a>
        <a href="adminLogs.php" id="logsTab">
          <img src="../assets/User.png" alt="Logs">
          Logs
        </a>
      </div>

      <div class="sidebar-footer">
        <div class="user-info">
          <?php
            // display name stored in session by login.php
            if (!empty($_SESSION['name'])) {
                echo '<p class="user-name">' . htmlspecialchars($_SESSION['name']) . '</p>';
            } else {
                echo '<p class="user-name">Not logged in</p>';
            }
          ?>
        </div>
        <button class="signout" id="signoutBtn">
          <img src="../assets/out.png" alt="Sign Out">
          Logout
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">

      <?php
        if (!empty($_SESSION['flash_create_user'])) {
            echo '<div style="margin:12px 18px; padding:10px; border-radius:6px; background:#e6ffea; color:#0b6b2b">' . htmlspecialchars($_SESSION['flash_create_user']) . '</div>';
            unset($_SESSION['flash_create_user']);
        }
        if (!empty($_SESSION['flash_create_user_error'])) {
            echo '<div style="margin:12px 18px; padding:10px; border-radius:6px; background:#ffe6e6; color:#a70000">' . htmlspecialchars($_SESSION['flash_create_user_error']) . '</div>';
            unset($_SESSION['flash_create_user_error']);
        }
      ?>

      <!-- ===== USERS SECTION ===== -->
      <section id="userSection" class="content-box active">
        <div class="section-header">

          <!-- SEARCH BAR -->
          <div class="search-container">
            <input type="text" id="userSearchInput" placeholder="Search users...">
          </div>
          <div>
            <button id="openCreateUser" class="create-btn" onclick="(function(){var m=document.getElementById('createUserModal'); if(m) m.style.display='block'; var r=document.getElementById('createRole'); var e=document.getElementById('extraFields'); var a=document.getElementById('createAdvisory'); var lblAdvisory=document.getElementById('lblAdvisory'); var rv = r ? r.value : ''; var showAdvisory = (rv === 'adviser'); if(e) e.style.display = showAdvisory ? 'block' : 'none'; if(a){ a.style.display = showAdvisory ? 'block' : 'none'; a.required = showAdvisory; } if(lblAdvisory) lblAdvisory.style.display = showAdvisory ? 'block' : 'none'; })()" style="margin-left:12px; padding:8px 12px; background:#001f3f; color:#fff; border:none; border-radius:6px; cursor:pointer;">+ Create Account</button>
          </div>
        </div>

        <!-- USERS TABLE -->
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <?php
              // Include server-side users rows so the Users table shows even without JS
              require_once __DIR__ . '/list_users.php';
            ?>
          </tbody>
        </table>
      </section>

      <!-- Create user modal -->
      <div id="createUserModal" style="display:none; position:fixed; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:#fff; width:420px; max-width:95%; margin:80px auto; padding:18px; border-radius:8px; box-shadow:0 6px 30px rgba(0,0,0,0.2);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <h3 style="margin:0; font-size:18px;">Create Account</h3>
            <button id="closeCreateUser" onclick="document.getElementById('createUserModal').style.display='none'" style="background:#e74c3c; color:#fff; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">Close</button>
          </div>
          <div id="createUserAlert" style="display:none; margin-bottom:8px; padding:8px; border-radius:6px;"></div>
          <form id="createUserForm" action="create_user.php" method="post">
            <label>Role</label>
            <select name="role" id="createRole" onchange="(function(r){var e=document.getElementById('extraFields');var a=document.getElementById('createAdvisory');var lblAdvisory=document.getElementById('lblAdvisory'); var showAdvisory=(r==='adviser'); if(e) e.style.display=showAdvisory?'block':'none'; if(a){ a.style.display=showAdvisory?'block':'none'; a.required=showAdvisory; } if(lblAdvisory) lblAdvisory.style.display=showAdvisory?'block':'none'; })(this.value)" style="width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;">
              <option value="teacher">Teacher</option>
              <option value="adviser">Adviser</option>
              <option value="principal">Principal</option>
            </select>

            <label>Name</label>
            <input type="text" name="name" id="createName" style="width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;" required>

            <label>Email</label>
            <input type="email" name="email" id="createEmail" style="width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;" required>

            <div id="extraFields" style="display:none;">
              <label id="lblAdvisory">Advisory</label>
              <input type="text" name="advisory" id="createAdvisory" style="width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <label>Password</label>
            <input type="password" name="password" id="createPassword" style="width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="createConfirm" style="width:100%; padding:8px; margin-bottom:12px; border-radius:6px; border:1px solid #ccc;" required>

            <div style="display:flex; gap:8px; justify-content:flex-end;">
              <button type="submit" id="submitCreateUser" style="background:#001f3f; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Create</button>
              <button type="button" id="cancelCreateUser" onclick="document.getElementById('createUserModal').style.display='none'" style="background:#ccc; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Cancel</button>
            </div>
          </form>
        </div>
      </div>

    </main>
  </div>

  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Admin/scriptAdmin.js"></script>
</body>
</html>
