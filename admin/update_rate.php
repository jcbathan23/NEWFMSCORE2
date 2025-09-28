<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rate_id = intval($_POST['rate_id']);
    $mode = trim($_POST['mode']);
    $distance_range = trim($_POST['distance_range']);
    $weight_range = trim($_POST['weight_range']);
    $rate = floatval($_POST['rate']);
    $unit = trim($_POST['unit']);
    $status = trim($_POST['status']); // ✅ Get status from form

    $sql = "UPDATE freight_rates 
            SET mode=?, distance_range=?, weight_range=?, rate=?, unit=?, status=?
            WHERE rate_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssi", $mode, $distance_range, $weight_range, $rate, $unit, $status, $rate_id);

    if ($stmt->execute()) {
        echo "<script>alert('Rate updated successfully!'); window.location.href='rates_management.php';</script>";
    } else {
        echo "<script>alert('Error updating rate.'); window.location.href='rates_management.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
