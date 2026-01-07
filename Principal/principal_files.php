<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../Login/config.php'; ?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Principal - Files</title>
  <link rel="stylesheet" href="<?= $base_url ?>Principal/stylePrincipalDashboard.css">
  <link rel="stylesheet" href="<?= $base_url ?>Principal/principal_files.css">
</head>
<body>
  </div>

  <script src="<?= $base_url ?>Principal/scriptPrincipal.js"></script>
</body>
</html>
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