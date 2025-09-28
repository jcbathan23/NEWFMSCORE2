<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');

$rate_id = isset($_GET['rate_id']) ? intval($_GET['rate_id']) : 0;
if($rate_id>0){
    $stmt = $conn->prepare("SELECT total_rate FROM calculated_rates WHERE id=? LIMIT 1");
    $stmt->bind_param("i",$rate_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    echo json_encode(['total_rate'=>$res['total_rate'] ?? null]);
}else{
    echo json_encode(['total_rate'=>null]);
}
