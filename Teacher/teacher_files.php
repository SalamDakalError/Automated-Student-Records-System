<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher | Files</title>
  <!-- Core and Page-Specific CSS -->
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Teacher/teacher_style.css">
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Teacher/teacher_files.css">
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Adviser/styleFiles.css">
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
  <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" alt="Logo">
  <h1>Teacher</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="<?php echo $base_url . SITE_BASE; ?>Teacher/teacher_dashboard.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/dashboard.png" alt="">Dashboard</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Teacher/teacher_students.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/User.png" alt="">Students</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Teacher/teacher_files.php" class="active"><img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="">Files</a>
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
          <img src="<?php echo $base_url . SITE_BASE; ?>assets/out.png" alt="Logout Icon">
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
              <input type="text" id="teacherFileSearchInput" placeholder="Search files...">
              <button id="teacherFileSearchClear" class="clear-btn" aria-label="Clear search" title="Clear">✖</button>
            </div>
          </div>
          <button class="new-file-btn" id="openUploadModal">+ New</button>
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
            <tbody id="fileTableBody">
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
        <p id="dropText">Drag and drop Excel files here</p>
        <span>- OR -</span>
        <button class="browse-btn" id="browseFilesBtn">Browse Files</button>
        <input type="file" id="hiddenFileInput" style="display:none;" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
      </div>
      <div class="file-preview" id="filePreview" style="display:none;margin-top:12px;">
        <p><strong>Filename:</strong> <span id="previewName"></span></p>
        <p><strong>Grade & Section:</strong> <span id="previewGrade"></span></p>
        <p><strong>Teacher:</strong> <span id="previewTeacher"><?php echo !empty($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Unknown'; ?></span></p>
        <div style="margin-top:8px;"><button id="uploadBtn">Upload</button> <button id="cancelBtn">Cancel</button></div>
        <p id="previewError" style="color:#cc0000;display:none;margin-top:8px;"></p>
      </div>
    </div>
  </div>

  <script>
    const openModal = document.getElementById("openUploadModal");
    const closeModal = document.getElementById("closeUploadModal");
    const modal = document.getElementById("uploadModal");
    const browseBtn = document.getElementById("browseFilesBtn");
    const hiddenFileInput = document.getElementById("hiddenFileInput");
    const dropZone = document.getElementById("dropZone");
    const dropText = document.getElementById("dropText");
    const filePreview = document.getElementById("filePreview");
    const previewName = document.getElementById("previewName");
    const previewGrade = document.getElementById("previewGrade");
    const previewTeacher = document.getElementById("previewTeacher");
    const uploadBtn = document.getElementById("uploadBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const previewError = document.getElementById("previewError");
    let selectedFile = null;

    openModal.addEventListener("click", () => { modal.style.display = "flex"; resetUpload(); });
    closeModal.addEventListener("click", () => { modal.style.display = "none"; resetUpload(); });
    window.onclick = (e) => { if (e.target === modal) { modal.style.display = "none"; resetUpload(); } };

    if (browseBtn && hiddenFileInput) {
      browseBtn.addEventListener("click", function(e) {
        e.preventDefault();
        hiddenFileInput.click();
      });
      hiddenFileInput.addEventListener("change", function(e) {
        handleFiles(hiddenFileInput.files);
      });
    }

    // Drag and drop support
    if (dropZone && hiddenFileInput) {
      dropZone.addEventListener("dragover", function(e) {
        e.preventDefault();
        dropZone.style.background = "#e3f2fd";
      });
      dropZone.addEventListener("dragleave", function(e) {
        e.preventDefault();
        dropZone.style.background = "";
      });
      dropZone.addEventListener("drop", function(e) {
        e.preventDefault();
        dropZone.style.background = "";
        if (e.dataTransfer && e.dataTransfer.files) {
          handleFiles(e.dataTransfer.files);
        }
      });
    }

    function handleFiles(files) {
      previewError.style.display = 'none';
      const f = files[0];
      if (!f) return;
      const name = f.name;
      const ext = name.split('.').pop().toLowerCase();
      if (!['xls','xlsx'].includes(ext)) { showPreviewError('Only Excel files (.xls, .xlsx) are allowed.'); return; }
      const base = name.replace(/\.[^.]+$/, '');
      const parts = base.split('-');
      let gradeSection = '';
      if (parts.length >= 2) { gradeSection = parts.slice(1).join('-').trim(); }
      selectedFile = f;
      previewName.textContent = name;
      previewGrade.textContent = gradeSection || '(specify in filename)';
      filePreview.style.display = 'block';
    }

    function showPreviewError(msg) { previewError.textContent = msg; previewError.style.display = 'block'; }
    function resetUpload() { selectedFile = null; previewName.textContent = ''; previewGrade.textContent = ''; previewError.style.display = 'none'; filePreview.style.display = 'none'; hiddenFileInput.value = ''; }
    if (cancelBtn) cancelBtn.addEventListener('click', resetUpload);

    if (uploadBtn) uploadBtn.addEventListener('click', async function() {
      if (!selectedFile) { showPreviewError('No file selected.'); return; }
      const name = selectedFile.name;
      const base = name.replace(/\.[^.]+$/, '');
      const parts = base.split('-');
      let grade_section = '';
      if (parts.length >= 2) grade_section = parts.slice(1).join('-').trim();
      const form = new FormData();
      form.append('file', selectedFile);
      form.append('grade_section', grade_section);
      uploadBtn.disabled = true; uploadBtn.textContent = 'Uploading...';
      try {
        const res = await fetch('upload.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) { alert('Upload successful'); modal.style.display = 'none'; resetUpload(); window.location.reload(); }
        else showPreviewError(data.error || data.message || 'Upload failed');
      } catch (err) { showPreviewError('Upload error: ' + err.message); }
      finally { uploadBtn.disabled = false; uploadBtn.textContent = 'Upload'; }
    });
  </script>
  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Teacher/scriptTeacher.js"></script>

</body>
</html>
