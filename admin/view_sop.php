  <?php
  include('header.php');
  include('sidebar.php');
  include('navbar.php');
  include('../connect.php');

  // ✅ Handle archiving inline
  if (isset($_GET['archive_id'])) {
      $sop_id = intval($_GET['archive_id']);

      $sql = "UPDATE sop_documents SET status='Archived' WHERE sop_id=?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $sop_id);

      if ($stmt->execute()) {
          echo "<script>alert('SOP archived successfully.'); window.location='view_sop.php';</script>";
      } else {
          echo "<script>alert('Error archiving SOP.'); window.location='view_sop.php';</script>";
      }

      $stmt->close();
  }
  ?>

<link rel="stylesheet" href="modern-table-styles.css">

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

<div class="content p-4">
    <!-- Header Section -->
    <div class="modern-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">SOP Management</h3>
            <p>Manage Standard Operating Procedures and documentation</p>
        </div>
        <div class="btn-group" role="group">
            <a href="create_sop.php" class="btn btn-modern-primary">
                <i class="fas fa-plus me-2"></i>Add SOP
            </a>
            <a href="archived_sop.php" class="btn btn-modern-view">
                <i class="fas fa-archive me-2"></i>View Archived SOPs
            </a>
        </div>
    </div>

    <!-- Modern SOP Management Table -->
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
        // Fetch only Active or Draft SOPs
        $result = $conn->query("SELECT * FROM sop_documents WHERE status IN ('Active','Draft') ORDER BY created_at DESC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='modern-table-row'>
                        <td><span class='fw-medium'>{$row['sop_id']}</span></td>
                        <td>
                            <div class='contact-info'>
                                <i class='fas fa-file-alt me-2 text-muted'></i>
                                <span class='fw-medium'>" . htmlspecialchars($row['title']) . "</span>
                            </div>
                        </td>
                        <td>
                            <span class='modern-badge badge-pending'>
                                <i class='fas fa-tag me-1'></i>
                                " . htmlspecialchars($row['category']) . "
                            </span>
                        </td>
                        <td>
                            <span class='modern-badge " . ($row['status'] == 'Active' ? 'badge-active' : 'badge-inactive') . "'>
                                <i class='fas fa-" . ($row['status'] == 'Active' ? 'check-circle' : 'pause-circle') . " me-1'></i>
                                {$row['status']}
                            </span>
                        </td>
                        <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                        <td class='text-center'>
                            <div class='action-buttons'>
                                <!-- View Button -->
                                <button class='btn btn-modern-view viewBtn' 
                                  data-id='{$row['sop_id']}'
                                  data-title='{$row['title']}'
                                  data-category='{$row['category']}'
                                  data-status='{$row['status']}'
                                  data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'
                                  data-file='{$row['file_path']}'
                                  title='View SOP'>
                                  <i class='fas fa-eye'></i>
                                </button>

                                <!-- Update Button -->
                                <button class='btn btn-modern-edit updateBtn'
                                  data-id='{$row['sop_id']}'
                                  data-title='{$row['title']}'
                                  data-category='{$row['category']}'
                                  data-status='{$row['status']}'
                                  data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'
                                  data-file='{$row['file_path']}'
                                  data-bs-toggle='modal' data-bs-target='#updateSopModal'
                                  title='Edit SOP'>
                                  <i class='fas fa-edit'></i>
                                </button>

                                <!-- Archive Button -->
                                <button class='btn btn-modern-delete archiveBtn'
                                  data-id='{$row['sop_id']}'
                                  data-title='{$row['title']}'
                                  data-bs-toggle='modal' data-bs-target='#archiveSopModal'
                                  title='Archive SOP'>
                                  <i class='fas fa-archive'></i>
                                </button>

                                <!-- Print Button -->
                                <a href='print_sop.php?id={$row['sop_id']}' target='_blank' class='btn btn-modern-view' title='Print SOP'>
                                  <i class='fas fa-print'></i>
                                </a>
                            </div>
                        </td>
                      </tr>";
            }
        } else {
        ?>
            <tr>
                <td colspan="6" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h5>No SOPs Found</h5>
                        <p>There are no Standard Operating Procedures available at this time.</p>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
            </tbody>
            </table>
        </div>
    </div>
</div>

  <!-- ✅ Clean View Modal -->
  <div class="modal fade" id="viewSopModal" tabindex="-1" aria-labelledby="viewSopLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="viewSopLabel">SOP Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-borderless mb-0">
              <tbody>
                <tr>
                  <th class="w-25">Title:</th>
                  <td><span id="sopTitle"></span></td>
                </tr>
                <tr>
                  <th>Category:</th>
                  <td><span id="sopCategory"></span></td>
                </tr>
                <tr>
                  <th>Status:</th>
                  <td><span id="sopStatus" class="badge bg-secondary"></span></td>
                </tr>
                <tr>
                  <th>Content:</th>
                  <td>
                    <div class="border rounded p-3 bg-light" id="sopContent"></div>
                  </td>
                </tr>
                <tr>
                  <th>Attached File:</th>
                  <td><a href="" target="_blank" id="sopFileLink">No file uploaded</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- ✅ Clean Update Modal -->
  <div class="modal fade" id="updateSopModal" tabindex="-1" aria-labelledby="updateSopLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <form action="update_sop.php" method="POST" class="modal-content" enctype="multipart/form-data">
        
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="updateSopLabel">Update SOP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="sop_id" id="updateSopId">

          <!-- Row 1: Title + Category -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Title</label>
              <input type="text" name="title" class="form-control" id="updateSopTitle" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Category</label>
              <input type="text" name="category" class="form-control" id="updateSopCategory" required>
            </div>
          </div>

          <!-- Row 2: Status + File -->
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label fw-bold">Status</label>
              <select name="status" class="form-select" id="updateSopStatus" required>
                <option value="Active">Active</option>
                <option value="Draft">Draft</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Upload File (optional)</label>
              <input type="file" name="file" class="form-control">
            </div>
          </div>

          <!-- Row 3: Content -->
          <div class="mt-3">
            <label class="form-label fw-bold">Content</label>
            <textarea name="content" class="form-control" id="updateSopContent" rows="6" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_sop" class="btn btn-warning">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>


  <!-- Archive Modal -->
  <div class="modal fade" id="archiveSopModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title">Archive SOP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to archive <strong id="archiveSopTitle"></strong>?</p>
        </div>
        <div class="modal-footer">
          <form method="GET" action="view_sop.php">
            <input type="hidden" name="archive_id" id="archiveSopId">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Archive</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    // View Button
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

    // Update Button
    document.querySelectorAll(".updateBtn").forEach(btn => {
      btn.addEventListener("click", function () {
        document.getElementById("updateSopId").value = this.dataset.id;
        document.getElementById("updateSopTitle").value = this.dataset.title;
        document.getElementById("updateSopCategory").value = this.dataset.category;
        document.getElementById("updateSopStatus").value = this.dataset.status;
        document.getElementById("updateSopContent").value = this.dataset.content;
      });
    });

    // Archive Button
    document.querySelectorAll(".archiveBtn").forEach(btn => {
      btn.addEventListener("click", function () {
        document.getElementById("archiveSopId").value = this.dataset.id;
        document.getElementById("archiveSopTitle").textContent = this.dataset.title;
      });
    });
  });
  </script>

  <?php include('footer.php'); ?>
