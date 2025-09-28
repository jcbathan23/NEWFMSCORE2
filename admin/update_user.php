<?php
include('../connect.php');

if(isset($_POST['update_user']) || (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['account_type']))){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $account_type = $conn->real_escape_string($_POST['account_type']);
    $company_name = $conn->real_escape_string($_POST['company_name'] ?? '');
    $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? '');
    $contact_number = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $services = $conn->real_escape_string($_POST['services'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? 'Active');

    // Basic validation
    if(empty($email) || empty($password) || empty($account_type)) {
        header("Location: profile.php?error=missing_fields");
        exit;
    }

    // Additional validation for Service Providers
    if($account_type === 'Service Provider' && (empty($company_name) || empty($contact_person))) {
        header("Location: profile.php?error=missing_sp_fields");
        exit;
    }

    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Remove from all tables first
        $tables = ['newaccounts', 'admin_list', 'pending_service_provider', 'active_service_provider'];
        foreach($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
        }

        // Insert into appropriate table based on account type
        if($account_type === 'Service Provider'){
            // Add to active_service_provider with all required fields
            // Note: company_name is required, so we need to provide a default if empty
            $company_name_final = !empty($company_name) ? $company_name : 'Default Company';
            $stmt = $conn->prepare("INSERT INTO active_service_provider (company_name, email, password, account_type, contact_person, contact_number, services, status) VALUES (?, ?, ?, 3, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $company_name_final, $email, $password, $contact_person, $contact_number, $services, $status);
            
        } elseif($account_type === 'Admin'){
            // Add to admin_list
            $stmt = $conn->prepare("INSERT INTO admin_list (email, password, account_type) VALUES (?, ?, 1)");
            $stmt->bind_param("ss", $email, $password);
            
        } else {
            // Add to newaccounts (regular users)
            $stmt = $conn->prepare("INSERT INTO newaccounts (email, password, account_type) VALUES (?, ?, 2)");
            $stmt->bind_param("ss", $email, $password);
        }
        
        if($stmt->execute()) {
            $conn->commit();
            header("Location: profile.php?success=updated");
        } else {
            $conn->rollback();
            error_log("SQL Error: " . $stmt->error);
            error_log("Account Type: " . $account_type);
            error_log("Email: " . $email);
            header("Location: profile.php?error=update_failed&debug=sql_error");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Update user error: " . $e->getMessage());
        header("Location: profile.php?error=update_failed");
    }
    
    exit;
} else {
    // Redirect if accessed directly
    header("Location: profile.php");
    exit;
}
?>
