<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal - Files</title>
  <link rel="stylesheet" href="stylePrincipalDashboard.css">
  <link rel="stylesheet" href="principal_files.css">
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
        <a href="principalDashboard.php">
          <img src="../assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="principal_files.php" class="active">
          <img src="../assets/google-docs.png" alt="Files Icon">
          Files
        </a>
      </nav>

      <!-- SIDEBAR FOOTER -->
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
          <img src="../assets/out.png" alt="Logout Icon">
          Sign Out
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="topbar">
        <h3>Files</h3>
        <p>Manage and review submitted files</p>
      </div>

      <!-- SEARCH BAR (outside files-section) -->
      <div class="search-box" style="margin-bottom:18px; max-width:320px;">
        <input type="text" id="principalFileSearchInput" placeholder="Search files..." style="width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e0e0e0; background:#fafbfc; font-size:1em; box-sizing:border-box; outline:none; transition:border 0.2s;">
      </div>
      <!-- FILES TABLE -->
      <div class="files-section">
        <table class="files-table">
          <thead>
            <tr>
              <th>Filename</th>
              <th>Teacher</th>
              <th>Submitted Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="principalFileTableBody">
            <!-- Data from database will load here -->
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Sign out functionality
      var signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
        signoutBtn.addEventListener('click', function() {
          if(confirm('Are you sure you want to sign out?')) {
            window.location.href = '../Login/logout.php';
          }
        });
      }

      // Principal file search logic
      const searchInput = document.getElementById('principalFileSearchInput');
      const searchClear = document.getElementById('principalFileSearchClear');
      const tableBody = document.getElementById('principalFileTableBody');
      let searchTimeout;

      async function loadPrincipalFiles(q = '') {
        if (tableBody) tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:12px;">Loading...</td></tr>';
        const url = 'search_principal_files.php' + (q ? ('?q=' + encodeURIComponent(q)) : '');
        const res = await fetch(url, { cache: 'no-store' });
        const html = await res.text();
        if (tableBody) tableBody.innerHTML = html;
        attachActionHandlers();
      }

      function attachActionHandlers() {
        document.querySelectorAll('.approve-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            if(confirm('Are you sure you want to approve this file?')) {
              const row = this.closest('tr');
              const statusCell = row.querySelector('.status');
              statusCell.textContent = 'Approved';
              statusCell.classList.remove('pending');
              statusCell.classList.add('approve');
              this.closest('.actions-cell').innerHTML = '<span class="no-action">—</span>';
              // TODO: Add backend update for approval
            }
          });
        });
        document.querySelectorAll('.reject-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            if(confirm('Are you sure you want to reject this file?')) {
              const row = this.closest('tr');
              row.remove(); // Or update status to rejected
              // TODO: Add backend update for rejection
            }
          });
        });
      }

      // Initial load
      loadPrincipalFiles();

      if (searchInput) {
        searchInput.addEventListener('input', function(e) {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => loadPrincipalFiles(searchInput.value.trim()), 200);
        });
        searchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') { e.preventDefault(); clearTimeout(searchTimeout); loadPrincipalFiles(searchInput.value.trim()); }
        });
      }
      if (searchClear && searchInput) {
        searchClear.addEventListener('click', function(e) {
          e.preventDefault(); searchInput.value = ''; searchInput.focus(); loadPrincipalFiles('');
        });
      }
    });
  </script>

</body>
</html>