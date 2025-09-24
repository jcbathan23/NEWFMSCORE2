<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connect.php');
require_once __DIR__ . '/auth.php';

$query = "SELECT provider_id, company_name FROM active_service_provider WHERE status = 'active'";
$result = mysqli_query($conn, $query);

if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}
?>

<select>
  <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <option value="<?= htmlspecialchars($row['provider_id']) ?>">
      <?= htmlspecialchars($row['company_name']) ?>
    </option>
  <?php endwhile; ?>
</select>
