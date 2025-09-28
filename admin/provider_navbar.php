    <nav class="navbar px-3">
    <span class="navbar-text">
        Welcome, <strong><?php echo htmlspecialchars($company_name); ?></strong>
    </span>
    <?php if ($account_status == "pending"): ?>
        <span class="pending-alert">Your registration is still pending for approval</span>
    <?php elseif ($account_status == "active"): ?>
        <span class="active-alert">Active Service Provider</span>
    <?php endif; ?>
</nav>
