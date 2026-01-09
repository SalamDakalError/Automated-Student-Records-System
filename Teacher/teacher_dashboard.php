<?php
// ensure session is started before any output so session cookie and vars are available
session_start();

// Function to get teacher dashboard counts (server-side)
function getTeacherDashboardCounts($pdo, $teacherName) {
    try {
        // Get total files uploaded by this teacher
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM teacher_files WHERE teacher_name = :tname");
        $stmt->execute([':tname' => $teacherName]);
        $fileCountRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $file_count = intval($fileCountRow['cnt'] ?? 0);
        
        // Get distinct students from all files of this teacher
        $stmtFiles = $pdo->prepare("SELECT file_path, file_name FROM teacher_files WHERE teacher_name = :tname");
        $stmtFiles->execute([':tname' => $teacherName]);
        $files = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
        
        $uniqueStudents = [];
        foreach ($files as $f) {
            $path = $f['file_path'] ?? $f['file_name'] ?? '';
            $base = pathinfo($path, PATHINFO_BASENAME);
            $baseNoExt = preg_replace('/\.[^.]+$/', '', $base);
            $table = preg_replace('/[^A-Za-z0-9_]/', '_', $baseNoExt);
            if (!$table) continue;
            
            $stmtExists = $pdo->prepare("SHOW TABLES LIKE :t");
            $stmtExists->execute([':t' => $table]);
            $exists = $stmtExists->fetch(PDO::FETCH_NUM);
            if (!$exists) continue;
            
            $colsStmt = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "`");
            $colsStmt->execute();
            $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $studentCol = null;
            if (in_array('student_name', $cols, true)) $studentCol = 'student_name';
            elseif (in_array('name', $cols, true)) $studentCol = 'name';
            elseif (in_array('student', $cols, true)) $studentCol = 'student';
            if (!$studentCol) continue;
            
            $sql = "SELECT DISTINCT `" . $studentCol . "` as s FROM `" . $table . "` WHERE `" . $studentCol . "` IS NOT NULL AND `" . $studentCol . "` != ''";
            $sstmt = $pdo->query($sql);
            $rows = $sstmt ? $sstmt->fetchAll(PDO::FETCH_COLUMN, 0) : [];
            foreach ($rows as $name) {
                $n = trim((string)$name);
                if ($n === '') continue;
                $uniqueStudents[$n] = true;
            }
        }
        
        $student_count = count($uniqueStudents);
        return ['student_count' => $student_count, 'file_count' => $file_count];
    } catch (Exception $e) {
        return ['student_count' => 0, 'file_count' => 0];
    }
}

// Get counts if logged in
$student_count_initial = '—';
$file_count_initial = '—';

// Debug: Check if session has data
error_log('teacher_dashboard.php - Session name: ' . ($_SESSION['name'] ?? 'NOT SET'));

