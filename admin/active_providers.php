<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('navbar.php'); ?>

<style>
  /* Page-specific override: remove background from the H3 title */
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important; /* keep text color consistent with theme */
  }
</style>

<div class="content p-4">
    <h3 class="mb-4">ACTIVE SERVICE PROVIDERS</h3>

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show auto-fade" role="alert">
                Service Provider Details Updated!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'deleted'): ?>
            <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
                Service Provider Deleted!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Providers Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>Contact Person</th>
                    <th>Contact Number</th>
                    <th>Services Offered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM active_service_provider";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $row['provider_id'] ?></td>
                        <td><?= htmlspecialchars($row['company_name']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['contact_person']) ?></td>
                        <td><?= htmlspecialchars($row['contact_number']) ?></td>
                        <td><?= htmlspecialchars($row['services']) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <!-- View Button -->
                            <button class="btn btn-info btn-sm viewBtn" data-id="<?= $row['provider_id'] ?>" data-bs-toggle="modal" data-bs-target="#viewProviderModal">
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Update Button -->
                            <button class="btn btn-warning btn-sm updateBtn" 
                                    data-id="<?= $row['provider_id'] ?>"
                                    data-company="<?= htmlspecialchars($row['company_name']) ?>"
                                    data-address="<?= htmlspecialchars($row['address']) ?>"
                                    data-contact_person="<?= htmlspecialchars($row['contact_person']) ?>"
                                    data-contact_number="<?= htmlspecialchars($row['contact_number']) ?>"
                                    data-services="<?= htmlspecialchars($row['services']) ?>"
                                    data-status="<?= $row['status'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#updateModal">
                                <i class="fas fa-pen"></i>
                            </button>

                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm deleteBtn" 
                                    data-id="<?= $row['provider_id'] ?>"
                                    data-company="<?= htmlspecialchars($row['company_name']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                    echo "<tr><td colspan='8' class='text-center'>No active providers found.</td></tr>";
                endif;
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewProviderModal" tabindex="-1" aria-labelledby="viewProviderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="modalContent">
                <!-- Dynamic content loaded via fetch -->
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="update_provider.php" method="POST" class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="updateLabel">Update Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="provider_id" id="updateProviderId">
                    <div class="mb-3">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" id="updateCompany" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" id="updateAddress" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" id="updateContactPerson" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" id="updateContactNumber" required>
                    </div>
                    <div class="mb-3">
                        <label>Services Offered</label>
                        <input type="text" name="services" class="form-control" id="updateServices" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" id="updateStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_provider" class="btn btn-warning">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="delete_provider.php" method="POST" class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="provider_id" id="deleteProviderId">
                    <p>Are you sure you want to delete <strong id="deleteProviderName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_provider" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS for dynamic modal content -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // View Button
    document.querySelectorAll(".viewBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            const providerId = this.getAttribute("data-id");
            fetch("get_provider_modal1.php?id=" + providerId)
                .then(res => res.text())
                .then(html => {
                    document.getElementById("modalContent").innerHTML = html;
                })
                .catch(() => {
                    document.getElementById("modalContent").innerHTML = "<div class='modal-body'><div class='alert alert-danger'>Failed to load details.</div></div>";
                });
        });
    });

    // Update Button
    document.querySelectorAll(".updateBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("updateProviderId").value = this.dataset.id;
            document.getElementById("updateCompany").value = this.dataset.company;
            document.getElementById("updateAddress").value = this.dataset.address;
            document.getElementById("updateContactPerson").value = this.dataset.contact_person;
            document.getElementById("updateContactNumber").value = this.dataset.contact_number;
            document.getElementById("updateServices").value = this.dataset.services;
            document.getElementById("updateStatus").value = this.dataset.status;
        });
    });

    // Delete Button
    document.querySelectorAll(".deleteBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("deleteProviderId").value = this.dataset.id;
            document.getElementById("deleteProviderName").textContent = this.dataset.company;
        });
    });
});
</script>

<?php include('footer.php'); ?>
  