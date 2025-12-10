 // Navigation handling
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOMContentLoaded event fired'); // Debug log
      
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

      // Load dashboard data if on dashboard page
      const fileTableBody = document.getElementById('fileTableBody');
      if (fileTableBody) {
        console.log('Dashboard page detected, loading files...'); // Debug log
        loadDashboardFiles();
        loadStudentCount();
      }
  
      // If we're on the students page, support selecting approved files and showing imported rows
      const approvedSelect = document.getElementById('approvedFileSelect');
      const studentTbody = document.getElementById('studentTableBody');
      let importedRows = null;
      let selectedImportedTable = '';
      if (approvedSelect) {
        // fetch approved files for this teacher
        fetch('../Adviser/get_approved_files.php?teacher=1', { credentials: 'same-origin' })
          .then(r => {
            if (!r.ok) throw new Error('Network response not ok: ' + r.status);
            const ct = r.headers.get('content-type') || '';
            if (ct.includes('application/json')) return r.json();
            return r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error('Invalid JSON response: ' + t); } });
          })
          .then(data => {
            if (!data || !data.success) { console.warn('No approved files or success=false', data); return; }
            data.files.forEach(f => {
              const opt = document.createElement('option');
              // prefer file_path basename when available to construct table name reliably
              let val = '';
              if (f.file_path) {
                const parts = f.file_path.split('/');
                val = parts[parts.length - 1];
              } else if (f.file_name) {
                val = f.file_name;
              } else {
                val = f.id;
              }
              opt.value = val;
              opt.textContent = (f.file_name || f.file_path || f.id) + (f.subject ? ' — ' + f.subject : '');
              approvedSelect.appendChild(opt);
            });
          }).catch((err)=>{ console.error('Error fetching approved files:', err); });

        approvedSelect.addEventListener('change', function(){
          const val = this.value;
          if (!val) {
            importedRows = null; selectedImportedTable = '';
            if (studentTbody) studentTbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#888;">No students available</td></tr>';
            return;
          }
          console.log('approvedSelect change value=', val);
          const base = val.replace(/\.[^.]+$/, '');
          const table = base.replace(/[^A-Za-z0-9_]/g, '_');
          selectedImportedTable = table;
          console.log('fetching imported rows for table=', table);
          fetch('../Adviser/get_imported_rows_with_grades.php?table=' + encodeURIComponent(table))
            .then(r => r.json())
            .then(d => {
              console.log('imported rows response', d);
              if (!studentTbody) return;
              if (!d.success) { studentTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>'; return; }
              importedRows = d.rows || [];
              if (!importedRows.length) { studentTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>'; return; }
              studentTbody.innerHTML = '';
              importedRows.forEach((r, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td>' + (idx+1) + '</td>' + '<td>' + (r.student_name || '') + '</td>' + '<td>' + (r.final_grade ?? r.final ?? '') + '</td>';
                tr.dataset.studentName = r.student_name || '';
                studentTbody.appendChild(tr);
              });
            }).catch((err)=>{ console.error('Error fetching imported rows:', err); });
        });
      }

      // Row click - show overlay modal for imported file grades if modal exists
      const gradeModal = document.getElementById('gradeModal');
      const gradeModalClose = document.getElementById('gradeModalClose');
      const modalStudentName = document.getElementById('modalStudentName');
      const gradesTbody = document.getElementById('gradesTbody');
      if (document.querySelector('.data-table')) {
        document.querySelector('.data-table').addEventListener('click', function(e){
          const tr = e.target.closest('tr');
          if (!tr) return;
          const tds = tr.querySelectorAll('td');
          if (!tds || tds.length < 2) return;
          const studentName = tds[1].textContent.trim();
          if (!studentName) return;
          if (modalStudentName) modalStudentName.textContent = studentName;
          if (gradeModal) gradeModal.style.display = 'block';

          if (selectedImportedTable) {
            // Fetch single student from import table to avoid mismatches caused by extra columns
            gradesTbody.innerHTML = '';
            fetch('../Adviser/get_student_from_import.php?table=' + encodeURIComponent(selectedImportedTable) + '&student_name=' + encodeURIComponent(studentName))
              .then(r => r.json())
              .then(d => {
                if (!gradesTbody) return;
                gradesTbody.innerHTML = '';
                if (!d.success) {
                  const row = document.createElement('tr');
                  const cell = document.createElement('td'); cell.colSpan = 6; cell.textContent = d.message || d.error || 'No grades found'; row.appendChild(cell); gradesTbody.appendChild(row); return;
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
              }).catch(()=>{
                gradesTbody.innerHTML = '';
                const row = document.createElement('tr');
                const cell = document.createElement('td'); cell.colSpan = 6; cell.textContent = 'Error fetching grades'; row.appendChild(cell); gradesTbody.appendChild(row);
              });
            return;
          }
        });
      }
      if (gradeModalClose) gradeModalClose.addEventListener('click', ()=>{ if (gradeModal) gradeModal.style.display='none'; if (gradesTbody) gradesTbody.innerHTML=''; });
    });

    // Load files for the logged-in teacher
    async function loadDashboardFiles() {
      try {
        const res = await fetch('../Adviser/list_files.php?teacher=1');
        const html = await res.text();
        console.log('Files response:', html); // Debug log
        const tbody = document.getElementById('fileTableBody');
        if (tbody) {
          tbody.innerHTML = html;
        }
      } catch (err) {
        console.error('Error loading files:', err);
        const tbody = document.getElementById('fileTableBody');
        if (tbody) {
          tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #888;">Error loading files</td></tr>';
        }
      }
    }

    // Load student and file counts for the dashboard
    function loadStudentCount() {
      const studentCountEl = document.getElementById('studentCount');
      const fileCountEl = document.getElementById('fileCount');

      if (!studentCountEl && !fileCountEl) return;

      fetch('../Adviser/get_dashboard_counts.php?teacher=1', { credentials: 'same-origin' })
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
            console.warn('Failed to load dashboard counts', data);
            return;
          }
          if (studentCountEl) studentCountEl.textContent = data.student_count ?? '0';
          if (fileCountEl) fileCountEl.textContent = data.file_count ?? '0';
        })
        .catch(err => {
          console.error('Error loading dashboard counts:', err);
          if (studentCountEl) studentCountEl.textContent = '—';
          if (fileCountEl) fileCountEl.textContent = '—';
        });
    }