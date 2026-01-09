<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adviser | Files</title>
  <!-- Core and Page-Specific CSS -->
<<<<<<< HEAD
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Adviser/styleAdviserDashboard.css">
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Adviser/styleFiles.css">
=======
  <?php require_once __DIR__ . '/../Login/config.php'; ?>
  <link rel="stylesheet" href="<?= $base_url ?>Adviser/styleAdviserDashboard.css">
  <link rel="stylesheet" href="<?= $base_url ?>Adviser/stylefiles.css">
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
    <h1>Adviser</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
<<<<<<< HEAD
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/adviserDashboard.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/dashboard.png" alt="">Dashboard</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/student_list.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/User.png" alt="">Students</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/advisory.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="">Advisory</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/files.php" class="active"><img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="">Files</a>
=======
        <a href="<?= $base_url ?>Adviser/adviserDashboard.php"><img src="<?= $base_url ?>assets/dashboard.png" alt="">Dashboard</a>
        <a href="<?= $base_url ?>Adviser/student_list.php"><img src="<?= $base_url ?>assets/User.png" alt="">Students</a>
        <a href="<?= $base_url ?>Adviser/advisory.php"><img src="<?= $base_url ?>assets/google-docs.png" alt="">Advisory</a>
        <a href="<?= $base_url ?>Adviser/files.php" class="active"><img src="<?= $base_url ?>assets/google-docs.png" alt="">Files</a>
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
      </div>

      <!-- ===== SIDEBAR FOOTER ===== -->
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
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/out.png" alt="Logout Icon">
=======
          <img src="<?= $base_url ?>assets/out.png" alt="Logout Icon">
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf
          Sign Out
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <section class="content-box active" id="files">
        <div class="section-header">
          <div class="title-group">
            <div>
              <h2>Files</h2>
              <p style="color: #666;">Overview of Students</p>
            </div>
            <div class="search-box">
              <input type="text" id="fileSearchInput" placeholder="Search files...">
              <button id="fileSearchClear" class="clear-btn" aria-label="Clear search" title="Clear">✖</button>
            </div>
          </div>
          <div>
            <button class="new-file-btn" id="openUploadModal">+ New</button>
          </div>
        </div>

        <!-- ===== FILES TABLE ===== -->
        <div>
          <table class="data-table">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Approve Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="filesTableBody">
              <!-- Data from database will load here -->
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- ===== UPLOAD MODAL ===== -->
  <div class="upload-modal" id="uploadModal">
    <div class="upload-content">
      <button class="close-modal" id="closeUploadModal">✖</button>
      <h3>Upload Files</h3>
      <div class="upload-box" id="dropZone">
        <p>Drag and drop files here</p>
        <span>- OR -</span>
        <button class="browse-btn">Browse Files</button>
      </div>
    </div>
  </div>

  <script>
    const openModal = document.getElementById("openUploadModal");
    const closeModal = document.getElementById("closeUploadModal");
    const modal = document.getElementById("uploadModal");

    openModal.addEventListener("click", () => modal.style.display = "flex");
    closeModal.addEventListener("click", () => modal.style.display = "none");
    window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
  </script>
<<<<<<< HEAD
  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Adviser/scriptAdviser.js"></script>
=======
  <script src="<?= $base_url ?>Adviser/scriptAdviser.js"></script>
>>>>>>> 992314625673de62f89b7894eae8d5c6b20176cf

</body>
</html>
