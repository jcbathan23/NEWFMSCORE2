<?php
session_start();
include("../connect.php");
require 'functions.php'; // ✅ must include addNotification($conn, $message, $type, $link)

// ✅ Only allow logged-in users
if (!isset($_SESSION['email'])) {
    header("Location: ../admin/loginpage.php");
    exit();
}

// ✅ Get action and rate_id from URL
if (isset($_GET['action']) && isset($_GET['rate_id'])) {
    $action = $_GET['action'];
    $rate_id = intval($_GET['rate_id']);

    // 🔹 Check if the rate exists
    $stmt = $conn->prepare("SELECT status FROM freight_rates WHERE rate_id = ?");
    $stmt->bind_param("i", $rate_id);
    $stmt->execute();
    $stmt->bind_result($currentStatus);
    if (!$stmt->fetch()) {
        $stmt->close();
        die("❌ Rate not found.");
    }
    $stmt->close();

    // 🔹 Only update if still Pending
    if ($currentStatus == "Pending") {
        if ($action == "accept") {
            $newStatus = "Accepted";
        } elseif ($action == "reject") {
            $newStatus = "Rejected";
        } else {
            die("❌ Invalid action.");
        }

        // ✅ Update status
        $stmt = $conn->prepare("UPDATE freight_rates SET status = ? WHERE rate_id = ?");
        $stmt->bind_param("si", $newStatus, $rate_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ Rate successfully $newStatus.";

            // ✅ Notify only admin
            addNotification(
                $conn,
                "Freight rate #$rate_id has been $newStatus.",
                "admin",
                "rates_management.php"
            );

        } else {
            $_SESSION['message'] = "❌ Failed to update rate.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "⚠️ This rate has already been processed.";
    }
} else {
    $_SESSION['message'] = "❌ Invalid request.";
}

// 🔹 Redirect back to rates page
header("Location: provider_rates.php");
exit();
?>
