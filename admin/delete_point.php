<?php
require '../connect.php';
require_once __DIR__ . '/auth.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table'];
    $id = intval($_POST['id']);

    if($table && $id) {
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE point_id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()) {
            header("Location: network_manage.php?success=point_deleted");
            exit;
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}
?>
