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
.table-responsive { max-height: 500px; overflow-y: auto; }


  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
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
                User Details Updated!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'deleted'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                User Deleted!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Account Type</th>
                    <th>Actions</th>
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
                    <tr data-account="<?= htmlspecialchars($data['account_type']) ?>">
                        <td><?= htmlspecialchars($email) ?></td>
                        <td><?= htmlspecialchars($data['password']) ?></td>
                        <td>
                            <span class="badge <?= $data['account_type']==='Admin' ? 'bg-danger' : ($data['account_type']==='User' ? 'bg-primary' : 'bg-success') ?>">
                                <?= htmlspecialchars($data['account_type']) ?>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <!-- Update Button -->
                            <button class="btn btn-warning btn-sm updateBtn"
                                    data-email="<?= htmlspecialchars($email) ?>"
                                    data-password="<?= htmlspecialchars($data['password']) ?>"
                                    data-account="<?= htmlspecialchars($data['account_type']) ?>"
                                    data-company="<?= htmlspecialchars($data['company_name'] ?? '') ?>"
                                    data-contact_person="<?= htmlspecialchars($data['contact_person'] ?? '') ?>"
                                    data-contact_number="<?= htmlspecialchars($data['contact_number'] ?? '') ?>"
                                    data-services="<?= htmlspecialchars($data['services'] ?? '') ?>"
                                    data-status="<?= $data['status'] ?? '' ?>"
                                    data-bs-toggle="modal" data-bs-target="#updateModal"
                                    title="Update">
                                <i class="fas fa-pen"></i>
                            </button>

                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm deleteBtn"
                                    data-email="<?= htmlspecialchars($email) ?>"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
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
});
</script>

<?php include('footer.php'); ?>
