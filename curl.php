<?php


// API endpoint for getting tokens
$url = "https://switchapidev.centenarybank.co.ug/api/token/";

// User credentials
$username = "sunsys"; 
$password = "C0mpl3x@Sun="; 

// Sample request data [cite: 12]
$data = array(
    "username" => $username,
    "password" => $password
);

// Convert data to JSON format
$json_data = json_encode($data);

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data))
);

// Execute the cURL request
$response = curl_exec($curl);

// Check for cURL errors
if (curl_errno($curl)) {
    echo 'cURL Error: ' . curl_error($curl);
} else {
    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Handle the response
    if (isset($responseData['access']) && isset($responseData['refresh'])) {
        // Successful response 
        $accessToken = $responseData['access'];
        $refreshToken = $responseData['refresh'];
        echo "Access Token: " . $accessToken . "\n";
        echo "Refresh Token: " . $refreshToken . "\n";
        echo "Successfully retrieved tokens.";
    } elseif (isset($responseData['detail'])) {
        // Failure response 
        $errorDetail = $responseData['detail'];
        echo "Error: " . $errorDetail;
    } else {
        // Unexpected response format
        echo "Unexpected response from the API:\n" . $response;
    }
}

// Close the cURL session
curl_close($curl);
?>