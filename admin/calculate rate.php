<?php
include('../connect.php');

header('Content-Type: application/json');

if (isset($_POST['route_id'], $_POST['provider_id'])) {
    $route_id = intval($_POST['route_id']);
    $provider_id = intval($_POST['provider_id']);

    $stmt = $conn->prepare("
        SELECT rate 
        FROM freight_rates 
        WHERE provider_id = ? AND route_id = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->bind_param("ii", $provider_id, $route_id);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
        exit;
    }

    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        echo json_encode(['success' => true, 'rate' => number_format($result['rate'], 2)]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No rate found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
}
?>
