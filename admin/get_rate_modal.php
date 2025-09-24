<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';

if (!isset($_GET['id'])) {
    echo "<div class='modal-body'><div class='alert alert-danger'>Invalid request.</div></div>";
    exit;
}

$rate_id = intval($_GET['id']);

$sql = "SELECT fr.rate_id, fr.mode, fr.distance_range, fr.weight_range, fr.rate, fr.unit, fr.status, fr.created_at,
               sp.company_name, sp.contact_person, sp.contact_number
        FROM freight_rates fr
        JOIN active_service_provider sp ON fr.provider_id = sp.provider_id
        WHERE fr.rate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()):
?>
    <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Rate Details (ID: <?= $row['rate_id'] ?>)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <table class="table table-bordered">
            <tr>
                <th>Provider</th>
                <td><?= htmlspecialchars($row['company_name']) ?></td>
            </tr>
            <tr>
                <th>Contact Person</th>
                <td><?= htmlspecialchars($row['contact_person']) ?></td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td><?= htmlspecialchars($row['contact_number']) ?></td>
            </tr>
            <tr>
                <th>Mode</th>
                <td><span class="badge bg-primary"><?= htmlspecialchars($row['mode']) ?></span></td>
            </tr>
            <tr>
                <th>Distance Range</th>
                <td><?= htmlspecialchars($row['distance_range']) ?></td>
            </tr>
            <tr>
                <th>Weight/Volume Range</th>
                <td><?= htmlspecialchars($row['weight_range']) ?></td>
            </tr>
            <tr>
                <th>Rate</th>
                <td><strong>₱<?= number_format($row['rate'], 2) ?></strong></td>
            </tr>
            <tr>
                <th>Unit</th>
                <td><?= htmlspecialchars($row['unit']) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if ($row['status'] == "Accepted"): ?>
                        <span class="badge bg-success">Accepted</span>
                    <?php elseif ($row['status'] == "Rejected"): ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Pending</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Date Submitted</th>
                <td><?= date("M d, Y h:i A", strtotime($row['created_at'])) ?></td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
<?php
else:
    echo "<div class='modal-body'><div class='alert alert-warning'>Rate not found.</div></div>";
endif;

$stmt->close();
$conn->close();
?>
