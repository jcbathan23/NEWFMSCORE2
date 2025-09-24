<?php
require '../connect.php';
require_once __DIR__ . '/auth.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM active_service_provider WHERE provider_id = $id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='modal-body'><div class='alert alert-danger'>Provider not found.</div></div>";
        exit;
    }
} else {
    echo "<div class='modal-body'><div class='alert alert-warning'>No ID provided.</div></div>";
    exit;
}
?>

<div class="modal-header bg-info text-black">
  <h5 class="modal-title">Service Provider Details</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
  <table class="table table-bordered">
    <tr><th>Company Name</th><td><?= htmlspecialchars($row['company_name']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($row['email']) ?></td></tr>
    <tr><th>Contact Person</th><td><?= htmlspecialchars($row['contact_person']) ?></td></tr>
    <tr><th>Contact Number</th><td><?= htmlspecialchars($row['contact_number']) ?></td></tr>
    <tr><th>Address</th><td><?= htmlspecialchars($row['address']) ?></td></tr>
    <tr><th>Services Offered</th><td><?= htmlspecialchars($row['services']) ?></td></tr>
    <tr><th>ISO Certified</th><td><?= htmlspecialchars($row['iso_certified']) ?></td></tr>
    <tr>
  <th>Business Permit</th>
  <td>
    <?php
      $permit = $row['business_permit'];
      $ext = pathinfo($permit, PATHINFO_EXTENSION);
      if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
        echo "<img src='uploads/$permit' style='max-width:100%; height:auto; display:block; margin:auto;'>";
      } elseif ($ext === 'pdf') {
        echo "<embed src='uploads/$permit' type='application/pdf' width='100%' height='600' style='display:block;'>";
      } else {
        echo "No valid file.";
      }
    ?>
  </td>
</tr>
<tr>
  <th>Company Profile</th>
  <td>
    <?php
      $profile = $row['company_profile'];
      $ext = pathinfo($profile, PATHINFO_EXTENSION);
      if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
        echo "<img src='uploads/$profile' style='max-width:100%; height:auto; display:block; margin:auto;'>";
      } elseif ($ext === 'pdf') {
        echo "<embed src='uploads/$profile' type='application/pdf' width='100%' height='600' style='display:block;'>";
      } else {
        echo "No valid file.";
      }
    ?>
  </td>
</tr>

  </table>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
          