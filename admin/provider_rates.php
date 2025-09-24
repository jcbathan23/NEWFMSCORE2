<?php
session_start();
include("../connect.php");

// ✅ Only allow logged-in providers (account_type = 3)
if (!isset($_SESSION['email']) || $_SESSION['account_type'] != 3) {
    header("Location: ../admin/loginpage.php");
    exit();
}

$email = $_SESSION['email'];
$company_name = "";
$account_status = ""; 
$provider_id = null;

// 🔹 Check active providers
$stmt = $conn->prepare("SELECT provider_id, company_name FROM active_service_provider WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($provider_id, $company_name);
if ($stmt->fetch()) {
    $account_status = "active";
}
$stmt->close();

// 🔹 If not active, check pending
if (empty($company_name)) {
    $stmt = $conn->prepare("SELECT provider_id, company_name FROM pending_service_provider WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($provider_id, $company_name);
    if ($stmt->fetch()) {
        $account_status = "pending";
    }
    $stmt->close();
}

// Default if not found
if (empty($company_name)) {
    $company_name = "Service Provider";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Dashboard - Freight Rates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="providerstyles.css" rel="stylesheet">
</head>
<body>

<?php include('provider_sidebar.php'); ?>

<div class="main-content">
    <?php include('provider_navbar.php'); ?>

    <div class="content">
      <h3 class="mb-4">📦 My Freight Rates</h3>

      <div class="card shadow-lg border-0">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>Rate ID</th>
                <th>Mode</th>
                <th>Distance Range</th>
                <th>Weight/Volume Range</th>
                <th>Rate (₱)</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if ($provider_id) {
                  $sql = "SELECT rate_id, mode, distance_range, weight_range, rate, unit, status, created_at
                          FROM freight_rates 
                          WHERE provider_id = ?
                          ORDER BY created_at DESC";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $provider_id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      // 🔹 Status badge
                      $statusBadge = "<span class='badge bg-secondary'>Pending</span>";
                      if ($row['status'] == "Accepted") {
                          $statusBadge = "<span class='badge bg-success'>Accepted</span>";
                      } elseif ($row['status'] == "Rejected") {
                          $statusBadge = "<span class='badge bg-danger'>Rejected</span>";
                      }

                      echo "<tr>
                              <td>{$row['rate_id']}</td>
                              <td><span class='badge bg-primary'>{$row['mode']}</span></td>
                              <td>{$row['distance_range']}</td>
                              <td>{$row['weight_range']}</td>
                              <td><strong>₱".number_format($row['rate'], 2)."</strong></td>
                              <td>{$row['unit']}</td>
                              <td>{$statusBadge}</td>
                              <td>".date("M d, Y h:i A", strtotime($row['created_at']))."</td>
                              <td>";
                      
                      // 🔹 Only allow Accept/Reject if pending
                      if ($row['status'] == "Pending") {
                          echo "<a href='process_rate.php?action=accept&rate_id={$row['rate_id']}' 
                                   class='btn btn-success btn-sm me-2'>
                                   ✅ Accept
                                </a>
                                <a href='process_rate.php?action=reject&rate_id={$row['rate_id']}' 
                                   class='btn btn-danger btn-sm'>
                                   ❌ Reject
                                </a>";
                      } else {
                          echo "<span class='text-muted'>No Action</span>";
                      }

                      echo "</td></tr>";
                    }
                  } else {
                    echo "<tr><td colspan='9' class='text-muted'>No freight rates submitted yet.</td></tr>";
                  }
                  $stmt->close();
                } else {
                  echo "<tr><td colspan='9' class='text-muted'>No provider account found.</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<?php include('provider_footer.php'); ?>

</body>
</html>
