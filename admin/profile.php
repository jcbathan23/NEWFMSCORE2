<?php 
include('header.php'); 
include('sidebar.php'); 
include('navbar.php'); 
include('../connect.php'); 
?>

<style>
.card-dashboard {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    padding: 1.2rem;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
    height: 130px;
    cursor: pointer;
}
.card-dashboard:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}
.card-dashboard h5 { font-weight: 600; color: #555; margin-bottom: .5rem; font-size: 0.9rem; }
.card-dashboard h2 { font-weight: bold; font-size: 1.6rem; color: #333; margin: 0; }

.content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
}

.auto-fade {
    animation: fadeOut 5s ease-in-out forwards;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; display: none; }
}

/* Modern Table Styles */
.modern-table-container {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(43, 63, 78, 0.08);
    overflow: hidden;
    border: 1px solid rgba(43, 63, 78, 0.06);
}

.modern-table {
    margin-bottom: 0;
    border: none;
}

.modern-table thead {
    background: linear-gradient(135deg, #2b3f4e 0%, #1f3442 100%);
    color: #fff;
}

.modern-table thead th {
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.modern-table tbody tr {
    border-bottom: 1px solid rgba(43, 63, 78, 0.05);
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    background-color: rgba(43, 63, 78, 0.02);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(43, 63, 78, 0.08);
}

.modern-table tbody td {
    padding: 1.25rem;
    vertical-align: middle;
    border: none;
    font-size: 0.9rem;
}

.user-email {
    display: flex;
    align-items: center;
}

.user-email .fw-medium {
    color: #2b3f4e;
    font-weight: 500;
}

.password-display {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.85rem;
    border: 1px solid #e9ecef;
}

.modern-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
}

.badge-admin {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.badge-user {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.badge-service-provider {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
}

.btn-modern-primary {
    background: lightgrey;
    border: none;
    color: black;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(43, 63, 78, 0.2);
}

.btn-modern-primary:hover {
    background: linear-gradient(135deg, #1f3442, #2b3f4e);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 63, 78, 0.3);
    color: white;
}

.btn-modern-edit {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border: none;
    color: #212529;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    margin-right: 0.25rem;
}

.btn-modern-edit:hover {
    background: linear-gradient(135deg, #e0a800, #d39e00);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    color: #212529;
}

.btn-modern-delete {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.btn-modern-delete:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    color: white;
}

.table-responsive {
    border-radius: 16px;
    max-height: 600px;
    overflow-y: auto;
}

.table-responsive::-webkit-scrollbar {
    width: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #2b3f4e;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #1f3442;
}

</style>



<div class="content p-4">
    <h3 class="mb-4">USER MANAGEMENT</h3>

    <?php
    // Count totals
    $totalUsers = $conn->query("SELECT COUNT(*) as cnt FROM newaccounts")->fetch_assoc()['cnt'];
    $totalAdmins = $conn->query("SELECT COUNT(*) as cnt FROM admin_list")->fetch_assoc()['cnt'];
    $totalProviders = $conn->query("SELECT COUNT(*) as cnt FROM pending_service_provider")->fetch_assoc()['cnt']
                    + $conn->query("SELECT COUNT(*) as cnt FROM active_service_provider")->fetch_assoc()['cnt'];
    ?>

    <!-- Dashboard Cards -->
    <div class="row g-3 mb-4">
        <?php
        $userCards = [
            ['label'=>'Total Users','value'=>$totalUsers,'color'=>'#6f42c1','type'=>'User'],
            ['label'=>'Total Admins','value'=>$totalAdmins,'color'=>'#007bff','type'=>'Admin'],
            ['label'=>'Total Service Providers','value'=>$totalProviders,'color'=>'#28a745','type'=>'Service Provider'],
            ['label'=>'Show All','value'=>($totalUsers + $totalAdmins + $totalProviders),'color'=>'#fd7e14','type'=>'All'],
        ];

        foreach($userCards as $card):
        ?>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="card-dashboard" data-type="<?= $card['type'] ?>" style="border-top: 4px solid <?= $card['color'] ?>;">
                <h5><?= $card['label'] ?></h5>
                <h2><?= $card['value'] ?></h2>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show auto-fade" role="alert">
                User Details Updated Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'deleted'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                User Deleted Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'added'): ?>
            <div class="alert alert-success alert-dismissible fade show auto-fade" role="alert">
                User Added Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php elseif (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'update_failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Failed to update user. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'delete_failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Failed to delete user. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'user_not_found'): ?>
            <div class="alert alert-warning alert-dismissible fade show auto-fade" role="alert">
                User not found in the system.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'missing_fields'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Please fill in all required fields.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'missing_sp_fields'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Company Name and Contact Person are required for Service Providers.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'invalid_email'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Please enter a valid email address.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'email_exists'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                This email address is already registered in the system.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'add_failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Failed to add user. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-modern-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-2"></i>Add New User
        </button>
    </div>

    <!-- Modern Users Table -->
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-envelope me-2"></i>Email Address</th>
                        <th><i class="fas fa-key me-2"></i>Password</th>
                        <th><i class="fas fa-user-tag me-2"></i>Account Type</th>
                        <th class="text-center"><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php
                $tables = [
                    'newaccounts' => 'User',
                    'admin_list' => 'Admin',
                    'pending_service_provider' => 'Service Provider',
                    'active_service_provider' => 'Service Provider'
                ];

                $accounts = [];

                foreach ($tables as $table => $type) {
                    if($table === 'active_service_provider' || $table === 'pending_service_provider') {
                        $sql = "SELECT * FROM $table";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if (!isset($accounts[$row['email']])) {
                                    $accounts[$row['email']] = [
                                        'password' => $row['password'] ?? '',
                                        'account_type' => $type,
                                        'company_name' => $row['company_name'] ?? '',
                                        'contact_person' => $row['contact_person'] ?? '',
                                        'contact_number' => $row['contact_number'] ?? '',
                                        'services' => $row['services'] ?? '',
                                        'status' => $row['status'] ?? 'Active'
                                    ];
                                }
                            }
                        }
                    } else {
                        $sql = "SELECT email, password FROM $table";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $accounts[$row['email']] = [
                                    'password' => $row['password'],
                                    'account_type' => $type
                                ];
                            }
                        }
                    }
                }

                foreach ($accounts as $email => $data):
                ?>
                    <tr data-account="<?= htmlspecialchars($data['account_type']) ?>" class="modern-table-row">
                        <td>
                            <div class="user-email">
                                <i class="fas fa-user-circle me-2 text-muted"></i>
                                <span class="fw-medium"><?= htmlspecialchars($email) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="password-field">
                                <code class="password-display"><?= htmlspecialchars($data['password']) ?></code>
                            </div>
                        </td>
                        <td>
                            <span class="modern-badge badge-<?= strtolower(str_replace(' ', '-', $data['account_type'])) ?>">
                                <i class="fas fa-<?= $data['account_type']==='Admin' ? 'shield-alt' : ($data['account_type']==='User' ? 'user' : 'building') ?> me-1"></i>
                                <?= htmlspecialchars($data['account_type']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <!-- Update Button -->
                                <button class="btn btn-modern-edit updateBtn"
                                        data-email="<?= htmlspecialchars($email) ?>"
                                        data-password="<?= htmlspecialchars($data['password']) ?>"
                                        data-account="<?= htmlspecialchars($data['account_type']) ?>"
                                        data-company="<?= htmlspecialchars($data['company_name'] ?? '') ?>"
                                        data-contact_person="<?= htmlspecialchars($data['contact_person'] ?? '') ?>"
                                        data-contact_number="<?= htmlspecialchars($data['contact_number'] ?? '') ?>"
                                        data-services="<?= htmlspecialchars($data['services'] ?? '') ?>"
                                        data-status="<?= $data['status'] ?? '' ?>"
                                        data-bs-toggle="modal" data-bs-target="#updateModal"
                                        title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Delete Button -->
                                <button class="btn btn-modern-delete deleteBtn"
                                        data-email="<?= htmlspecialchars($email) ?>"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        title="Delete User">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="update_user.php" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="updateLabel">Update User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="email" id="updateEmail">

                <div class="mb-3">
                    <label>Password</label>
                    <input type="text" name="password" class="form-control" id="updatePassword" required>
                </div>

                <div class="mb-3">
                    <label>Account Type</label>
                    <select name="account_type" class="form-select" id="updateAccount" required>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                        <option value="Service Provider">Service Provider</option>
                    </select>
                </div>

                <!-- Service Provider fields -->
                <div class="service-provider-fields d-none">
                    <div class="mb-3">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" id="updateCompany">
                    </div>
                    <div class="mb-3">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" id="updateContactPerson">
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" id="updateContactNumber">
                    </div>
                    <div class="mb-3">
                        <label>Services Offered</label>
                        <input type="text" name="services" class="form-control" id="updateServices">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" id="updateStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="update_user" class="btn btn-warning">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="delete_user.php" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="email" id="deleteEmail">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="delete_user" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="add_user.php" method="POST" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addUserLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" id="addEmail" required>
                </div>

                <div class="mb-3">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="text" name="password" class="form-control" id="addPassword" required>
                </div>

                <div class="mb-3">
                    <label>Account Type <span class="text-danger">*</span></label>
                    <select name="account_type" class="form-select" id="addAccount" required>
                        <option value="">Select Account Type</option>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                        <option value="Service Provider">Service Provider</option>
                    </select>
                </div>

                <!-- Service Provider fields -->
                <div class="add-service-provider-fields d-none">
                    <div class="mb-3">
                        <label>Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" id="addCompany">
                    </div>
                    <div class="mb-3">
                        <label>Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person" class="form-control" id="addContactPerson">
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" id="addContactNumber">
                    </div>
                    <div class="mb-3">
                        <label>Services Offered</label>
                        <input type="text" name="services" class="form-control" id="addServices">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" id="addStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_user" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll("tbody tr");
    const filterCards = document.querySelectorAll(".card-dashboard");

    // Filter table
    filterCards.forEach(card => {
        card.addEventListener("click", () => {
            const type = card.getAttribute("data-type");
            rows.forEach(row => {
                if (type === "All" || row.dataset.account === type) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });

    // Show/hide Service Provider fields in update modal
    document.getElementById("updateAccount").addEventListener("change", function() {
        const spFields = document.querySelector(".service-provider-fields");
        if (this.value === "Service Provider") spFields.classList.remove("d-none");
        else spFields.classList.add("d-none");
    });

    // Show/hide Service Provider fields in add modal
    document.getElementById("addAccount").addEventListener("change", function() {
        const spFields = document.querySelector(".add-service-provider-fields");
        if (this.value === "Service Provider") spFields.classList.remove("d-none");
        else spFields.classList.add("d-none");
    });

    // Fill update modal
    document.querySelectorAll(".updateBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("updateEmail").value = this.dataset.email;
            document.getElementById("updatePassword").value = this.dataset.password;
            document.getElementById("updateAccount").value = this.dataset.account;
            document.getElementById("updateCompany").value = this.dataset.company;
            document.getElementById("updateContactPerson").value = this.dataset.contact_person;
            document.getElementById("updateContactNumber").value = this.dataset.contact_number;
            document.getElementById("updateServices").value = this.dataset.services;
            document.getElementById("updateStatus").value = this.dataset.status;

            // Trigger change event to show/hide SP fields
            document.getElementById("updateAccount").dispatchEvent(new Event('change'));
        });
    });

    // Delete modal
    document.querySelectorAll(".deleteBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("deleteEmail").value = this.dataset.email;
            document.getElementById("deleteUserName").textContent = this.dataset.email;
        });
    });
    
    // Form validation for update
    document.querySelector('form[action="update_user.php"]').addEventListener('submit', function(e) {
        const password = document.getElementById('updatePassword').value;
        const accountType = document.getElementById('updateAccount').value;
        const companyName = document.getElementById('updateCompany').value;
        const contactPerson = document.getElementById('updateContactPerson').value;
        
        if (!password.trim()) {
            e.preventDefault();
            alert('Password is required');
            return false;
        }
        
        if (accountType === 'Service Provider') {
            if (!companyName.trim()) {
                e.preventDefault();
                alert('Company Name is required for Service Providers');
                return false;
            }
            if (!contactPerson.trim()) {
                e.preventDefault();
                alert('Contact Person is required for Service Providers');
                return false;
            }
        }
    });

    // Form validation for add user
    document.querySelector('form[action="add_user.php"]').addEventListener('submit', function(e) {
        const email = document.getElementById('addEmail').value;
        const password = document.getElementById('addPassword').value;
        const accountType = document.getElementById('addAccount').value;
        const companyName = document.getElementById('addCompany').value;
        const contactPerson = document.getElementById('addContactPerson').value;
        
        if (!email.trim()) {
            e.preventDefault();
            alert('Email is required');
            return false;
        }
        
        if (!password.trim()) {
            e.preventDefault();
            alert('Password is required');
            return false;
        }
        
        if (!accountType) {
            e.preventDefault();
            alert('Please select an account type');
            return false;
        }
        
        if (accountType === 'Service Provider') {
            if (!companyName.trim()) {
                e.preventDefault();
                alert('Company Name is required for Service Providers');
                return false;
            }
            if (!contactPerson.trim()) {
                e.preventDefault();
                alert('Contact Person is required for Service Providers');
                return false;
            }
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.auto-fade');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.display = 'none';
            }
        });
    }, 5000);
});
</script>

<?php include('footer.php'); ?>
