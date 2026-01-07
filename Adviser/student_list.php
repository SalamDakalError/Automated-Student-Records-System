<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adviser | Students</title>
  <link rel="stylesheet" href="styleAdviserDashboard.css">
  <link rel="stylesheet" href="stylefiles.css">
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="../assets/OIP.png" alt="Logo">
    <h1>Adviser</h1>
  </header>

  <!-- ===== DASHBOARD CONTAINER ===== -->
  <div class="dashboard-container">
    
    <aside class="sidebar">
      <div class="menu">
        <a href="adviserDashboard.php"><img src="../assets/dashboard.png" alt="">Dashboard</a>
        <a href="student_list.php" class="active"><img src="../assets/User.png" alt="">Students</a>
        <a href="advisory.php"><img src="../assets/google-docs.png" alt="">Advisory</a>
        <a href="files.php"><img src="../assets/google-docs.png" alt="">Files</a>
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
          <img src="../assets/out.png" alt="Logout Icon">
          Sign Out
        </button>
      </div>
    </aside>

    <main class="main-content">
      <section class="content-box active" id="students">
        <div class="section-header">
          <div class="title-group">
            <div>
              <h2>Students</h2>
              <p style="color: #666;">Overview of Students</p>
            </div>
            <div class="search-box">
              <input type="text" id="studentSearchInput" placeholder="Search students...">
              <button id="studentSearchClear" class="clear-btn" aria-label="Clear search" title="Clear">✖</button>
            </div>
          </div>
        </div>

        <div style="background-color: #d9e8ef; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
          <div style="display:flex; gap:12px; align-items:center;">
            <div>
              <label>Approved Files:</label>
              <select id="approvedFileSelect" style="padding:6px 10px; border-radius:6px; border:1px solid #ccc; min-width:220px;">
                <option value="">-- Select a file --</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Students Table -->
        <div>
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 60px;">No.</th>
                <th>Fullname</th>
                <th style="width: 120px;">Grade</th>
              </tr>
            </thead>
            <tbody id="studentTableBody">
                <tr>
                  <td colspan="3" style="text-align: center; color: #888;">No students available</td>
                </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div> <!-- end dashboard container -->

  <!-- Grades modal -->
  <div id="gradeModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background:#fff; margin:60px auto; padding:20px; border-radius:6px; width:90%; max-width:900px;">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 id="modalStudentName">Student Name</h3>
        <button id="gradeModalClose" class="close-btn" style="cursor:pointer; background:#e74c3c; color:#fff; border:none; padding:6px 10px; border-radius:4px;">Close</button>
      </div>
      <div class="modal-body">
          <table style="width:100%; border-collapse:collapse; margin-top:12px;">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Q1</th>
              <th>Q2</th>
              <th>Q3</th>
              <th>Q4</th>
              <th>Final</th>
            </tr>
          </thead>
          <tbody id="gradesTbody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ===== JAVASCRIPT FILE LINK ===== -->
  <script src="scriptAdviser.js"></script>
</body>
</html>
