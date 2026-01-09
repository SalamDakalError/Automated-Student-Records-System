<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Logs</title>
<<<<<<< HEAD
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Admin/styleAdmin.css">
=======
  <?php require_once '../Login/config.php'; ?>
  <link rel="stylesheet" href="<?= $base_url ?>Admin/styleAdmin.css">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
<<<<<<< HEAD
    <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" alt="Logo">
=======
    <img src="<?= $base_url ?>assets/OIP.png" alt="Logo">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
    <h1>Admin Dashboard</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="menu">
<<<<<<< HEAD
        <a href="<?php echo $base_url . SITE_BASE; ?>Admin/adminDashboard.php" id="userTab">
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/dashboard.png" alt="Dashboard">
          Users
        </a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Admin/adminLogs.php" id="logsTab" class="active">
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/User.png" alt="Logs">
=======
        <a href="<?= $base_url ?>Admin/adminDashboard.php" id="userTab">
          <img src="<?= $base_url ?>assets/dashboard.png" alt="Dashboard">
          Users
        </a>
        <a href="<?= $base_url ?>Admin/adminLogs.php" id="logsTab" class="active">
          <img src="<?= $base_url ?>assets/User.png" alt="Logs">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
          Logs
        </a>
      </div>

      <div class="sidebar-footer">
        <div class="user-info">
          <?php
            if (!empty($_SESSION['name'])) {
                echo '<p class="user-name">' . htmlspecialchars($_SESSION['name']) . '</p>';
            } else {
                echo '<p class="user-name">Not logged in</p>';
            }
          ?>
        </div>
        <button class="signout" id="signoutBtn">
<<<<<<< HEAD
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/out.png" alt="Sign Out">
=======
          <img src="<?= $base_url ?>assets/out.png" alt="Sign Out">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
          Logout
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <section class="content-box active">
        <div class="section-header">
          <div class="search-container">
            <input type="text" id="logsSearchInput" placeholder="Search logs...">
          </div>
        </div>

        <!-- LOGS TABLE -->
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Filename</th>
              <th>Status</th>
              <th>Submitted Date</th>
              <th>Teacher Name</th>
              <th>Approved Date</th>
            </tr>
          </thead>
          <tbody id="logsTableBody">
            <?php
              // Include the rows generator (it outputs <tr> rows)
              require_once __DIR__ . '/list_file_logs.php';
            ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Admin/scriptAdmin.js"></script>
</body>
</html>
