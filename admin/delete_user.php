<?php
include('../connect.php');

if(isset($_POST['delete_user']) || isset($_POST['email'])){
    $email = $conn->real_escape_string($_POST['email']);
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Delete from all possible tables where the user might exist
        $tables = ['newaccounts', 'admin_list', 'pending_service_provider', 'active_service_provider'];
        $deleted = false;
        
        foreach($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE email = ?");
            $stmt->bind_param("s", $email);
            if($stmt->execute() && $stmt->affected_rows > 0) {
                $deleted = true;
            }
            $stmt->close();
        }
        
        if($deleted) {
            // Commit the transaction
            $conn->commit();
            header("Location: profile.php?success=deleted");
        } else {
            // Rollback if no user was found
            $conn->rollback();
            header("Location: profile.php?error=user_not_found");
        }
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        error_log("Delete user error: " . $e->getMessage());
        error_log("Email: " . $email);
        header("Location: profile.php?error=delete_failed");
    }
    
    exit;
} else {
    // Redirect if accessed directly
    header("Location: profile.php");
    exit;
}
?>
