<!--sidebar-->
<?php
require_once __DIR__ . '/auth.php';
$currentPage = basename($_SERVER['PHP_SELF']);

// Group pages
$serviceProviderPages = ['pending_providers.php', 'active_providers.php'];
$routePages = ['route_planner.php', 'network_manage.php', 'manage_routes.php'];
$ratePages = ['set_tariffs.php', 'rates_management.php','rate_calculator.php'];
$sopPages = ['create_sop.php', 'view_sop.php', 'archived_sop.php'];
$schedulePages = ['schedule_routes.php', 'confirmed_timetables.php'];
?>

<div class="sidebar">
  <div class="sidebar-header">
    <div class="logo d-flex align-items-center">
      <img src="logo.png" alt="Logo" class="logo-img me-2">
    </div>      
  </div>
  
  <div class="menu">
    <ul class="nav flex-column">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" >
        <i class="fas fa-home"></i> <span> Dashboard</span>
        </a>
      </li>

      <!-- Service Provider Management -->
      <li class="nav-item">
        <a class="nav-link <?= in_array($currentPage, $serviceProviderPages) ? 'active' : '' ?>" href="#" data-bs-toggle="tooltip" >
          <i class="fas fa-handshake"></i> <span> Service Provider</span>
          <i class="fas fa-chevron-down dropdown-icon ms-auto"></i>
        </a>
        <ul class="submenu collapse ms-4 <?= in_array($currentPage, $serviceProviderPages) ? 'show' : '' ?>">

                  <li><a href="pending_providers.php" class="nav-link <?= ($currentPage == 'pending_providers.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" ><i class="fas fa-hourglass-half me-2"></i><span>Pending Service Providers</span></a></li>
          <li><a href="active_providers.php" class="nav-link <?= ($currentPage == 'active_providers.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" ><i class="fas fa-list-ul me-2"></i><span>List of Service Providers</span></a></li>
        </ul>
      </li>

      <!-- Service Network & Route Planner -->
      <li class="nav-item">
        <a class="nav-link <?= in_array($currentPage, $routePages) ? 'active' : '' ?>" href="#" data-bs-toggle="tooltip" >
          <i class="fas fa-route"></i> <span>Service Network & Route Planner</span>
          <i class="fas fa-chevron-down dropdown-icon ms-auto"></i>
        </a>
        <ul class="submenu collapse ms-4 <?= in_array($currentPage, $routePages) ? 'show' : '' ?>">
          <li><a href="route_planner.php" class="nav-link <?= ($currentPage == 'route_planner.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" ><i class="fas fa-map me-2"></i><span>Create Route</span></a></li>
          <li><a href="network_manage.php" class="nav-link <?= ($currentPage == 'network_manage.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-network-wired me-2"></i><span>Network Points</span></a></li>
          <li><a href="manage_routes.php" class="nav-link <?= ($currentPage == 'manage_routes.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" ><i class="fas fa-route me-2"></i><span>Manage Routes</span></a></li>
        </ul>
      </li>

      <!-- Rate & Tariff Management -->
      <li class="nav-item">
        <a class="nav-link <?= in_array($currentPage, $ratePages) ? 'active' : '' ?>" href="#" data-bs-toggle="tooltip">
          <i class="fas fa-coins"></i> <span>Rate & Tariff</span>
          <i class="fas fa-chevron-down dropdown-icon ms-auto"></i>
        </a>
        <ul class="submenu collapse ms-4 <?= in_array($currentPage, $ratePages) ? 'show' : '' ?>">
          <li><a href="set_tariffs.php" class="nav-link <?= ($currentPage == 'set_tariffs.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-money-bill-wave me-2"></i><span>Set Provider Rates</span></a></li>
          <li><a href="rates_management.php" class="nav-link <?= ($currentPage == 'rates_management.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-chart-bar me-2"></i><span>Rates Management</span></a></li>
          <li><a href="rate_calculator.php" class="nav-link <?= ($currentPage == 'rate_calculator.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-chart-bar me-2"></i><span>Rates Calculator</span></a></li>
        </ul>
      </li>

      <!-- Schedules & Transit Timetable -->
      <li class="nav-item">
        <a class="nav-link <?= in_array($currentPage, $schedulePages) ? 'active' : '' ?>" href="#" data-bs-toggle="tooltip">
          <i class="fas fa-calendar-alt"></i> <span>Schedules & Transit Timetable</span>
          <i class="fas fa-chevron-down dropdown-icon ms-auto"></i>
        </a>
        <ul class="submenu collapse ms-4 <?= in_array($currentPage, $schedulePages) ? 'show' : '' ?>">
          <li><a href="schedule_routes.php" class="nav-link <?= ($currentPage == 'schedule_routes.php') ? 'active' : '' ?>" data-bs-toggle="tooltip" ><i class="fas fa-clock me-2"></i><span>Schedule Routes</span></a></li>
          <li><a href="confirmed_timetables.php" class="nav-link <?= ($currentPage == 'confirmed_timetables.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-check-circle me-2"></i><span>Confirmed Schedules</span></a></li>
        </ul>
      </li>

      <!-- SOP Manager -->
      <li class="nav-item">
        <a class="nav-link <?= in_array($currentPage, $sopPages) ? 'active' : '' ?>" href="#" data-bs-toggle="tooltip">
          <i class="fas fa-book-open"></i> <span>SOP Manager</span>
          <i class="fas fa-chevron-down dropdown-icon ms-auto"></i>
        </a>
        <ul class="submenu collapse ms-4 <?= in_array($currentPage, $sopPages) ? 'show' : '' ?>">
          <li><a href="create_sop.php" class="nav-link <?= ($currentPage == 'create_sop.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-plus-square me-2"></i><span>Create SOP</span></a></li>
          <li><a href="view_sop.php" class="nav-link <?= ($currentPage == 'view_sop.php') ? 'active' : '' ?>" data-bs-toggle="tooltip"><i class="fas fa-clipboard-list me-2"></i><span>View SOPs</span></a></li>
        </ul>
      </li>
    </ul>
  </div>

  <div class="admin-info mt-auto text-center p-3">
    <span class="admin-text text-white px-3 py-2 rounded">ADMIN</span>
  </div>
</div>
