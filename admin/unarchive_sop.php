<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';

if (isset($_POST['sop_id'])) {
    $sop_id = intval($_POST['sop_id']);

    // ✅ Set status back to Active
    $sql = "UPDATE sop_documents SET status='Active' WHERE sop_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sop_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('SOP unarchived successfully.');
                window.location='archived_sop.php';
              </script>";
    } else {
        echo "<script>
                alert('Error unarchiving SOP.');
                window.location='archived_sop.php';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
