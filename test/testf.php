<?php

$BASE_URL = "https://switchapiuat.centenarybank.co.ug";

// Module 1: Get Tokens (Access & Refresh)
function getTokens($username, $password, $baseUrl) {
    $url = $baseUrl . "/api/token/";
    $data = array(
        "username" => $username,
        "password" => $password
    );
    $data_string = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        echo "Tokens received successfully.\n";
        return $responseData;
    } else {
        $errorData = json_decode($response, true);
        echo "Failed to get tokens. Status code: " . $httpCode . "\n";
        echo "Error: " . ($errorData['detail'] ?? 'Unknown error') . "\n";
        return false;
    }
}


$username = "sunsys";
$password = "C0mpl3x@Sun=";

$tokens = getTokens($username, $password, $BASE_URL);

if ($tokens) {
    $accessToken = $tokens['access'];
    echo "Access Token: " . $accessToken . "\n";

 
    echo "Get Forex Rates\n";
  

    // Module 2: Get Forex Rates
    function getForexRates($accessToken, $baseUrl) {
        $url = $baseUrl . "/api/forex_rates";
        $auxNo = "XSDFFGGLFKNJ"; 
        $respType = "json"; 
        
        $data = array(
            "AUX_NO" => $auxNo,
            "RESP_TYPE" => $respType
        );
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Bearer ' . $accessToken
        ));
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            echo "Forex rates retrieved successfully.\n";
            print_r($responseData);
            return $responseData;
        } else {
            echo "Failed to retrieve forex rates. Status code: " . $httpCode . "\n";
            echo "Response: " . $response . "\n";
            return false;
        }
    }
    
    getForexRates($accessToken, $BASE_URL);

} else {
    echo "Could not proceed to Module 2 because token retrieval failed.\n";
}

?>