<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
?>

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

<div class="content p-4">
    <h3 class="mb-4">PENDING SERVICE PROVIDERS</h3>

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

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>Registration ID</th>
                    <th>Provider Name</th>
                    <th>Address</th>
                    <th>Contact Person</th>
                    <th>Contact Number</th>
                    <th>Services Offered</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $query = "SELECT * FROM pending_service_provider";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    // Status badge color
                    $statusColor = $row['status'] === 'Pending' ? 'bg-warning text-dark' : ($row['status'] === 'Approved' ? 'bg-success' : 'bg-danger text-white');
            ?>
                <tr class="align-middle">
                    <td><?= $row['registration_id'] ?></td>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['contact_person']) ?></td>
                    <td><?= htmlspecialchars($row['contact_number']) ?></td>
                    <td><?= htmlspecialchars($row['services']) ?></td>
                    <td><span class="badge <?= $statusColor ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td class="text-center">
                        <!-- View -->
                        <button class="btn btn-info btn-sm me-1 viewBtn" data-id="<?= $row['registration_id'] ?>" data-bs-toggle="modal" data-bs-target="#viewProviderModal">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Approve -->
                        <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#approveModal<?= $row['registration_id'] ?>">
                            <i class="fas fa-check-circle"></i>
                        </button>

                        <!-- Reject -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $row['registration_id'] ?>">
                            <i class="fas fa-times-circle"></i>
                        </button>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal<?= $row['registration_id'] ?>" tabindex="-1" aria-labelledby="approveModalLabel<?= $row['registration_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
    <div class="modal-content">
      

      <form action="approve_provider.php" method="POST">
        <input type="hidden" name="registration_id" value="<?= $row['registration_id'] ?>">
<div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="approveModalLabel<?= $row['registration_id'] ?>">Approve Provider</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
        <div class="modal-body text-center">
          Are you sure you want to approve <strong><?= htmlspecialchars($row['company_name']) ?></strong>?
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm">Approve</button>
        </div>
      </form>

    </div>
  </div>
</div>




<!-- Reject Modal --> <div class="modal fade" id="rejectModal<?= $row['registration_id'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $row['registration_id'] ?>" aria-hidden="true"> <div class="modal-dialog modal-sm modal-dialog-centered"> 
    <div class="modal-content"> 
        <form action="reject_provider.php" method="POST"> 
            <input type="hidden" name="registration_id" value="<?= $row['registration_id'] ?>"> 
            <div class="modal-header bg-danger text-white"> <h5 class="modal-title" id="rejectModalLabel<?= $row['registration_id'] ?>">Confirm Rejection</h5> <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button> </div> <div class="modal-body"> Are you sure you want to reject <strong><?= htmlspecialchars($row['company_name']) ?></strong>? </div> <div class="modal-footer"> <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button> <button type="submit" class="btn btn-danger btn-sm">Reject</button> </div> </form> </div> </div> </div>



                    </td>
                </tr>
            <?php
                endwhile;
            else:
                echo "<tr><td colspan='8' class='text-center text-muted'>No pending providers found.</td></tr>";
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
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const buttons = document.querySelectorAll(".viewBtn");
                        buttons.forEach(btn => {
                            btn.addEventListener("click", function () {
                                const providerId = this.getAttribute("data-id");
                                fetch("get_provider_modal.php?id=" + providerId)
                                    .then(response => response.text())
                                    .then(html => { document.getElementById("modalContent").innerHTML = html; })
                                    .catch(err => { document.getElementById("modalContent").innerHTML = "<div class='modal-body'><div class='alert alert-danger'>Failed to load details.</div></div>"; });
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

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
</style>

<?php include('footer.php'); ?>
