<?php

$url = "https://switchapidev.centenarybank.co.ug/api/token/";

// User credentials
$username = "sunsys"; 
$password = "C0mpl3x@Sun="; 

// Sample request data
$data = array(
    "username" => $username,
    "password" => $password
);

// Convert data to JSON format
$json_data = json_encode($data);

// Create a stream context for the POST request
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                    "Content-Length: " . strlen($json_data) . "\r\n",
        'content' => $json_data,
        'ignore_errors' => true, // To capture HTTP errors
    )
);

// Create context
$context = stream_context_create($options);

// Make the request
$response = file_get_contents($url, false, $context);

// Check for errors
if ($response === false) {
    echo 'Error: Failed to connect to the API. Check server status or network.';
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
?>