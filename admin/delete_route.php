<?php
include('../connect.php'); // adjust path if needed
require_once __DIR__ . '/auth.php';

if (isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']); // force integer for security

    // allowlist table names to prevent SQL injection
    $allowed_tables = ['routes']; 

    if (in_array($table, $allowed_tables)) {
        $sql = "DELETE FROM $table WHERE route_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: manage_routes.php?success=deleted");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Invalid table.";
    }
} else {
    echo "Invalid request.";
}
?>
