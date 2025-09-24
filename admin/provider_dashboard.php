<?php
session_start();
include("../connect.php");

// Only allow logged-in providers
if (!isset($_SESSION['email']) || $_SESSION['account_type'] != 3) {
    header("Location: ../admin/loginpage.php");
    exit();
}

$email = $_SESSION['email'];
$company_name = "";
$account_status = ""; // "pending" or "active"

// Get company name
$stmt = $conn->prepare("SELECT company_name FROM active_service_provider WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($company_name);
if ($stmt->fetch()) {
    $account_status = "active";
}
$stmt->close();

if (empty($company_name)) {
    $stmt = $conn->prepare("SELECT company_name FROM pending_service_provider WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($company_name);
    if ($stmt->fetch()) {
        $account_status = "pending";
    }
    $stmt->close();
}

if (empty($company_name)) {
    $company_name = "Service Provider";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="providerstyles.css" rel="stylesheet">
</head>
<body>

<?php include('provider_sidebar.php'); ?>

<div class="main-content">
    <?php include('provider_navbar.php'); ?>

    <div class="content">
      <h3 class="mb-4">Dashboard</h3>
      <div class="row g-3">
          <div class="col-md-4">
              <div class="card shadow-sm <?php echo ($account_status == 'pending') ? 'disabled' : ''; ?>" 
                   <?php if($account_status == 'pending') echo 'data-bs-toggle="tooltip" data-bs-placement="top" title="You cannot access this until approved"'; ?>>
                  <div class="card-body text-center">
                      <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                      <h5 class="card-title">Rates</h5>
                      <p class="card-text">Manage your pricing and tariffs.</p>
                      <a href="provider_rates.php" class="btn btn-primary btn-sm">Go</a>
                  </div>
              </div>
          </div>
          <div class="col-md-4">
              <div class="card shadow-sm <?php echo ($account_status == 'pending') ? 'disabled' : ''; ?>"
                   <?php if($account_status == 'pending') echo 'data-bs-toggle="tooltip" data-bs-placement="top" title="You cannot access this until approved"'; ?>>
                  <div class="card-body text-center">
                      <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                      <h5 class="card-title">My Schedules</h5>
                      <p class="card-text">View and update your service schedules.</p>
                      <a href="provider_schedules.php" class="btn btn-success btn-sm">Go</a>
                  </div>
              </div>
          </div>
          <div class="col-md-4">
              <div class="card shadow-sm">
                  <div class="card-body text-center">
                      <i class="fas fa-user fa-2x text-info mb-2"></i>
                      <h5 class="card-title">My Profile</h5>
                      <p class="card-text">Update your company information.</p>
                      <a href="provider_profile.php" class="btn btn-info btn-sm">Go</a>
                  </div>
              </div>
          </div>
      </div>
    </div>
</div>

<?php include('provider_footer.php'); ?>
