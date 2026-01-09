<?php
// ensure session is started before any output so session cookie and vars are available
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal Dashboard</title>
<<<<<<< HEAD
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Principal/stylePrincipalDashboard.css?v=<?php echo time(); ?>">
=======
  <?php require_once '../Login/config.php'; ?>
  <link rel="stylesheet" href="<?= $base_url ?>Principal/stylePrincipalDashboard.css?v=<?= time(); ?>">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
</head>
<body>
  <!-- HEADER BAR -->
  <header class="header">
<<<<<<< HEAD
    <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" alt="DepEd Logo">
=======
    <img src="<?= $base_url ?>assets/OIP.png" alt="DepEd Logo">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
    <h1>Principal</h1>
  </header>
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <nav class="menu">
<<<<<<< HEAD
        <a href="<?php echo $base_url . SITE_BASE; ?>Principal/principalDashboard.php" class="active">
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Principal/principal_files.php">
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="Files Icon">
=======
        <a href="<?= $base_url ?>Principal/principalDashboard.php" class="active">
          <img src="<?= $base_url ?>assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="<?= $base_url ?>Principal/principal_files.php">
          <img src="<?= $base_url ?>assets/google-docs.png" alt="Files Icon">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
          Files
        </a>
      </nav>
      <!-- SIDEBAR FOOTER -->
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
<<<<<<< HEAD
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/out.png" alt="Logout Icon">
=======
          <img src="<?= $base_url ?>assets/out.png" alt="Logout Icon">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
          Sign Out
        </button>
      </div>
    </aside>
    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="topbar">
        <h3>Dashboard</h3>
        <p>(Overview of Students)</p>
      </div>
      <!-- STATS CARDS -->
      <div class="stats">
        <div class="card">
          <p>Total Pending</p>
          <div class="blue-line" id="totalPending">—</div>
        </div>
        <div class="card">
          <p>Total Files</p>
          <div class="blue-line" id="totalFiles">—</div>
        </div>
      </div>
      <!-- FILES TABLE -->
      <div class="files-section">
        <h3>Files</h3>
        <table>
          <thead>
            <tr>
              <th>Filename</th>
              <th>Teacher</th>
              <th>Submitted Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="filesTableBody">
            <tr>
              <td colspan="4" class="no-data">Loading files...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <!-- JavaScript -->
<<<<<<< HEAD
  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Principal/scriptPrincipal.js"></script>
=======
  <script src="<?= $base_url ?>Principal/scriptPrincipal.js"></script>
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
  <script>
    console.log('Principal Dashboard inline script loaded');
    // Load all files and dashboard counts when page loads
    window.addEventListener('load', function() {
      console.log('Page fully loaded, fetching all files and counts...');
      
      // Load dashboard counts
      fetch('../Adviser/get_principal_dashboard_counts.php')
        .then(response => {
          if (!response.ok) throw new Error('Network response not ok');
          return response.json();
        })
        .then(data => {
          if (data.success) {
            const totalPendingEl = document.getElementById('totalPending');
            const totalFilesEl = document.getElementById('totalFiles');
            if (totalPendingEl) totalPendingEl.textContent = data.total_pending ?? '0';
            if (totalFilesEl) totalFilesEl.textContent = data.total_files ?? '0';
          }
        })
        .catch(error => {
          console.error('Error loading dashboard counts:', error);
          const totalPendingEl = document.getElementById('totalPending');
          const totalFilesEl = document.getElementById('totalFiles');
          if (totalPendingEl) totalPendingEl.textContent = '—';
          if (totalFilesEl) totalFilesEl.textContent = '—';
        });
      
      // Load files table
      fetch('../Adviser/list_files_principal.php?dashboard=1')
        .then(response => {
          console.log('Response received:', response.status);
          return response.text();
        })
        .then(html => {
          console.log('Files HTML:', html);
          const tbody = document.getElementById('filesTableBody');
          if (tbody) {
            tbody.innerHTML = html;
            console.log('Files loaded into table');
          } else {
            console.log('filesTableBody element not found');
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          const tbody = document.getElementById('filesTableBody');
          if (tbody) {
            tbody.innerHTML = '<tr><td colspan="4" class="no-data" style="color:red;">Error loading files</td></tr>';
          }
        });
    });
  </script>
</body>
</html>


