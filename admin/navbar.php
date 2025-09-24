<?php
require '../connect.php';
require 'functions.php';
require_once __DIR__ . '/auth.php';
?>
<!--navbar-->
<?php
// Fetch latest 5 notifications with timestamp difference
$notifQuery = mysqli_query($conn, "
    SELECT *, TIMESTAMPDIFF(MINUTE, created_at, NOW()) AS minutes_ago 
    FROM notifications 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Count unread notifications
$notifCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications WHERE is_read = 0");
$notifCount = mysqli_fetch_assoc($notifCountQuery)['total'];
?>

<div class="navbar bg-white shadow-sm px-3 d-flex align-items-center">
  <!-- Left Side -->
  <div class="navbar-left d-flex align-items-center me-2">
    <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-3">☰</button>
    <div id="datetime" class="datetime fw-semibold text-muted d-none d-md-block"></div>
  </div>

  <!-- Center Title -->
  <div class="flex-grow-1 text-center">
    <h5 class="m-0 fw-bold">Admin Dashboard <span class="text-primary">| CORE II</span></h5>
  </div>

  <!-- Right Side -->
  <div class="nav-links d-flex align-items-center ms-2">
    <!-- Notifications Dropdown -->
    <div class="dropdown me-3">
      <a href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" 
         class="text-success position-relative">
        <i class="fas fa-bell fs-5"></i>
        <?php if($notifCount > 0): ?>
          <span id="notifCount" 
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $notifCount ?>
          </span>
        <?php endif; ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" 
          aria-labelledby="notificationsDropdown" 
          data-bs-popper="static" 
          style="min-width: 320px; max-height: 300px; overflow-y: auto;">

        <!-- Header -->
        <li class="d-flex justify-content-between align-items-center mb-2 px-2">
          <strong>Notifications</strong>
          <button id="clearNotifs" class="btn btn-sm btn-link text-danger p-0">Clear All</button>
        </li>
        <li><hr class="dropdown-divider"></li>

        <!-- Notifications List -->
        <?php if(mysqli_num_rows($notifQuery) > 0): ?>
          <?php while($notif = mysqli_fetch_assoc($notifQuery)): ?>
            <?php
              $minutes = $notif['minutes_ago'];
              if($minutes < 1) {
                  $timeText = 'Just now';
              } elseif($minutes < 60) {
                  $timeText = $minutes.'m ago';
              } elseif($minutes < 1440) {
                  $timeText = floor($minutes/60).'h ago';
              } else {
                  $timeText = floor($minutes/1440).'d ago';
              }

              $unreadClass = $notif['is_read'] == 0 ? 'notif-unread' : '';
            ?>
            <li class="notif-item mb-1 <?= $unreadClass ?>">
              <a href="<?= $notif['link'] ?? '#' ?>" 
                 class="d-flex justify-content-between align-items-center text-decoration-none">
                <span>
                  <?= htmlspecialchars($notif['message']) ?>
                  <?php if($notif['is_read'] == 0): ?>
                    <span class="badge bg-success ms-1">New</span>
                  <?php endif; ?>
                </span>
                <small class="text-muted ms-2"><?= $timeText ?></small>
              </a>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li id="noNotif" class="text-center text-muted py-2">No notifications</li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Dark Mode Button -->
    <a href="#" id="darkModeToggle" class="text-success me-3" data-bs-toggle="tooltip" 
       data-bs-placement="bottom" title="Toggle Dark Mode">
      <i class="fas fa-moon fs-5"></i>
    </a>

    <!-- User Dropdown -->
    <div class="dropdown">
      <a href="#" class="text-success" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user fs-5"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<script>
  // Clear notifications
  const clearBtn = document.getElementById('clearNotifs');
  if(clearBtn){
    clearBtn.addEventListener('click', () => {
      fetch('clear_notifications.php')
        .then(res => res.text())
        .then(() => {
          document.querySelectorAll('.notif-item').forEach(item => item.remove());
          document.getElementById('noNotif').classList.remove('d-none');
          const notifCount = document.getElementById('notifCount');
          if(notifCount) notifCount.style.display = 'none';
        });
    });
  }

  // Dark mode toggle in navbar - delegates to global setDarkMode()
  const dmBtn = document.getElementById('darkModeToggle');
  if (dmBtn) {
    dmBtn.addEventListener('click', function(e){
      e.preventDefault();
      const turnOn = !document.body.classList.contains('dark-mode');
      if (typeof window.setDarkMode === 'function') {
        window.setDarkMode(turnOn);
      } else {
        // fallback
        document.body.classList.toggle('dark-mode');
        try { localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled'); } catch(e) {}
      }
    });
  }
</script>

<style>
  .navbar h5 { letter-spacing: .3px; }
  .notif-item a { 
    padding: 8px 12px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    font-size: 0.9rem; 
    color: #000; 
    transition: background 0.2s;
    border-radius: 5px;
  }
  .notif-item a:hover { background-color: #f8f9fa; }
  .notif-unread a { background-color: #e6f7ff; }
  .notif-unread a:hover { background-color: #cceeff; }
  .notif-item small { font-size: 0.75rem; }
  .notif-item .badge { font-size: 0.65rem; }
  #notifCount { font-size: 0.7rem; padding: 2px 5px; }
  .dropdown-menu { z-index: 1055 !important; }
</style>
