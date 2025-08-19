<?php

define('BASE_URL', 'https://switchapidev.centenarybank.co.ug'); 
define('TOKEN_ENDPOINT', BASE_URL . '/api/token/');

// User credentials (should be securely stored in production)
$username = 'sunsys'; 
$password = 'C0mpl3x@Sun='; 

// Function to get tokens
function getTokens($username, $password) {
    // Prepare request data
    $requestData = [
        'username' => $username,
        'password' => $password
    ];
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, TOKEN_ENDPOINT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Handle response
    if ($error) {
        return ['success' => false, 'message' => 'cURL Error: ' . $error];
    }
    
    $responseData = json_decode($response, true);
    
    if ($httpCode !== 200) {
        $errorMsg = isset($responseData['detail']) ? $responseData['detail'] : 'Authentication failed';
        return ['success' => false, 'message' => $errorMsg];
    }
    
    // Return tokens on success
    return [
        'success' => true,
        'refresh_token' => $responseData['refresh'],
        'access_token' => $responseData['access']
    ];
}

// Execute token request
$result = getTokens($username, $password);

// Output result
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>