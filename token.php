<?php
// API endpoint (change to DEV, UAT, or PROD URL)
$apiUrl =  "https://switchapidev.centenarybank.co.ug"; 

// If the API requires a token, put it here
$token = "YOUR_ACCESS_TOKEN_HERE"; // leave empty if not needed

// Initialize cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable SSL check (only for testing)
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token" // remove if no auth needed
]);

// Execute request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Decode JSON response
    $data = json_decode($response, true);
    
    // Print the data
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

// Close cURL
curl_close($ch);
?>
