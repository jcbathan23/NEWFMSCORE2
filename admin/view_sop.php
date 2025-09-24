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

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

  <div class="content">
    <h3 class="mb-4">SOP MANAGEMENT</h3>

    <div class="mb-3">
      <a href="create_sop.php" class="btn btn-success">+ Add SOP</a>
      <a href="archived_sop.php" class="btn btn-secondary">View Archived SOPs</a>
    </div>

    <table class="table table-hover table-striped">
      <thead class="table-secondary">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch only Active or Draft SOPs
        $result = $conn->query("SELECT * FROM sop_documents WHERE status IN ('Active','Draft') ORDER BY created_at DESC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['sop_id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['category']}</td>
                        <td><span class='badge bg-" . ($row['status'] == 'Active' ? 'success' : 'secondary') . "'>{$row['status']}</span></td>
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

                          <!-- Update Button -->
                          <button class='btn btn-warning btn-sm updateBtn'
                            data-id='{$row['sop_id']}'
                            data-title='{$row['title']}'
                            data-category='{$row['category']}'
                            data-status='{$row['status']}'
                            data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'
                            data-file='{$row['file_path']}'
                            data-bs-toggle='modal' data-bs-target='#updateSopModal'>
                            <i class='fas fa-edit'></i>
                          </button>

                          <!-- Archive Button (opens modal) -->
                          <button class='btn btn-secondary btn-sm archiveBtn'
                            data-id='{$row['sop_id']}'
                            data-title='{$row['title']}'
                            data-bs-toggle='modal' data-bs-target='#archiveSopModal'>
                            <i class='fas fa-archive'></i>
                          </button>

                          <!-- Print Button -->
                          <a href='print_sop.php?id={$row['sop_id']}' target='_blank' class='btn btn-dark btn-sm'>
                            <i class='fas fa-print'></i>
                          </a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No SOPs found</td></tr>";
        }
        ?>
      </tbody>
    </table>
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
