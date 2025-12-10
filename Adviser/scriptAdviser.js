// ===== Navigation handling =====
document.addEventListener('DOMContentLoaded', function() {
  // Sign out button handling
  const signoutBtn = document.getElementById('signoutBtn');
  if (signoutBtn) {
    signoutBtn.addEventListener('click', function() {
      // Navigate to logout.php which destroys the session and redirects
      window.location.href = '../Login/logout.php';
    });
  }

  // Get all sidebar links
  const sidebarLinks = document.querySelectorAll('.sidebar .menu a');
  
  // Add click event to each link
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Remove active class from all links
      sidebarLinks.forEach(l => l.classList.remove('active'));
      
      // Add active class to clicked link
      this.classList.add('active');
      
      // Allow default navigation to proceed
    });
  });

  // Row click -> show grades modal
  const dataTable = document.querySelector('.data-table');
  const modal = document.getElementById('gradeModal');
  const modalClose = document.getElementById('gradeModalClose');
  const modalStudentName = document.getElementById('modalStudentName');
  const gradesTbody = document.getElementById('gradesTbody');
  let importedRows = null;
  let selectedImportedTable = '';

  // Approved files select (if present) - load approved files for current user
  const approvedSelect = document.getElementById('approvedFileSelect');
  if (approvedSelect) {
    fetch('get_approved_files.php?teacher=1')
      .then(r => r.json())
      .then(data => {
        if (!data || !data.success) return;
        data.files.forEach(f => {
          const opt = document.createElement('option');
          opt.value = f.file_name || f.file_path || f.id;
          opt.dataset.subject = f.subject || '';
          opt.dataset.grade = f.grade_section || '';
          opt.dataset.filepath = f.file_path || '';
          opt.textContent = (f.file_name || f.file_path || f.id) + (f.subject ? ' — ' + f.subject : '');
          approvedSelect.appendChild(opt);
        });
      }).catch(()=>{});

    approvedSelect.addEventListener('change', function(){
      const val = this.value;
      if (!val) {
        importedRows = null;
        selectedImportedTable = '';
        const tbody = document.getElementById('studentTableBody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>';
        return;
      }

      const base = val.replace(/\.[^.]+$/, '');
      const table = base.replace(/[^A-Za-z0-9_]/g, '_');
      selectedImportedTable = table;
      fetch('get_imported_rows_with_grades.php?table=' + encodeURIComponent(table))
        .then(r => r.json())
        .then(d => {
          const tbody = document.getElementById('studentTableBody');
          if (!tbody) return;
          if (!d.success) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>';
            return;
          }
          importedRows = d.rows || [];
          if (!importedRows.length) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>';
            return;
          }
          tbody.innerHTML = '';
          importedRows.forEach((r, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.studentName = r.student_name || '';
            tr.innerHTML = '<td>' + (idx+1) + '</td>' +
                           '<td>' + (r.student_name || '') + '</td>' +
                           '<td>' + (r.final_grade ?? r.final ?? '') + '</td>';
            tbody.appendChild(tr);
          });
        }).catch(()=>{});
    });
  }

  function closeModal() {
    if (modal) modal.style.display = 'none';
    if (gradesTbody) gradesTbody.innerHTML = '';
  }

  if (modalClose) modalClose.addEventListener('click', closeModal);
  window.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
  });

  if (dataTable) {
    dataTable.addEventListener('click', function(e) {
      let tr = e.target.closest('tr');
      if (!tr) return;
      const tds = tr.querySelectorAll('td');
      if (!tds || tds.length < 2) return;
      
      const studentName = tds[1].textContent.trim();
      if (!studentName) return;
      if (modalStudentName) modalStudentName.textContent = studentName;
      if (modal) modal.style.display = 'block';

      // If an imported file is selected, fetch that single student's grades from server
      if (selectedImportedTable) {
        gradesTbody.innerHTML = '';
        fetch('get_student_from_import.php?table=' + encodeURIComponent(selectedImportedTable) + '&student_name=' + encodeURIComponent(studentName))
          .then(r => r.json())
          .then(d => {
            if (!gradesTbody) return;
            gradesTbody.innerHTML = '';
            if (!d.success) {
              const row = document.createElement('tr');
              const cell = document.createElement('td');
              cell.colSpan = 6;
              cell.textContent = d.message || d.error || 'No grades found';
              row.appendChild(cell);
              gradesTbody.appendChild(row);
              return;
            }
            const m = d.row;
            const subj = d.subject || '';
            const fmt = v => (v === null || v === undefined || v === '') ? '' : (Math.round(parseFloat(v) * 100) / 100).toFixed(2);
            const row = document.createElement('tr');
            row.innerHTML = '<td>' + subj + '</td>' +
                            '<td>' + fmt(m.q1 ?? m.q1) + '</td>' +
                            '<td>' + fmt(m.q2 ?? m.q2) + '</td>' +
                            '<td>' + fmt(m.q3 ?? m.q3) + '</td>' +
                            '<td>' + fmt(m.q4 ?? m.q4) + '</td>' +
                            '<td>' + fmt(m.final ?? m.final) + '</td>';
            gradesTbody.appendChild(row);
          }).catch(err => {
            gradesTbody.innerHTML = '';
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 6;
            cell.textContent = 'Error fetching grades';
            row.appendChild(cell);
            gradesTbody.appendChild(row);
          });
        return;
      }

      // Fallback to original behavior for the advisory table
      if (!tds || tds.length < 5) return;
      const gradeLevel = tds[3].textContent.trim();
      const section = tds[4].textContent.trim();

      // Fetch grades
      const debugFlag = window.location.search.indexOf('debug=1') !== -1 ? '1' : '0';
      fetch('../Adviser/get_student_grades_clean.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ grade_level: gradeLevel, section: section, student_name: studentName, debug: debugFlag })
      }).then(r => r.json()).then(data => {
        if (!gradesTbody) return;
        gradesTbody.innerHTML = '';
        if (!data.success) {
          const row = document.createElement('tr');
          const cell = document.createElement('td');
          cell.colSpan = 6;
          cell.textContent = data.message || 'No grades found';
          row.appendChild(cell);
          gradesTbody.appendChild(row);
          return;
        }

        data.grades.forEach(g => {
          const row = document.createElement('tr');
          row.innerHTML = '<td>' + (g.subject || g.table) + '</td>' +
                          '<td>' + (g.q1 ?? '') + '</td>' +
                          '<td>' + (g.q2 ?? '') + '</td>' +
                          '<td>' + (g.q3 ?? '') + '</td>' +
                          '<td>' + (g.q4 ?? '') + '</td>' +
                          '<td>' + (g.final_stored ?? '') + '</td>';
          gradesTbody.appendChild(row);
        });

        // Append GWA row (computed from stored finals)
        const gwa = (data.gwa !== undefined && data.gwa !== null) ? data.gwa : '';
        const gwaRow = document.createElement('tr');
        const gwaCell = document.createElement('td');
        gwaCell.colSpan = 5;
        gwaCell.style.textAlign = 'right';
        gwaCell.innerHTML = '<strong>GWA</strong>';
        const gwaValCell = document.createElement('td');
        gwaValCell.textContent = gwa;
        gwaRow.appendChild(gwaCell);
        gwaRow.appendChild(gwaValCell);
        gradesTbody.appendChild(gwaRow);
      }).catch(err => {
        if (!gradesTbody) return;
        gradesTbody.innerHTML = '';
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 7;
        cell.textContent = 'Error fetching grades';
        row.appendChild(cell);
        gradesTbody.appendChild(row);
      });
    });
  }

  // advisory upload handled by modal in advisory.php
});

