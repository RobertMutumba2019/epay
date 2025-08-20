<?php
$baseURL = "https://switchapidev.centenarybank.co.ug/api/token"; 
$url = $baseURL . "/token/";
$username = "sunsys"; 
$password = "C0mpl3x@Sun="; 

$data = [
    "username" => $username,
    "password" => $password
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Status: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
}

curl_close($ch);
