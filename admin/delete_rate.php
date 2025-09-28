<?php
// Enable errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('../connect.php');
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate_id'])) {
    $rate_id = intval($_POST['rate_id']);

    $stmt = $conn->prepare("DELETE FROM freight_rates WHERE rate_id = ?");
    $stmt->bind_param("i", $rate_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: rates_management.php?success=deleted");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Error deleting rate.'); window.location='rates_management.php';</script>";
        exit();
    }
} else {
    // No rate_id provided
    $conn->close();
    echo "<script>alert('No rate ID specified.'); window.location='rates_management.php';</script>";
    exit();
}
