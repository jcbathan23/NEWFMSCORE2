<?php
/**
 * Import Provider from Logistic1 to Local Database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get the provider data from POST body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['provider'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Provider data is required']);
    exit;
}

$provider = $input['provider'];

try {
    // Prepare the insert statement for pending_service_provider table
    $stmt = $conn->prepare("
        INSERT INTO pending_service_provider 
        (company_name, contact_person, email, contact_number, address, services, external_id, website, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    // Extract and clean the data
    $company_name = $provider['name'] ?? $provider['supplier_name'] ?? 'Unknown Company';
    $contact_person = $provider['contact'] ?? $provider['contact_person'] ?? '';
    $email = $provider['email'] ?? '';
    $contact_number = $provider['phone'] ?? '';
    $address = $provider['hub_location'] ?? '';
    $services = $provider['service_capabilities'] ?? $provider['service_areas'] ?? '';
    $external_id = 'LOG1_' . ($provider['id'] ?? uniqid());
    $website = $provider['website'] ?? '';
    
    // Execute the statement
    $stmt->bind_param("ssssssss", 
        $company_name, 
        $contact_person, 
        $email, 
        $contact_number, 
        $address, 
        $services, 
        $external_id, 
        $website
    );
    
    if ($stmt->execute()) {
        $new_id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Provider imported successfully',
            'provider_id' => $new_id,
            'company_name' => $company_name
        ]);
    } else {
        throw new Exception('Failed to insert provider: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>
