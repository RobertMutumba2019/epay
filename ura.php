<?php
// Replace with the actual URA test currency API endpoint when available
$apiUrl = "https://ura.go.ug/test-currency-api/endpoint";

// If the API requires an API key or token (update as needed)
// Leave empty or remove the header if no authentication is required
$apiKey = ""; // e.g. "YOUR_API_KEY_HERE"

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Set to false only in dev/testing
$headers = ["Accept: application/json"];
if (!empty($apiKey)) {
    $headers[] = "Authorization: Bearer $apiKey";
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Request Error: " . curl_error($ch);
} else {
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpStatus === 200) {
        $data = json_decode($response, true);
        echo "<h2>Currency API Response:</h2><pre>";
        print_r($data);
        echo "</pre>";
    } else {
        echo "API returned HTTP status $httpStatus. Response:";
        echo "<pre>$response</pre>";
    }
}

curl_close($ch);
