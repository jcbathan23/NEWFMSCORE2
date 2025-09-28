<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
?>

<link rel="stylesheet" href="modern-table-styles.css">

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

<div class="content p-4">
    <!-- Header Section -->
    <div class="modern-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Pending Service Providers</h3>
            <p>Review and manage pending service provider registrations</p>
        </div>
    </div>

    <!-- Success/Failure Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'rejected_provider'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Service Provider Rejected!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'approved_provider'): ?>
            <div class="alert alert-success alert-dismissible fade show auto-fade" role="alert">
                Service Provider Approved!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Modern Pending Providers Table -->
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-building me-2"></i>Company Name</th>
                        <th><i class="fas fa-user me-2"></i>Contact Person</th>
                        <th><i class="fas fa-envelope me-2"></i>Email</th>
                        <th><i class="fas fa-phone me-2"></i>Phone</th>
                        <th><i class="fas fa-map-marker-alt me-2"></i>Address</th>
                        <th><i class="fas fa-cogs me-2"></i>Services</th>
                        <th><i class="fas fa-calendar me-2"></i>Created</th>
                        <th class="text-center"><i class="fas fa-tools me-2"></i>Actions</th>
                    </tr>
                </thead>
            <tbody>
            <?php
            $query = "SELECT * FROM pending_service_provider";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr class="modern-table-row">
                    <td><span class="fw-medium"><?= $row['registration_id'] ?></span></td>
                    <td>
                        <div class="company-name">
                            <i class="fas fa-building me-2 text-muted"></i>
                            <span class="fw-medium"><?= htmlspecialchars($row['company_name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="contact-info">
                            <i class="fas fa-user-circle me-2 text-muted"></i>
                            <span><?= htmlspecialchars($row['contact_person']) ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="user-email">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <span class="fw-medium"><?= htmlspecialchars($row['email'] ?? 'N/A') ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($row['contact_number']) ?></td>
                    <td class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['address']) ?>">
                        <?= htmlspecialchars($row['address']) ?>
                    </td>
                    <td>
                        <span class="modern-badge badge-pending">
                            <i class="fas fa-cogs me-1"></i>
                            <?= htmlspecialchars($row['services']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($row['date_submitted'] ?? 'now')) ?></td>
                    <td class="text-center">
                        <div class="action-buttons">
                            <!-- View -->
                            <button class="btn btn-modern-view viewBtn" data-id="<?= $row['registration_id'] ?>" data-bs-toggle="modal" data-bs-target="#viewProviderModal" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Approve -->
                            <button class="btn btn-modern-success approve-btn" 
                                    data-id="<?= $row['registration_id'] ?>" 
                                    data-company="<?= htmlspecialchars($row['company_name']) ?>" 
                                    title="Approve Provider">
                                <i class="fas fa-check-circle"></i>
                            </button>

                            <!-- Reject -->
                            <button class="btn btn-modern-delete reject-btn" 
                                    data-id="<?= $row['registration_id'] ?>" 
                                    data-company="<?= htmlspecialchars($row['company_name']) ?>" 
                                    title="Reject Provider">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>


                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h5>No Pending Providers</h5>
                            <p>There are no pending service provider registrations at this time.</p>
                        </div>
                    </td>
                </tr>
            <?php
            endif;
            $conn->close();
            ?>
            </tbody>
            </table>
        </div>
    </div>

    <!-- Logistic1 Service Providers Section -->
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">LOGISTIC1 SERVICE PROVIDERS</h3>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary" id="refreshLogistic1Btn">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
                <button type="button" class="btn btn-info" id="searchLogistic1Btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>

        <!-- Loading and Status Messages -->
        <div id="logistic1Status" class="mb-3" style="display: none;">
            <!-- Status messages will be displayed here -->
        </div>

        <!-- Statistics Cards -->
        <div id="logistic1Stats" class="row mb-4" style="display: none;">
            <!-- Statistics cards will be displayed here -->
        </div>

        <!-- Logistic1 Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="logistic1Table">
                <thead class="table-info">
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Hub Location</th>
                        <th>Service Areas</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="logistic1TableBody">
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            <div class="py-4">
                                <i class="fas fa-cloud-download-alt fa-2x mb-2"></i><br>
                                Click "Refresh Data" to load service providers from Logistic1
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search Logistic1 Providers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="searchQuery" class="form-label">Search Query</label>
                        <input type="text" class="form-control" id="searchQuery" placeholder="Enter company name, location, service type, etc.">
                        <div class="form-text">Search across company names, locations, services, and contact information</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="performSearchBtn">Search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logistic1 Provider Details Modal -->
    <div class="modal fade" id="logistic1ProviderModal" tabindex="-1" aria-labelledby="logistic1ProviderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="logistic1ModalContent">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
            <div class="modal-content">
                <form id="approveForm" action="approve_provider.php" method="POST">
                    <input type="hidden" name="registration_id" id="approveProviderId">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="approveModalLabel">Approve Provider</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to approve <strong id="approveCompanyName"></strong>?
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm" id="approveSubmitBtn">
                            <i class="fas fa-check-circle me-1"></i>Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form id="rejectForm" action="reject_provider.php" method="POST">
                    <input type="hidden" name="registration_id" id="rejectProviderId">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">Confirm Rejection</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to reject <strong id="rejectCompanyName"></strong>?
                        <div class="mt-2">
                            <small class="text-muted">This action cannot be undone.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm" id="rejectSubmitBtn">
                            <i class="fas fa-times-circle me-1"></i>Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewProviderModal" tabindex="-1" aria-labelledby="viewProviderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="modalContent">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Simple view button functionality for local providers
    const buttons = document.querySelectorAll(".viewBtn");
    buttons.forEach(btn => {
        btn.addEventListener("click", function () {
            const providerId = this.getAttribute("data-id");
            fetch("get_provider_modal.php?id=" + providerId)
                .then(response => response.text())
                .then(html => { 
                    document.getElementById("modalContent").innerHTML = html; 
                })
                .catch(err => { 
                    document.getElementById("modalContent").innerHTML = "<div class='modal-body'><div class='alert alert-danger'>Failed to load details.</div></div>"; 
                });
        });
    });

    // Approve button functionality
    const approveButtons = document.querySelectorAll(".approve-btn");
    approveButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const providerId = this.getAttribute("data-id");
            const companyName = this.getAttribute("data-company");
            
            // Set the modal data
            document.getElementById("approveProviderId").value = providerId;
            document.getElementById("approveCompanyName").textContent = companyName;
            
            // Show the modal
            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            approveModal.show();
        });
    });

    // Reject button functionality
    const rejectButtons = document.querySelectorAll(".reject-btn");
    rejectButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const providerId = this.getAttribute("data-id");
            const companyName = this.getAttribute("data-company");
            
            // Set the modal data
            document.getElementById("rejectProviderId").value = providerId;
            document.getElementById("rejectCompanyName").textContent = companyName;
            
            // Show the modal
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            rejectModal.show();
        });
    });

    // Form submission handling with loading states
    document.getElementById('approveForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('approveSubmitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Approving...';
        submitBtn.disabled = true;
        
        // Allow form to submit normally
        // The loading state will be visible until page redirects
    });

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('rejectSubmitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Rejecting...';
        submitBtn.disabled = true;
        
        // Allow form to submit normally
        // The loading state will be visible until page redirects
    });

    // Reset modal states when they're hidden
    document.getElementById('approveModal').addEventListener('hidden.bs.modal', function () {
        const submitBtn = document.getElementById('approveSubmitBtn');
        submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i>Approve';
        submitBtn.disabled = false;
    });

    document.getElementById('rejectModal').addEventListener('hidden.bs.modal', function () {
        const submitBtn = document.getElementById('rejectSubmitBtn');
        submitBtn.innerHTML = '<i class="fas fa-times-circle me-1"></i>Reject';
        submitBtn.disabled = false;
    });

    // Logistic1 API Integration
    const logistic1API = {
        baseUrl: '../api/core2_pull_service_providers.php',
        
        showStatus: function(message, type = 'info') {
            const statusDiv = document.getElementById('logistic1Status');
            statusDiv.style.display = 'block';
            statusDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        },
        
        showLoading: function(message = 'Loading data from Logistic1...') {
            this.showStatus(`<i class="fas fa-spinner fa-spin"></i> ${message}`, 'info');
        },
        
        hideStatus: function() {
            const statusDiv = document.getElementById('logistic1Status');
            statusDiv.style.display = 'none';
        },
        
        loadProviders: function() {
            this.showLoading('Fetching service providers from Logistic1...');
            
            fetch(this.baseUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.displayProviders(data.data);
                        this.loadStats();
                        this.showStatus(`Successfully loaded ${data.count} service providers from Logistic1`, 'success');
                        setTimeout(() => this.hideStatus(), 3000);
                    } else {
                        this.showStatus(`Error: ${data.message || data.error}`, 'danger');
                        this.displayError(data.message || 'Failed to load providers');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showStatus('Network error: Unable to connect to pull script', 'danger');
                    this.displayError('Network connection failed');
                });
        },
        
        loadStats: function() {
            fetch(this.baseUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Calculate stats from the data
                        const providers = data.data;
                        const stats = {
                            total_service_providers: providers.length,
                            service_providers_only: providers.filter(p => p.type === 'service_provider').length,
                            both_types: providers.filter(p => p.type === 'both').length,
                            approved_count: providers.filter(p => p.status === 'active' || p.status === '1').length
                        };
                        this.displayStats(stats);
                    }
                })
                .catch(error => {
                    console.error('Stats error:', error);
                });
        },
        
        displayStats: function(stats) {
            const statsDiv = document.getElementById('logistic1Stats');
            statsDiv.style.display = 'block';
            statsDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">${stats.total_service_providers || 0}</h4>
                                        <small>Total Providers</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">${stats.service_providers_only || 0}</h4>
                                        <small>Service Only</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-truck fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">${stats.both_types || 0}</h4>
                                        <small>Both Types</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">${stats.approved_count || 0}</h4>
                                        <small>Approved</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        displayProviders: function(providers) {
            const tbody = document.getElementById('logistic1TableBody');
            
            if (!providers || providers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No service providers found in Logistic1</td></tr>';
                return;
            }
            
            tbody.innerHTML = providers.map(provider => `
                <tr>
                    <td>${provider.id}</td>
                    <td><strong class="text-primary">${this.escapeHtml(provider.name || provider.supplier_name || 'N/A')}</strong></td>
                    <td>${this.escapeHtml(provider.contact || provider.contact_person || 'N/A')}</td>
                    <td>${this.escapeHtml(provider.email || 'N/A')}</td>
                    <td>${this.escapeHtml(provider.phone || 'N/A')}</td>
                    <td>
                        <span class="badge ${(provider.type || provider.supplier_type) === 'service_provider' ? 'bg-success' : 'bg-secondary'}">
                            ${this.escapeHtml(provider.type || provider.supplier_type || 'N/A')}
                        </span>
                    </td>
                    <td>${this.escapeHtml(provider.hub_location || 'N/A')}</td>
                    <td>${this.escapeHtml(provider.service_areas || 'N/A')}</td>
                    <td><small class="text-muted">${this.formatDate(provider.created_at)}</small></td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm me-1 logistic1ViewBtn" data-id="${provider.id}" data-bs-toggle="modal" data-bs-target="#logistic1ProviderModal">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-success btn-sm logistic1ImportBtn" data-id="${provider.id}" title="Import to Local Database">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Add event listeners for the new buttons
            this.attachEventListeners();
        },
        
        displayError: function(message) {
            const tbody = document.getElementById('logistic1TableBody');
            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                ${message}<br>
                <button class="btn btn-outline-primary btn-sm mt-2" onclick="logistic1API.loadProviders()">Try Again</button>
            </td></tr>`;
        },
        
        attachEventListeners: function() {
            // View buttons
            document.querySelectorAll('.logistic1ViewBtn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const providerId = e.currentTarget.getAttribute('data-id');
                    this.viewProvider(providerId);
                });
            });
            
            // Import buttons
            document.querySelectorAll('.logistic1ImportBtn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const providerId = e.currentTarget.getAttribute('data-id');
                    this.importProvider(providerId, e.currentTarget);
                });
            });
        },
        
        viewProvider: function(providerId) {
            this.showLoading('Loading provider details...');
            
            fetch(this.baseUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Find the provider by ID
                        const provider = data.data.find(p => p.id == providerId);
                        if (provider) {
                            this.displayProviderModal(provider);
                            this.hideStatus();
                        } else {
                            this.showStatus(`Provider with ID ${providerId} not found`, 'danger');
                        }
                    } else {
                        this.showStatus(`Error: ${data.message || data.error}`, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showStatus('Failed to load provider details', 'danger');
                });
        },
        
        displayProviderModal: function(provider) {
            const modalContent = document.getElementById('logistic1ModalContent');
            modalContent.innerHTML = `
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i>${this.escapeHtml(provider.name || provider.supplier_name || 'Unknown Provider')}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-info-circle"></i> Basic Information</h6>
                            <table class="table table-sm">
                                <tr><th>ID:</th><td>${provider.id}</td></tr>
                                <tr><th>Company Name:</th><td>${this.escapeHtml(provider.name || provider.supplier_name || 'N/A')}</td></tr>
                                <tr><th>Contact Person:</th><td>${this.escapeHtml(provider.contact || provider.contact_person || 'N/A')}</td></tr>
                                <tr><th>Email:</th><td>${this.escapeHtml(provider.email || 'N/A')}</td></tr>
                                <tr><th>Phone:</th><td>${this.escapeHtml(provider.phone || 'N/A')}</td></tr>
                                <tr><th>Type:</th><td><span class="badge ${(provider.type || provider.supplier_type) === 'service_provider' ? 'bg-success' : 'bg-secondary'}">${this.escapeHtml(provider.type || provider.supplier_type || 'N/A')}</span></td></tr>
                                <tr><th>Status:</th><td><span class="badge ${(provider.status === 'active' || provider.status === '1') ? 'bg-success' : 'bg-warning'}">${this.escapeHtml(provider.status || 'Unknown')}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-map-marker-alt"></i> Location & Services</h6>
                            <table class="table table-sm">
                                <tr><th>Hub Location:</th><td>${this.escapeHtml(provider.hub_location || 'N/A')}</td></tr>
                                <tr><th>Service Areas:</th><td>${this.escapeHtml(provider.service_areas || 'N/A')}</td></tr>
                                <tr><th>Facility Type:</th><td>${this.escapeHtml(provider.facility_type || 'N/A')}</td></tr>
                                <tr><th>Service Capabilities:</th><td>${this.escapeHtml(provider.service_capabilities || 'N/A')}</td></tr>
                                <tr><th>Created:</th><td>${this.formatDate(provider.created_at)}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${provider._raw ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-code"></i> Raw Data (Debug)</h6>
                            <details>
                                <summary class="btn btn-sm btn-outline-secondary">Show Raw API Data</summary>
                                <pre class="mt-2 bg-light p-2 rounded" style="font-size: 12px; max-height: 200px; overflow-y: auto;">${JSON.stringify(provider._raw, null, 2)}</pre>
                            </details>
                        </div>
                    </div>
                    ` : ''}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="logistic1API.importProvider(${provider.id}, this)">
                        <i class="fas fa-download"></i> Import to Local Database
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            `;
        },
        
        importProvider: function(providerId, buttonElement) {
            // Find the provider data first
            fetch(this.baseUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const provider = data.data.find(p => p.id == providerId);
                        if (provider) {
                            this.performImport(provider, buttonElement);
                        } else {
                            this.showStatus(`Provider with ID ${providerId} not found`, 'danger');
                        }
                    } else {
                        this.showStatus(`Error loading provider data: ${data.message || data.error}`, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showStatus('Failed to load provider data for import', 'danger');
                });
        },
        
        performImport: function(provider, buttonElement) {
            if (buttonElement) {
                const originalText = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
                buttonElement.disabled = true;
            }
            
            this.showStatus('Importing provider to local database...', 'info');
            
            fetch('../api/import_provider.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    provider: provider
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showStatus(`Successfully imported "${data.company_name}" to pending providers!`, 'success');
                    
                    if (buttonElement) {
                        buttonElement.innerHTML = '<i class="fas fa-check"></i> Imported';
                        buttonElement.classList.remove('btn-success');
                        buttonElement.classList.add('btn-secondary');
                        buttonElement.disabled = true;
                    }
                    
                    // Refresh the main pending providers table
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    this.showStatus(`Import failed: ${data.message || data.error}`, 'danger');
                    
                    if (buttonElement) {
                        buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed';
                        buttonElement.classList.remove('btn-success');
                        buttonElement.classList.add('btn-danger');
                        buttonElement.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Import error:', error);
                this.showStatus('Network error during import', 'danger');
                
                if (buttonElement) {
                    buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                    buttonElement.classList.remove('btn-success');
                    buttonElement.classList.add('btn-danger');
                    buttonElement.disabled = false;
                }
            });
        },
        
        searchProviders: function(query) {
            this.showLoading(`Searching for "${query}"...`);
            
            fetch(this.baseUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Filter the data locally
                        const searchTerm = query.toLowerCase();
                        const matchingProviders = data.data.filter(provider => {
                            const searchFields = [
                                provider.name || '',
                                provider.contact || '',
                                provider.email || '',
                                provider.phone || '',
                                provider.hub_location || '',
                                provider.service_areas || '',
                                provider.service_capabilities || '',
                                provider.facility_type || '',
                                provider.type || ''
                            ];
                            
                            return searchFields.some(field => 
                                field.toLowerCase().includes(searchTerm)
                            );
                        });
                        
                        this.displayProviders(matchingProviders);
                        this.showStatus(`Found ${matchingProviders.length} providers matching "${query}"`, 'success');
                        setTimeout(() => this.hideStatus(), 3000);
                    } else {
                        this.showStatus(`Search error: ${data.message || data.error}`, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showStatus('Search failed: Network error', 'danger');
                });
        },
        
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        formatDate: function(dateString) {
            if (!dateString) return 'N/A';
            try {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }
    };
    
    // Event listeners for Logistic1 functionality
    document.getElementById('refreshLogistic1Btn').addEventListener('click', () => {
        logistic1API.loadProviders();
    });
    
    document.getElementById('performSearchBtn').addEventListener('click', () => {
        const query = document.getElementById('searchQuery').value.trim();
        if (query) {
            logistic1API.searchProviders(query);
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
            modal.hide();
        } else {
            alert('Please enter a search query');
        }
    });
    
    // Allow search on Enter key
    document.getElementById('searchQuery').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            document.getElementById('performSearchBtn').click();
        }
    });
    
    // Make logistic1API globally accessible for onclick handlers
    window.logistic1API = logistic1API;
});
</script>

<style>
.table-hover tbody tr:hover {
    background-color: #f1f3f5;
    transition: background-color 0.2s;
}
.btn-sm {
    border-radius: 6px;
}
.badge {
    padding: 0.45em 0.75em;
    font-size: 0.85rem;
}

/* Modal improvements */
.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}

.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
}

.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 12px 12px;
}

/* Button loading state */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Prevent modal conflicts */
#approveModal .modal-dialog,
#rejectModal .modal-dialog {
    margin: 1.75rem auto;
}
</style>

<?php include('footer.php'); ?>
