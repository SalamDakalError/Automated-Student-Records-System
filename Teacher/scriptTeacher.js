 // Navigation handling
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOMContentLoaded event fired'); // Debug log
      
      // Sign out button handling
      const signoutBtn = document.getElementById('signoutBtn');
      if (signoutBtn) {
          signoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to sign out?')) {
              window.location.href = (window.BASE_URL || '../') + 'Login/logout.php';
            }
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
        // wire up search on files page if present
        const teacherFileSearchInput = document.getElementById('teacherFileSearchInput');
        const teacherFileSearchClear = document.getElementById('teacherFileSearchClear');
        if (teacherFileSearchInput) {
          let fileTimeout;
          teacherFileSearchInput.addEventListener('input', function(e) {
            clearTimeout(fileTimeout);
            fileTimeout = setTimeout(() => loadDashboardFiles(teacherFileSearchInput.value.trim()), 200);
          });
          teacherFileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); clearTimeout(fileTimeout); loadDashboardFiles(teacherFileSearchInput.value.trim()); }
          });
          if (teacherFileSearchClear) {
            teacherFileSearchClear.addEventListener('click', function(e){ e.preventDefault(); teacherFileSearchInput.value = ''; teacherFileSearchInput.focus(); loadDashboardFiles(''); });
          }
        }
        // Only load counts if they are not already rendered by the page
        const studentCountEl = document.getElementById('studentCount');
        const fileCountEl = document.getElementById('fileCount');
        const needCounts = !studentCountEl || ['—', '', '0'].includes(studentCountEl.textContent.trim());
        if (needCounts) {
          loadStudentCount();
        } else {
          console.log('Counts already present on page, skipping fetch to avoid overwrite');
        }
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
            // Clear existing options except the first placeholder
            while (approvedSelect.options.length > 1) {
              approvedSelect.remove(1);
            }
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
          // Store current search value if any
          const teacherSearchInput = document.getElementById('teacherStudentSearchInput');
          const searchVal = teacherSearchInput ? teacherSearchInput.value.trim() : '';
          fetch('get_imported_rows_with_grades.php?table=' + encodeURIComponent(table) + (searchVal ? ('&q=' + encodeURIComponent(searchVal)) : ''))
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
                const grade = r.final_grade ?? r.final ?? '—';
                tr.innerHTML = '<td>' + (idx+1) + '</td>' + '<td>' + (r.student_name || '') + '</td>' + '<td>' + grade + '</td>';
                tr.dataset.studentName = r.student_name || '';
                studentTbody.appendChild(tr);
              });
            }).catch((err)=>{ console.error('Error fetching imported rows:', err); });
        });

          // Teacher student search: backend-powered search
          const teacherSearchInput = document.getElementById('teacherStudentSearchInput');
          const teacherSearchClear = document.getElementById('teacherStudentSearchClear');
          if (teacherSearchInput && studentTbody) {
            let ttimeout;
            function fetchAndRenderStudents(q) {
              if (!selectedImportedTable) return;
              fetch('get_imported_rows_with_grades.php?table=' + encodeURIComponent(selectedImportedTable) + (q ? ('&q=' + encodeURIComponent(q)) : ''))
                .then(r => r.json())
                .then(d => {
                  if (!studentTbody) return;
                  if (!d.success) { studentTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>'; return; }
                  importedRows = d.rows || [];
                  if (!importedRows.length) { studentTbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#888;">No students available</td></tr>'; return; }
                  studentTbody.innerHTML = '';
                  importedRows.forEach((r, idx) => {
                    const tr = document.createElement('tr');
                    const grade = r.final_grade ?? r.final ?? '—';
                    tr.innerHTML = '<td>' + (idx+1) + '</td>' + '<td>' + (r.student_name || '') + '</td>' + '<td>' + grade + '</td>';
                    tr.dataset.studentName = r.student_name || '';
                    studentTbody.appendChild(tr);
                  });
                });
            }
            teacherSearchInput.addEventListener('input', (e) => { clearTimeout(ttimeout); ttimeout = setTimeout(()=>fetchAndRenderStudents(e.target.value.trim()), 150); });
            teacherSearchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); clearTimeout(ttimeout); fetchAndRenderStudents(teacherSearchInput.value.trim()); } });
            if (teacherSearchClear) teacherSearchClear.addEventListener('click', (e) => { e.preventDefault(); teacherSearchInput.value=''; teacherSearchInput.focus(); fetchAndRenderStudents(''); });
          }
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
            // Get the subject from the selected option's text (which includes subject name)
            const selectedOption = approvedSelect.options[approvedSelect.selectedIndex];
            const optionText = selectedOption ? selectedOption.textContent : '';
            // Extract subject from the text (it's after the " — " separator)
            let subjectToPass = '';
            const dashIdx = optionText.indexOf(' — ');
            if (dashIdx > 0) {
              subjectToPass = optionText.substring(dashIdx + 3).trim();
            }
            console.log('Modal opened: optionText="' + optionText + '", dashIdx=' + dashIdx + ', subjectToPass="' + subjectToPass + '"');
            fetch('../Adviser/get_student_from_import.php?table=' + encodeURIComponent(selectedImportedTable) + '&student_name=' + encodeURIComponent(studentName) + (subjectToPass ? '&subject=' + encodeURIComponent(subjectToPass) : ''))
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
                let finalVal = m.final ?? m.final;
                let remarks = '';
                if (
                  finalVal !== null &&
                  finalVal !== undefined &&
                  finalVal !== '' &&
                  !isNaN(finalVal) &&
                  parseFloat(finalVal) !== 0
                ) {
                  remarks = parseFloat(finalVal) >= 75 ? 'Passed' : 'Failed';
                }
                row.innerHTML = '<td>' + subj + '</td>' +
                                '<td>' + fmt(m.q1 ?? m.q1) + '</td>' +
                                '<td>' + fmt(m.q2 ?? m.q2) + '</td>' +
                                '<td>' + fmt(m.q3 ?? m.q3) + '</td>' +
                                '<td>' + fmt(m.q4 ?? m.q4) + '</td>' +
                                '<td>' + fmt(finalVal) + '</td>' +
                                '<td>' + remarks + '</td>';
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
    async function loadDashboardFiles(query = '') {
      try {
        const base = '../Adviser/list_files.php?teacher=1';
        const url = base + (query ? '&q=' + encodeURIComponent(query) : '');
        const tbody = document.getElementById('fileTableBody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:12px;">Loading...</td></tr>';
        const res = await fetch(url, { cache: 'no-store' });
        const html = await res.text();
        console.log('Files response length:', html.length);
        if (tbody) {
          tbody.innerHTML = html || '<tr><td colspan="5" class="no-data">No files found</td></tr>';
        }
      } catch (err) {
        console.error('Error loading files:', err);
        const tbody = document.getElementById('fileTableBody');
        if (tbody) {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #888;">Error loading files</td></tr>';
        }
      }
    }

    // Load student and file counts for the dashboard
    function loadStudentCount() {
      const studentCountEl = document.getElementById('studentCount');
      const fileCountEl = document.getElementById('fileCount');

      if (!studentCountEl && !fileCountEl) return;

      // Use teacher-specific endpoint and same-origin credentials
      fetch('../Adviser/get_teacher_dashboard_counts.php?teacher=1', { credentials: 'same-origin' })
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