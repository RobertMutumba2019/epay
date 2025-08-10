<?php

// API credentials and URL
$dev_url = "https://switchapidev.centenarybank.co.ug";
$token_endpoint = "/token/";
$username = "name"; // Replace with your actual username
$password = "ter@2025"; // Replace with your actual password

// Prepare the request data
$data = json_encode([
    "username" => $username,
    "password" => $password
]);

// Initialize cURL
$ch = curl_init($dev_url . $token_endpoint);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);

// Execute the request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL resource
curl_close($ch);

// Handle the response
if ($http_code == 200) {
    $tokens = json_decode($response, true);
    $access_token = $tokens['access'];
    $refresh_token = $tokens['refresh'];
    echo "Successfully retrieved tokens.\n";
    echo "Access Token: " . $access_token . "\n";
    echo "Refresh Token: " . $refresh_token . "\n";
} else {
    $error_response = json_decode($response, true);
    echo "Failed to retrieve tokens. HTTP Status Code: " . $http_code . "\n";
    echo "Error Detail: " . ($error_response['detail'] ?? "Unknown error") . "\n";
    exit;
}

?>