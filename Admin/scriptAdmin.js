// Admin dashboard and logs page script - OPTIMIZED
let userSearchTimeout;
let logsSearchTimeout;
let lastUserQuery = '';
let lastLogsQuery = '';

function initAdmin() {
  // User search with live filtering on input (debounced - reduced to 150ms for faster response)
  const userSearchInput = document.getElementById('userSearchInput');
  if (userSearchInput) {
    userSearchInput.addEventListener('input', function(e) {
      const v = e.target.value;
      console.log('user input event:', v);
      clearTimeout(userSearchTimeout);
      userSearchTimeout = setTimeout(searchUsers, 150);
    });
  }

  // Logs search with live filtering on input (debounced - reduced to 150ms for faster response)
  const logsSearchInput = document.getElementById('logsSearchInput');
  if (logsSearchInput) {
    logsSearchInput.addEventListener('input', function(e) {
      const v = e.target.value;
      console.log('logs input event:', v);
      clearTimeout(logsSearchTimeout);
      logsSearchTimeout = setTimeout(searchLogs, 150);
    });
  }

  // Sign out button
  const signoutBtn = document.getElementById('signoutBtn');
  if (signoutBtn) {
    signoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Are you sure you want to sign out?')) {
        window.location.href = '../Login/logout.php';
      }
    });
  }

  // Create user modal handlers
  const openCreateBtn = document.getElementById('openCreateUser');
  const createModal = document.getElementById('createUserModal');
  const closeCreateBtn = document.getElementById('closeCreateUser');
  const cancelCreateBtn = document.getElementById('cancelCreateUser');
  const submitCreateBtn = document.getElementById('submitCreateUser');
  const createAlert = document.getElementById('createUserAlert');
  const createRole = document.getElementById('createRole');
  const extraFields = document.getElementById('extraFields');
  const createAdvisory = document.getElementById('createAdvisory');
  const lblAdvisory = document.getElementById('lblAdvisory');

  if (openCreateBtn && createModal) {
    openCreateBtn.addEventListener('click', () => { 
      createModal.style.display = 'block'; 
      // ensure extra fields visibility follows the selected role when modal opens
      if (createRole) toggleExtraFields(createRole.value);
    });
  }
  // toggle extra fields based on role selection
  function toggleExtraFields(role) {
    if (!extraFields) return;
    const showAdvisory = (role === 'adviser');
    extraFields.style.display = showAdvisory ? 'block' : 'none';
    if (createAdvisory) { createAdvisory.style.display = showAdvisory ? 'block' : 'none'; createAdvisory.required = showAdvisory; }
    if (lblAdvisory) lblAdvisory.style.display = showAdvisory ? 'block' : 'none';
  }
  if (createRole) {
    createRole.addEventListener('change', (e) => toggleExtraFields(e.target.value));
    // initialize state on load
    toggleExtraFields(createRole.value);
  }
  if (closeCreateBtn) closeCreateBtn.addEventListener('click', () => { createModal.style.display = 'none'; });
  if (cancelCreateBtn) cancelCreateBtn.addEventListener('click', () => { createModal.style.display = 'none'; });

  if (submitCreateBtn) {
    submitCreateBtn.addEventListener('click', async () => {
      if (!createModal) return;
      const role = document.getElementById('createRole').value;
      const name = document.getElementById('createName').value.trim();
      const email = document.getElementById('createEmail').value.trim();
      const password = document.getElementById('createPassword').value;
      const confirm = document.getElementById('createConfirm').value;

      createAlert.style.display = 'none';

      if (!name || !email || !password || !confirm) {
        createAlert.style.display = 'block'; createAlert.style.background='#ffe6e6'; createAlert.style.color='#a70000'; createAlert.textContent = 'All fields are required.'; return;
      }
      if (password.length < 6) { createAlert.style.display = 'block'; createAlert.style.background='#ffe6e6'; createAlert.style.color='#a70000'; createAlert.textContent = 'Password must be at least 6 characters.'; return; }
      if (password !== confirm) { createAlert.style.display = 'block'; createAlert.style.background='#ffe6e6'; createAlert.style.color='#a70000'; createAlert.textContent = 'Passwords do not match.'; return; }

      submitCreateBtn.disabled = true; submitCreateBtn.textContent = 'Creating...';
      try {
          const advisory = (createAdvisory && createAdvisory.value) ? createAdvisory.value.trim() : '';
          const form = new URLSearchParams({ role, name, email, password, confirm_password: confirm, advisory });
        const res = await fetch('create_user.php', { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (!data || !data.success) {
          createAlert.style.display = 'block'; createAlert.style.background='#ffe6e6'; createAlert.style.color='#a70000'; createAlert.textContent = data.error || 'Failed to create user';
        } else {
          // on success, redirect back to admin page with confirmation so page state refreshes
          window.location.href = 'adminDashboard.php?created=1';
        }
      } catch (err) {
        createAlert.style.display = 'block'; createAlert.style.background='#ffe6e6'; createAlert.style.color='#a70000'; createAlert.textContent = 'Error creating user';
      } finally {
        submitCreateBtn.disabled = false; submitCreateBtn.textContent = 'Create';
      }
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAdmin);
} else {
  // DOM already ready
  initAdmin();
}

// Search users with live input - OPTIMIZED with faster network and caching
async function searchUsers() {
  const searchInput = document.getElementById('userSearchInput');
  const query = searchInput ? searchInput.value.trim() : '';
  const userTableBody = document.getElementById('usersTableBody');
  
  if (!userTableBody) return;
  
  // Skip if query hasn't changed
  if (query === lastUserQuery) return;
  lastUserQuery = query;

  try {
    const urlBase = 'search_users.php';
    const url = urlBase + (query ? '?q=' + encodeURIComponent(query) : '') + (query ? '&' : '?') + 't=' + Date.now();
    console.log('Searching users with query:', query);
    
    // Show loading indicator
    userTableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 15px;">Loading...</td></tr>';
    
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      console.error('Search users HTTP error', res.status);
      userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">Server error</td></tr>';
      return;
    }
    const html = await res.text();
    console.log('Search response length:', html.length);
    if (!html || html.trim().length === 0) {
      userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">No users found</td></tr>';
    } else {
      userTableBody.innerHTML = html;
    }
  } catch (err) {
    console.error('Failed to search users:', err);
    userTableBody.innerHTML = '<tr><td colspan="4" class="no-data">Error searching users</td></tr>';
  }
}

// Search logs with live input - OPTIMIZED with faster network and caching
async function searchLogs() {
  const searchInput = document.getElementById('logsSearchInput');
  const query = searchInput ? searchInput.value.trim() : '';
  const logsTableBody = document.getElementById('logsTableBody');
  
  if (!logsTableBody) return;
  
  // Skip if query hasn't changed
  if (query === lastLogsQuery) return;
  lastLogsQuery = query;

  try {
    const urlBase = 'search_logs.php';
    const url = urlBase + (query ? '?q=' + encodeURIComponent(query) : '') + (query ? '&' : '?') + 't=' + Date.now();
    console.log('Searching logs with query:', query);
    
    // Show loading indicator
    logsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 15px;">Loading...</td></tr>';
    
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      console.error('Search logs HTTP error', res.status);
      logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">Server error</td></tr>';
      return;
    }
    const html = await res.text();
    console.log('Search response length:', html.length);
    if (!html || html.trim().length === 0) {
      logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">No logs found</td></tr>';
    } else {
      logsTableBody.innerHTML = html;
    }
  } catch (err) {
    console.error('Failed to search logs:', err);
    logsTableBody.innerHTML = '<tr><td colspan="6" class="no-data">Error searching logs</td></tr>';
  }
}




