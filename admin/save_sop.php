<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';
require 'functions.php';

if (isset($_POST['save_sop'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content = trim($_POST['content']);
    $status = trim($_POST['status']);

    $file_path = NULL;
    if (!empty($_FILES['sop_file']['name'])) {
        $targetDir = "../uploads/sop/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["sop_file"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["sop_file"]["tmp_name"], $targetFilePath)) {
            $file_path = "uploads/sop/" . $fileName;
        }
    }

    $sql = "INSERT INTO sop_documents (title, category, content, file_path, status, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $title, $category, $content, $file_path, $status);

    if ($stmt->execute()) {
        // Add notification for new SOP
        addNotification($conn, "New SOP document created: '$title' ($category)", 'success', 'view_sop.php');
        echo "<script>alert('SOP Created Successfully!'); window.location='create_sop.php';</script>";
    } else {
        echo "<script>alert('Error saving SOP.'); window.location='create_sop.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
