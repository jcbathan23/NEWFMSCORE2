<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';

if (isset($_POST['update_sop'])) {
    $sop_id  = intval($_POST['sop_id']);
    $title   = trim($_POST['title']);
    $category = trim($_POST['category']);
    $status  = $_POST['status'];
    $content = trim($_POST['content']);

    // Handle file upload if a new file is selected
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $uploadDir = "../uploads/";
        $filename = basename($_FILES['file']['name']);
        $targetFile = $uploadDir . time() . "_" . $filename; // timestamp to avoid conflicts

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $file_path = time() . "_" . $filename;
        } else {
            echo "<script>alert('Error uploading file.'); window.location='view_sop.php';</script>";
            exit;
        }
    }

    // Prepare SQL
    if ($file_path) {
        $sql = "UPDATE sop_documents 
                SET title=?, category=?, status=?, content=?, file_path=? 
                WHERE sop_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $title, $category, $status, $content, $file_path, $sop_id);
    } else {
        $sql = "UPDATE sop_documents 
                SET title=?, category=?, status=?, content=? 
                WHERE sop_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $category, $status, $content, $sop_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('SOP updated successfully!'); window.location='view_sop.php';</script>";
    } else {
        echo "<script>alert('Error updating SOP.'); window.location='view_sop.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
