// ===== Navigation and Sign Out handling =====
document.addEventListener('DOMContentLoaded', function() {
  // Sign out button handling
  const signoutBtn = document.getElementById('signoutBtn');
  if (signoutBtn) {
    signoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Are you sure you want to sign out?')) {
        window.location.href = '../Login/logout.php';
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
    });
  });
});