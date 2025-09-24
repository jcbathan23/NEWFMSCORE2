<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../connect.php'; // Adjust path if needed
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Grab POST values and sanitize
    $originId = isset($_POST['origin_id']) ? (int)$_POST['origin_id'] : 0;
    $destinationId = isset($_POST['destination_id']) ? (int)$_POST['destination_id'] : 0;
    $carrierType = isset($_POST['carrier_type']) ? trim($_POST['carrier_type']) : '';
    $providerId = isset($_POST['provider_id']) ? (int)$_POST['provider_id'] : 0;
    $distance = isset($_POST['distance']) ? (float)$_POST['distance'] : 0;
    $eta = isset($_POST['eta']) ? (int)$_POST['eta'] : 0;

    // Validate required fields
    if (!$originId || !$destinationId || !$carrierType || !$providerId || !$distance || !$eta) {
        die("All fields are required.");
    }

    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO routes 
        (origin_id, destination_id, carrier_type, provider_id, distance_km, eta_min)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters with correct types: i=int, s=string, d=double
    $stmt->bind_param("iisidi", $originId, $destinationId, $carrierType, $providerId, $distance, $eta);

    // Execute statement
    if ($stmt->execute()) {
         header("Location: route_planner.php?success=route_saved"); // redirect back
    } else {
        echo "Error saving route: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    die("Invalid request method.");
}
?>
