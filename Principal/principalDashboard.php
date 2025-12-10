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
  <link rel="stylesheet" href="stylePrincipalDashboard.css?v=<?php echo time(); ?>">
</head>
<body>
  <!-- HEADER BAR -->
  <header class="header">
    <img src="../assets/OIP.png" alt="DepEd Logo">
    <h1>Principal</h1>
  </header>
  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <nav class="menu">
        <a href="principal_dashboard.php" class="active">
          <img src="../assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="principal_files.php">
          <img src="../assets/google-docs.png" alt="Files Icon">
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
          <img src="../assets/out.png" alt="Logout Icon">
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
  <script src="scriptPrincipal.js"></script>
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


