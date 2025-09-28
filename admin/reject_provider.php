<?php
require '../connect.php';
require 'functions.php'; // Make sure addNotification() is available
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id'])) {
    $id = intval($_POST['registration_id']);

    // Optional: fetch provider name before deleting for notification
    $stmtFetch = $conn->prepare("SELECT company_name FROM pending_service_provider WHERE registration_id = ?");
    $stmtFetch->bind_param("i", $id);
    $stmtFetch->execute();
    $result = $stmtFetch->get_result();
    $providerName = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['company_name'] : 'Unknown Provider';

    // Delete provider
    $stmt = $conn->prepare("DELETE FROM pending_service_provider WHERE registration_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ✅ Add notification
        addNotification(
            $conn,
            "Service Provider rejected: " . $providerName,
            "service_provider",
            "pending_providers.php"
        );

        header("Location: pending_providers.php?success=rejected_provider"); // redirect back
        exit();
    } else {
        echo "Error deleting provider.";
    }
} else {
    echo "Invalid request.";
}
?>
