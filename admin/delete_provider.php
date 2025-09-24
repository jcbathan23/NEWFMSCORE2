<?php
require '../connect.php';
require_once __DIR__ . '/auth.php';

if (isset($_POST['delete_provider'])) {
    $id = intval($_POST['provider_id']);

    $stmt = $conn->prepare("DELETE FROM active_service_provider WHERE provider_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: active_providers.php?success=deleted");
    } else {
        echo "Error deleting provider: " . $conn->error;
    }
}
?>
