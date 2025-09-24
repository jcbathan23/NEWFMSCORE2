<div class="sidebar d-flex flex-column vh-100 p-3 bg-dark text-white" style="width: 220px;">
    <!-- Logo / Title -->
    <div class="text-center py-2">
        <img src="logo.png" alt="Logo" class="img-fluid" style="max-height: 90px;">
    </div>
    <div class="section-divider my-2"><span class="section-label">CORE II</span></div>

    <!-- Menu Links -->
    <nav class="nav flex-column">
        <a href="provider_dashboard.php" class="nav-link text-white d-flex align-items-center mb-2 rounded p-2 hover-bg-light">
            <i class="fas fa-home me-2"></i> Dashboard
        </a>

        <?php if ($account_status == 'active'): ?>
        <a href="provider_rates.php" class="nav-link text-white d-flex align-items-center mb-2 rounded p-2 hover-bg-light">
            <i class="fas fa-tags me-2"></i> Rates
        </a>
        <a href="provider_schedules.php" class="nav-link text-white d-flex align-items-center mb-2 rounded p-2 hover-bg-light">
            <i class="fas fa-calendar-alt me-2"></i> My Schedules
        </a>
        <?php endif; ?>

        <a href="provider_profile.php" class="nav-link text-white d-flex align-items-center mb-2 rounded p-2 hover-bg-light">
            <i class="fas fa-user me-2"></i> My Profile
        </a>
    </nav>

    <!-- Spacer to push logout to bottom -->
    <div class="mt-auto">
        <a href="../admin/loginpage.php" class="nav-link text-white bg-danger d-flex align-items-center justify-content-center rounded p-2">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</div>

<!-- Custom Hover Style -->
<style>
    .hover-bg-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transition: 0.2s;
    }
    .sidebar .nav-link {
        font-size: 0.95rem;
    }
</style>
