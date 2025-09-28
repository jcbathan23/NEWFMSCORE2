<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
include('../connect.php');
?>

<link rel="stylesheet" href="modern-table-styles.css">

<div class="content p-4">
    <!-- Header Section -->
    <div class="modern-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Archived SOPs</h3>
            <p>View and manage archived Standard Operating Procedures</p>
        </div>
        <a href="view_sop.php" class="btn btn-modern-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Active SOPs
        </a>
    </div>

    <!-- Modern Archived SOPs Table -->
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-file-alt me-2"></i>Title</th>
                        <th><i class="fas fa-tag me-2"></i>Category</th>
                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                        <th><i class="fas fa-calendar me-2"></i>Created</th>
                        <th class="text-center"><i class="fas fa-tools me-2"></i>Actions</th>
                    </tr>
                </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM sop_documents WHERE status='Archived' ORDER BY created_at DESC");
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['sop_id']}</td>
                      <td>{$row['title']}</td>
                      <td>{$row['category']}</td>
                      <td><span class='badge bg-secondary'>{$row['status']}</span></td>
                      <td>{$row['created_at']}</td>
                      <td>
                        <!-- View Button -->
                        <button class='btn btn-info btn-sm viewBtn' 
                          data-id='{$row['sop_id']}'
                          data-title='{$row['title']}'
                          data-category='{$row['category']}'
                          data-status='{$row['status']}'
                          data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'
                          data-file='{$row['file_path']}'>
                          <i class='fas fa-eye'></i>
                        </button>

<!-- Unarchive Button (opens modal) -->
<button class='btn btn-success btn-sm unarchiveBtn'
  data-id='{$row['sop_id']}'
  data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "'
  data-bs-toggle='modal' data-bs-target='#unarchiveSopModal'>
  <i class='fas fa-folder-open'></i>
</button>



                        <!-- Print Button -->
                        <a href='print_sop.php?id={$row['sop_id']}' target='_blank' class='btn btn-dark btn-sm'>
                          <i class='fas fa-print'></i>
                        </a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='6' class='text-center'>No archived SOPs found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewSopModal" tabindex="-1" aria-labelledby="viewSopLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewSopLabel">SOP Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="sopTitle"></span></p>
        <p><strong>Category:</strong> <span id="sopCategory"></span></p>
        <p><strong>Status:</strong> <span id="sopStatus"></span></p>
        <p><strong>Content:</strong></p>
        <div class="border rounded p-2 bg-light" id="sopContent"></div>
        <p><strong>Attached File:</strong> <a href="" target="_blank" id="sopFileLink">No file uploaded</a></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Clean Unarchive Modal -->
<div class="modal fade" id="unarchiveSopModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Unarchive SOP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Are you sure you want to unarchive <strong id="unarchiveSopTitle"></strong>?</p>
      </div>

      <div class="modal-footer">
        <form method="POST" action="unarchive_sop.php">
          <input type="hidden" name="sop_id" id="unarchiveSopId">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Unarchive</button>
        </form>
      </div>

    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
  // View Modal
  document.querySelectorAll(".viewBtn").forEach(btn => {
    btn.addEventListener("click", function () {
      document.getElementById("sopTitle").innerText = this.dataset.title;
      document.getElementById("sopCategory").innerText = this.dataset.category;
      document.getElementById("sopStatus").innerText = this.dataset.status;
      document.getElementById("sopContent").innerHTML = this.dataset.content.replace(/\n/g, "<br>");

      let filePath = this.dataset.file;
      let fileLink = document.getElementById("sopFileLink");
      if (filePath && filePath.trim() !== "") {
        fileLink.href = "../uploads/" + filePath;
        fileLink.innerText = "Download File";
      } else {
        fileLink.href = "#";
        fileLink.innerText = "No file uploaded";
      }

      new bootstrap.Modal(document.getElementById("viewSopModal")).show();
    });
  });

  // Unarchive Modal
  document.querySelectorAll(".unarchiveBtn").forEach(btn => {
    btn.addEventListener("click", function () {
      document.getElementById("unarchiveSopId").value = this.dataset.id;
      document.getElementById("unarchiveSopTitle").innerText = this.dataset.title;
      new bootstrap.Modal(document.getElementById("unarchiveModal")).show();
    });
  });
});
// Unarchive Button
document.querySelectorAll(".unarchiveBtn").forEach(btn => {
  btn.addEventListener("click", function () {
    document.getElementById("unarchiveSopId").value = this.dataset.id;
    document.getElementById("unarchiveSopTitle").textContent = this.dataset.title;
  });
});

</script>

<?php include('footer.php'); ?>
