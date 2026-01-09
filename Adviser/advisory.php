<?php session_start(); ?>
<?php
require_once __DIR__ . '/../Login/db.php';

// determine adviser name and assigned advisory (grade/section)
$adviserName = $_SESSION['name'] ?? '';
$assignedAdvisory = '';
if ($adviserName) {
  try {
    $u = $pdo->prepare('SELECT advisory FROM users WHERE name = :name LIMIT 1');
    $u->execute([':name' => $adviserName]);
    $ur = $u->fetch(PDO::FETCH_ASSOC);
    if ($ur && !empty($ur['advisory'])) $assignedAdvisory = $ur['advisory'];
  } catch (Exception $e) {
    $assignedAdvisory = '';
  }
}

// helper to map advisory into same table name format used by importer
function advisory_table_name(string $gradeSection): string {
  $raw = trim($gradeSection);
  $tableName = preg_replace('/[^A-Za-z0-9]+/', '_', strtoupper($raw));
  $tableName = trim($tableName, '_');
  if (empty($tableName)) $tableName = 'ADVISORY_IMPORT';
  return $tableName;
}

// fetch students from adviser table if available
$students = [];
if (!empty($assignedAdvisory)) {
  $tbl = advisory_table_name($assignedAdvisory);
  try {
    // Order so that males come first, then females, then others; within each group sort by name
    $q = "SELECT student_name, gender, grade_level, section FROM `" . str_replace('`','``',$tbl) . "` ORDER BY (CASE WHEN LOWER(gender) = 'male' THEN 0 WHEN LOWER(gender) = 'female' THEN 1 ELSE 2 END), student_name";
    $s = $pdo->query($q);
    if ($s) $students = $s->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $students = [];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advisory</title>
  <!-- Core and Page-Specific CSS -->
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Adviser/styleAdviserDashboard.css">
  <link rel="stylesheet" href="<?php echo $base_url . SITE_BASE; ?>Adviser/styleFiles.css">
  <style>
    /* Simple modal styles for grade display */
    #gradeModal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.5); }
    #gradeModal .modal-content { background:#fff; margin:60px auto; padding:20px; border-radius:6px; width:90%; max-width:900px; }
    #gradeModal .modal-header { display:flex; justify-content:space-between; align-items:center; }
    #gradeModal table { width:100%; border-collapse:collapse; margin-top:12px; }
    #gradeModal th, #gradeModal td { padding:8px; border:1px solid #ddd; text-align:center; }
    #gradeModal .close-btn { cursor:pointer; background:#e74c3c; color:#fff; border:none; padding:6px 10px; border-radius:4px; }
  </style>
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="<?php echo $base_url . SITE_BASE; ?>assets/OIP.png" alt="Logo">
    <h1>Adviser</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/adviserDashboard.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/dashboard.png" alt="">Dashboard</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/student_list.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/User.png" alt="">Students</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/advisory.php" class="active"><img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="">Advisory</a>
        <a href="<?php echo $base_url . SITE_BASE; ?>Adviser/files.php"><img src="<?php echo $base_url . SITE_BASE; ?>assets/google-docs.png" alt="">Files</a>
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
              <h2>Advisory</h2>
              <p style="color: #666;">Overview of Students</p>
            </div>
            <div class="search-box">
              <input type="text" id="advisorySearchInput" placeholder="Search students...">
              <button id="advisorySearchClear" class="clear-btn" aria-label="Clear search" title="Clear">✖</button>
            </div>
          </div>
        </div>

        <!-- ===== UPLOAD (Adviser only) ===== -->
        <div style="margin-bottom:12px; display:flex; align-items:center; gap:12px;">
          <?php if (!empty($adviserName)): ?>
            <?php if (empty($students)): ?>
              <button class="new-file-btn" id="openAdvisoryUploadModal">+ Upload Advisory</button>
            <?php else: ?>
              <span style="color:#2d7a2d;font-weight:600;">Student list loaded</span>
            <?php endif; ?>
            <span style="color:#666;">Assigned advisory: <?php echo htmlspecialchars($assignedAdvisory ?: 'Not assigned'); ?></span>
          <?php else: ?>
            <p style="color:#888;">Log in as an adviser to upload advisory files.</p>
          <?php endif; ?>
        </div>

        <!-- ===== FILES TABLE ===== -->
        <div>
          <table class="data-table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Fullname</th>
                <th>Gender</th>
                <th>Grade Level</th>
                <th>Section</th>
              </tr>
            </thead>
            <tbody id="studentsTbody">
              <?php if (empty($students)): ?>
                <tr><td colspan="5" style="text-align:center;color:#888;">No students available</td></tr>
              <?php else: ?>
                <?php $i = 1; foreach ($students as $st): ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($st['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($st['gender'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($st['grade_level'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($st['section'] ?? ''); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- Grades modal -->
  <div id="gradeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalStudentName">Student Name</h3>
        <button id="gradeModalClose" class="close-btn">Close</button>
      </div>
      <div class="modal-body">
        <table>
          <thead>
            <tr>
              <th>Subject</th>
              <th>Q1</th>
              <th>Q2</th>
              <th>Q3</th>
              <th>Q4</th>
              <th>Final (stored)</th>
            </tr>
          </thead>
          <tbody id="gradesTbody"></tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- ===== ADVISORY UPLOAD MODAL (reuse files.php design) ===== -->
  <div class="upload-modal" id="advisoryUploadModal" style="display:none;">
    <div class="upload-content">
      <button class="close-modal" id="closeAdvisoryUploadModal">✖</button>
      <h3>Upload Advisory</h3>
      <div class="upload-box" id="advisoryDropZone">
        <p id="advisoryDropText">Drag and drop Excel files here</p>
        <span>- OR -</span>
        <button class="browse-btn" id="advisoryBrowseBtn">Browse Files</button>
        <input type="file" id="advisoryFileInput" accept=".xls,.xlsx" style="display:none">
      </div>

      <div class="file-preview" id="advisoryFilePreview" style="display:none;margin-top:12px;">
        <p><strong>Filename:</strong> <span id="advisoryPreviewName"></span></p>
        <p><strong>Grade & Section:</strong> <span id="advisoryPreviewGrade"></span></p>
        <p><strong>Adviser:</strong> <span id="advisoryPreviewAdviser"><?php echo !empty($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Unknown'; ?></span></p>
        <div style="margin-top:8px;"><button id="advisoryUploadBtn">Upload</button> <button id="advisoryCancelBtn">Cancel</button></div>
        <p id="advisoryPreviewError" style="color:#cc0000;display:none;margin-top:8px;"></p>
      </div>
    </div>
  </div>

  <script>
    // Advisory upload modal controls
    (function(){
      const openBtn = document.getElementById('openAdvisoryUploadModal');
      const modal = document.getElementById('advisoryUploadModal');
      const closeBtn = document.getElementById('closeAdvisoryUploadModal');
      const dropZone = document.getElementById('advisoryDropZone');
      const fileInput = document.getElementById('advisoryFileInput');
      const browseBtn = document.getElementById('advisoryBrowseBtn');
      const filePreview = document.getElementById('advisoryFilePreview');
      const previewName = document.getElementById('advisoryPreviewName');
      const previewGrade = document.getElementById('advisoryPreviewGrade');
      const previewAdviser = document.getElementById('advisoryPreviewAdviser');
      const uploadBtn = document.getElementById('advisoryUploadBtn');
      const cancelBtn = document.getElementById('advisoryCancelBtn');
      const previewError = document.getElementById('advisoryPreviewError');
      let selectedFile = null;

      if (openBtn) openBtn.addEventListener('click', () => modal.style.display = 'flex');
      if (closeBtn) closeBtn.addEventListener('click', () => { modal.style.display='none'; reset(); });
      window.addEventListener('click', (e)=>{ if (e.target===modal){ modal.style.display='none'; reset(); } });

      if (browseBtn) browseBtn.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', (e)=> handleFiles(e.target.files));

      ['dragenter','dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); dropZone.classList.add('drag-over'); });
      });
      ['dragleave','drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); dropZone.classList.remove('drag-over'); });
      });
      dropZone.addEventListener('drop', (e)=>{ const dt = e.dataTransfer; if (dt && dt.files && dt.files.length) handleFiles(dt.files); });

      function handleFiles(files){
        previewError.style.display='none';
        const f = files[0];
        if (!f) return;
        const name = f.name;
        const ext = name.split('.').pop().toLowerCase();
        if (!['xls','xlsx'].includes(ext)){ showPreviewError('Only Excel files (.xls, .xlsx) are allowed.'); return; }
        const base = name.replace(/\.[^.]+$/, '');
        const parts = base.split('-');
        // For advisory, we expect at least grade/section in filename or rely on assigned advisory
        let gradeSection = '<?php echo addslashes($assignedAdvisory); ?>';
        if (parts.length >= 2){ gradeSection = parts.slice(1).join('-').trim(); }

        selectedFile = f;
        previewName.textContent = name;
        previewGrade.textContent = gradeSection || '(from assignment)';
        filePreview.style.display = 'block';
      }

      function showPreviewError(msg){ previewError.textContent = msg; previewError.style.display = 'block'; }
      function reset(){ selectedFile=null; previewName.textContent=''; previewGrade.textContent=''; previewError.style.display='none'; filePreview.style.display='none'; fileInput.value=''; }
      cancelBtn.addEventListener('click', ()=> reset());

      uploadBtn.addEventListener('click', async ()=>{
        if (!selectedFile){ showPreviewError('No file selected.'); return; }
        const name = selectedFile.name;
        const base = name.replace(/\.[^.]+$/, '');
        const parts = base.split('-');
        let grade_section = '<?php echo addslashes($assignedAdvisory); ?>';
        if (parts.length >= 2) grade_section = parts.slice(1).join('-').trim();

        const form = new FormData();
        form.append('file', selectedFile);
        form.append('grade_section', grade_section);

        uploadBtn.disabled = true; uploadBtn.textContent = 'Uploading...';
        try{
          const res = await fetch('advisory_upload.php', { method: 'POST', body: form });
          const data = await res.json();
          if (data.success){ alert('Import successful'); modal.style.display='none'; reset(); window.location.reload(); }
          else showPreviewError(data.error || data.message || 'Import failed');
        }catch(err){ showPreviewError('Upload error: ' + err.message); }
        finally{ uploadBtn.disabled=false; uploadBtn.textContent='Upload'; }
      });
    })();
  </script>

  <script>
    window.BASE_URL = '<?php echo $base_url . SITE_BASE; ?>';
  </script>
  <script src="<?php echo $base_url . SITE_BASE; ?>Adviser/scriptAdviser.js"></script>
  </body>
  </html>
