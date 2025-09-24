<?php
include('../connect.php');
require_once __DIR__ . '/auth.php';

if(isset($_POST['delete_user'])){
    $email = $conn->real_escape_string($_POST['email']);
    foreach(['newaccounts','admin_list','pending_service_provider','active_service_provider'] as $tbl){
        $conn->query("DELETE FROM $tbl WHERE email='$email'");
    }
    header("Location: user_management.php?success=deleted");
    exit;
}
?>