// Load dashboard counts for adviser
function loadDashboardCounts() {
  const studentCountEl = document.getElementById('studentCount');
  const fileCountEl = document.getElementById('fileCount');
  const honorstudentEl = document.getElementById('honorstudent');

  if (!studentCountEl && !fileCountEl && !honorstudentEl) return;

  fetch('get_dashboard_counts.php?teacher=1', { credentials: 'same-origin' })
    .then(r => {
      if (!r.ok) throw new Error('Network response not ok: ' + r.status);
      const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) return r.json();
      return r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error('Invalid JSON response: ' + t); } });
    })
    .then(data => {
      if (!data || !data.success) {
        if (studentCountEl) studentCountEl.textContent = '—';
        if (fileCountEl) fileCountEl.textContent = '—';
        if (honorstudentEl) honorstudentEl.textContent = '—';
        console.warn('Failed to load dashboard counts', data);
        return;
      }
      if (studentCountEl) studentCountEl.textContent = data.student_count ?? '0';
      if (fileCountEl) fileCountEl.textContent = data.file_count ?? '0';
      if (honorstudentEl) honorstudentEl.textContent = data.honors_count ?? '0';
    })
    .catch(err => {
      console.error('Error loading dashboard counts:', err);
      if (studentCountEl) studentCountEl.textContent = '—';
      if (fileCountEl) fileCountEl.textContent = '—';
      if (honorstudentEl) honorstudentEl.textContent = '—';
    });
}
