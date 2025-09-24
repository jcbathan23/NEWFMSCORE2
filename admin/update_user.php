<?php
include('../connect.php');

if(isset($_POST['update_user'])){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $account_type = $conn->real_escape_string($_POST['account_type']);
    $company_name = $conn->real_escape_string($_POST['company_name'] ?? '');
    $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? '');
    $contact_number = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $services = $conn->real_escape_string($_POST['services'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? 'Active');

    // Remove from all tables
    foreach(['newaccounts','admin_list','pending_service_provider','active_service_provider'] as $tbl){
        $conn->query("DELETE FROM $tbl WHERE email='$email'");
    }

    if($account_type === 'Service Provider'){
        // Add to active_service_provider by default
        $conn->query("INSERT INTO active_service_provider (email,password,company_name,contact_person,contact_number,services,status) VALUES ('$email','$password','$company_name','$contact_person','$contact_number','$services','$status')");
    } elseif($account_type === 'Admin'){
        $conn->query("INSERT INTO admin_list (email,password) VALUES ('$email','$password')");
    } else {
        $conn->query("INSERT INTO newaccounts (email,password) VALUES ('$email','$password')");
    }

    header("Location: user_management.php?success=updated");
    exit;
}
?>
