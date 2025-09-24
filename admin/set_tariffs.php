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

<!-- Main Content -->
<div class="content">
  <h3 class="mb-4">SET RATES</h3>
  
  <?php if (isset($_GET['success']) && $_GET['success'] === 'rates_sent'): ?>
    <div class="alert alert-success alert-dismissible fade show auto-fade" role="alert">
      Rates Sent Successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <table class="table table-hover table-striped">
    <thead class="table-secondary">
      <tr>
        <th>Provider ID</th>
        <th>Company Name</th>
        <th>Contact Person</th>
        <th>Contact Number</th>
        <th>Address</th>
        <th>Services</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
        // Fetch active providers
        $sql = "SELECT provider_id, company_name, contact_person, contact_number, address, services 
                FROM active_service_provider";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $providerId = $row['provider_id'];
            ?>
            <tr>
              <td><?php echo $row['provider_id']; ?></td>
              <td><?php echo $row['company_name']; ?></td>
              <td><?php echo $row['contact_person']; ?></td>
              <td><?php echo $row['contact_number']; ?></td>
              <td><?php echo $row['address']; ?></td>
              <td><?php echo $row['services']; ?></td>
              <td>
                <!-- Send Rates Button opens modal -->
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#sendRatesModal<?php echo $providerId; ?>">
                 Send Rates

                </button>
              </td>
            </tr>

            <!-- Modal for Sending Rates -->
            <div class="modal fade" id="sendRatesModal<?php echo $providerId; ?>" tabindex="-1" aria-labelledby="sendRatesLabel<?php echo $providerId; ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="sendRatesLabel<?php echo $providerId; ?>">
                      Send Rates to <?php echo $row['company_name']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">

      <!-- 📌 Suggested Standard Rates -->
<div class="alert alert-info">
  <h6 class="fw-bold">📘 Suggested Standard Rates Guide & Input Instructions</h6>
  <ul>
    <li>
      <b>Land Freight (per km):</b>  
      <ul>
        <li>Light Van: ₱20 – ₱50</li>
        <li>6-Wheeler: ₱50 – ₱80</li>
        <li>Wing Van (10W/12W): ₱80 – ₱150</li>
      </ul>
      <small>💡 Input example: <i>Distance Range:</i> "0-50 km", <i>Weight:</i> "0-500 kg", <i>Rate:</i> 70.00, <i>Unit:</i> "per km".</small>
    </li>
    <li>
      <b>Air Cargo (per kg):</b> ₱150 – ₱300 depending on distance & handling.  
      <small>💡 Input example: <i>Distance Range:</i> "Domestic" or "International", <i>Weight:</i> "0-100 kg", <i>Rate:</i> 250.00, <i>Unit:</i> "per kg".</small>
    </li>
    <li>
      <b>Sea Cargo:</b>  
      <ul>
        <li>₱3,000 – ₱7,000 per 20ft container</li>
        <li>₱5,000 – ₱12,000 per 40ft container</li>
        <li>₱300 – ₱800 per cbm (loose cargo)</li>
      </ul>
      <small>💡 Input example: <i>Distance Range:</i> "Manila – Cebu", <i>Weight/Volume:</i> "1-10 cbm", <i>Rate:</i> 500.00, <i>Unit:</i> "per cbm".</small>
    </li>
  </ul>
  <hr>
  <p class="mb-1"><b>📌 How to Fill Out the Form:</b></p>
  <ol class="mb-0">
    <li><b>Mode of Transport:</b> Choose <i>Land, Air, or Sea</i>.</li>
    <li><b>Distance Range:</b> Enter travel range (e.g., "0-100 km", "Domestic", "International").</li>
    <li><b>Weight/Volume Range:</b> Specify cargo load (e.g., "0-500 kg", "10-20 tons", "1-10 cbm").</li>
    <li><b>Rate (₱):</b> Input your charge (numeric only, e.g., 120.50).</li>
    <li><b>Unit:</b> Select the applicable rate basis (per km, per kg, per cbm, per container).</li>
  </ol>
  <small class="text-muted">⚠️ Make sure your rates are competitive but realistic to avoid rejections.</small>
</div>


                    <!-- Form -->
                    <form method="POST" action="">
                      <input type="hidden" name="provider_id" value="<?php echo $providerId; ?>">

                      <div class="mb-3">
                        <label class="form-label">Mode of Transport</label>
                        <select name="mode" class="form-control" required>
                          <option value="land">Land</option>
                          <option value="air">Air</option>
                          <option value="sea">Sea</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Distance Range</label>
                        <input type="text" name="distance_range" class="form-control" placeholder="e.g., 0-50 km or N/A for Sea/Air" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Weight/Volume Range</label>
                        <input type="text" name="weight_range" class="form-control" placeholder="e.g., 0-500 kg or 1-10 cbm" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Rate (₱)</label>
                        <input type="number" step="0.01" name="rate" class="form-control" placeholder="e.g., 70.00" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <select name="unit" class="form-control" required>
                          <option value="per km">per km</option>
                          <option value="per ton">per ton</option>
                          <option value="per kg">per kg</option>
                          <option value="per cbm">per cbm</option>
                          <option value="per container">per container</option>
                        </select>
                      </div>

                      <div class="modal-footer">
                        <button type="submit" name="submit_rates" class="btn btn-success">Submit Rates</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        } else {
          echo "<tr><td colspan='7' class='text-center'>No active service providers found.</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<?php
// Handle form submission
if (isset($_POST['submit_rates'])) {
    $provider_id    = $_POST['provider_id'];
    $mode           = $_POST['mode'];
    $distance_range = $_POST['distance_range'];
    $weight_range   = $_POST['weight_range'];
    $rate           = $_POST['rate'];
    $unit           = $_POST['unit'];

    $sql = "INSERT INTO freight_rates (provider_id, mode, distance_range, weight_range, rate, unit) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssds", $provider_id, $mode, $distance_range, $weight_range, $rate, $unit);

    if ($stmt->execute()) {
        echo "<script>window.location='set_tariffs.php?success=rates_sent';</script>";

    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<?php include('footer.php'); ?>
