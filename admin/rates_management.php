<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
include('../connect.php');
?>

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

<div class="content p-4">
    <h3 class="mb-4">RATES MANAGEMENT</h3>

    <!-- ✅ Alerts Section (always above table, clean spacing) -->
    <div class="mb-3">
        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] === 'updated'): ?>
                <div class="alert alert-success  alert-dismissible fade show auto-fade" role="alert">
                    Rate Updated Successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['success'] === 'deleted'): ?>
                <div class="alert alert-danger  alert-dismissible fade show auto-fade" role="alert">
                    Rate Deleted Successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ✅ Card + Table -->

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Rate ID</th>
                            <th>Provider</th>
                            <th>Mode</th>
                            <th>Distance</th>
                            <th>Weight/Volume</th>
                            <th>Rate</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT fr.rate_id, fr.mode, fr.distance_range, fr.weight_range, fr.rate, fr.unit, fr.status, fr.created_at,
                                       sp.company_name
                                FROM freight_rates fr
                                JOIN active_service_provider sp ON fr.provider_id = sp.provider_id
                                ORDER BY fr.created_at DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                $statusBadge = "<span class='badge bg-secondary'>Pending</span>";
                                if ($row['status'] == "Accepted") {
                                    $statusBadge = "<span class='badge bg-success'>Accepted</span>";
                                } elseif ($row['status'] == "Rejected") {
                                    $statusBadge = "<span class='badge bg-danger'>Rejected</span>";
                                }
                        ?>
                            <tr>
                                <td><?= $row['rate_id'] ?></td>
                                <td><strong><?= htmlspecialchars($row['company_name']) ?></strong></td>
                                <td><span class="badge bg-primary"><?= $row['mode'] ?></span></td>
                                <td><?= htmlspecialchars($row['distance_range']) ?></td>
                                <td><?= htmlspecialchars($row['weight_range']) ?></td>
                                <td><strong>₱<?= number_format($row['rate'], 2) ?></strong></td>
                                <td><?= htmlspecialchars($row['unit']) ?></td>
                                <td><?= $statusBadge ?></td>
                                <td><?= date("M d, Y h:i A", strtotime($row['created_at'])) ?></td>
                                <td class="text-nowrap">
                                    <!-- View Button -->
                                    <button class="btn btn-info btn-sm viewRateBtn" data-id="<?= $row['rate_id'] ?>" data-bs-toggle="modal" data-bs-target="#viewRateModal">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Update Button -->
                                    <button class="btn btn-warning btn-sm updateRateBtn"
                                            data-id="<?= $row['rate_id'] ?>"
                                            data-mode="<?= htmlspecialchars($row['mode']) ?>"
                                            data-distance="<?= htmlspecialchars($row['distance_range']) ?>"
                                            data-weight="<?= htmlspecialchars($row['weight_range']) ?>"
                                            data-rate="<?= htmlspecialchars($row['rate']) ?>"
                                            data-unit="<?= htmlspecialchars($row['unit']) ?>"
                                            data-status="<?= htmlspecialchars($row['status']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#updateRateModal">
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button class="btn btn-danger btn-sm deleteRateBtn"
                                            data-id="<?= $row['rate_id'] ?>"
                                            data-bs-toggle="modal" data-bs-target="#deleteRateModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                            echo "<tr><td colspan='10' class='text-center text-muted'>No freight rates submitted yet.</td></tr>";
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

<!-- ✅ View Modal -->
<div class="modal fade" id="viewRateModal" tabindex="-1" aria-labelledby="viewRateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="viewRateContent">
            <!-- Dynamic content loaded via fetch -->
        </div>
    </div>
</div>

<!-- ✅ Update Modal -->
<div class="modal fade" id="updateRateModal" tabindex="-1" aria-labelledby="updateRateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="update_rate.php" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="updateRateLabel">Update Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="rate_id" id="updateRateId">
                <div class="mb-3"><label>Mode</label><input type="text" name="mode" id="updateMode" class="form-control" required></div>
                <div class="mb-3"><label>Distance Range</label><input type="text" name="distance_range" id="updateDistance" class="form-control" required></div>
                <div class="mb-3"><label>Weight/Volume Range</label><input type="text" name="weight_range" id="updateWeight" class="form-control" required></div>
                <div class="mb-3"><label>Rate (₱)</label><input type="number" step="0.01" name="rate" id="updateRateValue" class="form-control" required></div>
                <div class="mb-3"><label>Unit</label><input type="text" name="unit" id="updateUnit" class="form-control" required></div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" id="updateStatus" class="form-select">
                        <option value="Pending">Pending</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_rate" class="btn btn-warning"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ Delete Modal -->
<div class="modal fade" id="deleteRateModal" tabindex="-1" aria-labelledby="deleteRateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="delete_rate.php" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteRateLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="rate_id" id="deleteRateId">
                <p>Are you sure you want to delete this <strong>Rate</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="delete_rate" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // View modal
    document.querySelectorAll(".viewRateBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            const rateId = this.dataset.id;
            fetch("get_rate_modal.php?id=" + rateId)
                .then(res => res.text())
                .then(html => document.getElementById("viewRateContent").innerHTML = html)
                .catch(() => {
                    document.getElementById("viewRateContent").innerHTML =
                        "<div class='modal-body'><div class='alert alert-danger'>Failed to load details.</div></div>";
                });
        });
    });

    // Update modal
    document.querySelectorAll(".updateRateBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("updateRateId").value = this.dataset.id;
            document.getElementById("updateMode").value = this.dataset.mode;
            document.getElementById("updateDistance").value = this.dataset.distance;
            document.getElementById("updateWeight").value = this.dataset.weight;
            document.getElementById("updateRateValue").value = this.dataset.rate;
            document.getElementById("updateUnit").value = this.dataset.unit;
            document.getElementById("updateStatus").value = this.dataset.status;
        });
    });

    // Delete modal
    document.querySelectorAll(".deleteRateBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("deleteRateId").value = this.dataset.id;
        });
    });
});
</script>

<?php include('footer.php'); ?>