if (!empty($_SESSION['name'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . SITE_BASE . 'Login/db.php';
    $counts = getTeacherDashboardCounts($pdo, $_SESSION['name']);
    $student_count_initial = $counts['student_count'];
    $file_count_initial = $counts['file_count'];
    error_log('teacher_dashboard.php - Counts loaded: student=' . $student_count_initial . ', file=' . $file_count_initial);
} else {
    error_log('teacher_dashboard.php - Session name is empty, counts will be loaded via JavaScript');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>Teacher/teacher_style.css">
  <style>
    .sidebar-footer {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 15px;
      margin-top: auto;
    }
    .user-info {
      width: 100%;
      padding: 0 0 15px 0;
      text-align: center;
      border-bottom: 1px solid #eee;
      margin-bottom: 15px;
    }
    .user-name {
      color: #333;
      font-size: 16px;
      margin: 0;
      font-weight: 600;
    }
    .signout {
      width: 100%;
    }
  </style>
</head>
<body>
  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="<?php echo $base_url; ?>assets/OIP.png" alt="Logo">
    <h1>Teacher</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
      <div class="menu">
        <a href="<?php echo $base_url; ?>Teacher/teacher_dashboard.php" class="active">
          <img src="<?php echo $base_url; ?>assets/dashboard.png" alt="Dashboard Icon">
          Dashboard
        </a>
        <a href="<?php echo $base_url; ?>Teacher/teacher_students.php">
          <img src="<?php echo $base_url; ?>assets/User.png" alt="Students Icon">
          Students
        </a>
        <a href="<?php echo $base_url; ?>Teacher/teacher_files.php">
          <img src="<?php echo $base_url; ?>assets/google-docs.png" alt="Files Icon">
          Files
        </a>
      </div>

      <!-- ===== SIDEBAR FOOTER ===== -->
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
          <img src="<?php echo $base_url; ?>assets/out.png" alt="Logout Icon"> Sign Out
        </button>
      </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <!-- Dashboard Section -->
      <section class="content-box active" id="dashboard">
        <div class="section-header">
          <h2>Dashboard</h2>
          <span style="color: #666; font-size: 14px;">(Overview of Classes)</span>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards-container">
          <div class="summary-card">
            <h3>Total Enrolled Students</h3>
            <p id="studentCount"><?php echo htmlspecialchars($student_count_initial); ?></p>
          </div>
          <div class="summary-card">
            <h3>Total Files</h3>
            <p id="fileCount"><?php echo htmlspecialchars($file_count_initial); ?></p>
          </div>
        </div>
        
        <!-- Store teacher name for JavaScript -->
        <script>
          window.teacherName = <?php echo json_encode($_SESSION['name'] ?? ''); ?>;
        </script>

        <!-- File Table -->
        <div>
          <h3 style="margin-bottom: 10px;">Files</h3>
          <table class="data-table">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Approve Date</th>
              </tr>
            </thead>
            <tbody id="fileTableBody">
              <tr>
                <td colspan="4" style="text-align: center; color: #888;">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- ===== JAVASCRIPT FILE LINK ===== -->
  <script>
    function setupSignout() {
      const signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
        signoutBtn.addEventListener('click', function() {
          window.location.href = '<?php echo $base_url; ?>Login/logout.php';
        });
      }
    }

    function loadDashboardCounts(retries = 0) {
      // Robust fetch with retries to handle timing/session race after login
      const maxRetries = 6;
      const backoff = Math.min(1000, 200 * Math.pow(2, retries)); // exponential backoff up to 1s

      // Build URL and include teacher name if available
      const url = new URL('<?php echo $base_url; ?>Adviser/get_teacher_dashboard_counts.php', window.location.href);
      if (window.teacherName) url.searchParams.append('teacher', window.teacherName);
      url.searchParams.append('t', new Date().getTime());

      fetch(url.toString(), { credentials: 'same-origin' })
        .then(response => {
          if (!response.ok) throw new Error('Network response not ok: ' + response.status);
          return response.json();
        })
        .then(data => {
          console.log('Counts data received:', data);
          const studentCountEl = document.getElementById('studentCount');
          const fileCountEl = document.getElementById('fileCount');

          if (data && data.success) {
            if (studentCountEl && data.student_count !== undefined) studentCountEl.textContent = data.student_count;
            if (fileCountEl && data.file_count !== undefined) fileCountEl.textContent = data.file_count;
          } else {
            // If server returned empty or not-success, retry a few times
            if (retries < maxRetries) {
              console.log('Counts empty or not successful, retrying...', retries + 1);
              setTimeout(() => loadDashboardCounts(retries + 1), backoff);
            } else {
              if (studentCountEl) studentCountEl.textContent = (data && data.student_count !== undefined) ? data.student_count : '—';
              if (fileCountEl) fileCountEl.textContent = (data && data.file_count !== undefined) ? data.file_count : '—';
            }
          }
        })
        .catch(error => {
          console.error('Error loading dashboard counts (attempt ' + (retries + 1) + '):', error);
          if (retries < maxRetries) {
            setTimeout(() => loadDashboardCounts(retries + 1), backoff);
          }
        });
    }

    function loadFilesTable() {
      fetch('<?php echo $base_url; ?>Adviser/list_files_teacher_adviser.php?teacher=1&dashboard=1', {
        credentials: 'same-origin'  // Use same-origin for session handling
      })
        .then(response => {
          console.log('Files response received:', response.status);
          return response.text();
        })
        .then(html => {
          console.log('Files HTML length:', html.length);
          const tbody = document.getElementById('fileTableBody');
          if (tbody) {
            tbody.innerHTML = html;
            console.log('Files loaded into table');
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          const tbody = document.getElementById('fileTableBody');
          if (tbody) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #888;">Error loading files</td></tr>';
          }
        });
    }
    
    // Load as soon as DOM is ready (don't wait for page load)
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM Content Loaded - starting to fetch data');
      setupSignout();
      loadDashboardCounts();
      loadFilesTable();
    });
  </script>
</body>
</html>
