<?php
include('../connect.php');

// Test database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful!<br>";

// Test table structures
$tables = ['newaccounts', 'admin_list', 'active_service_provider', 'pending_service_provider'];

foreach($tables as $table) {
    echo "<h3>Table: $table</h3>";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "Error describing table: " . $conn->error . "<br>";
    }
}

// Test a simple update operation
echo "<h3>Testing Update Operation</h3>";

// Test data
$test_email = "test@example.com";
$test_password = "testpass";
$test_account_type = "User";

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Remove from all tables first
    foreach($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE email = ?");
        $stmt->bind_param("s", $test_email);
        $stmt->execute();
        echo "Deleted from $table: " . $stmt->affected_rows . " rows<br>";
        $stmt->close();
    }
    
    // Insert into newaccounts
    $stmt = $conn->prepare("INSERT INTO newaccounts (email, password, account_type) VALUES (?, ?, 2)");
    $stmt->bind_param("ss", $test_email, $test_password);
    
    if($stmt->execute()) {
        echo "Successfully inserted test user into newaccounts<br>";
        $conn->commit();
    } else {
        echo "Error inserting: " . $stmt->error . "<br>";
        $conn->rollback();
    }
    
    $stmt->close();
    
    // Clean up test data
    $stmt = $conn->prepare("DELETE FROM newaccounts WHERE email = ?");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    echo "Cleaned up test data<br>";
    $stmt->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo "Exception: " . $e->getMessage() . "<br>";
}

echo "<h3>Test Complete</h3>";
?>
