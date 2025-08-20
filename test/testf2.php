
<?php
class ForexAPIClient {
    private $baseUrl;
    private $accessToken;
    private $refreshToken;
    
    public function __construct($baseUrl) {
        $this->baseUrl = $baseUrl;
        $this->accessToken = null;
        $this->refreshToken = null;
    }
    
    // Module 1: Get tokens (access and refresh)
    public function getTokens($username, $password) {
        $url = $this->baseUrl . '/token/';
        
        $data = [
            'username' => $username,
            'password' => $password
        ];
        
        $response = $this->makeRequest($url, $data);
        
        if (isset($response['refresh']) && isset($response['access'])) {
            $this->refreshToken = $response['refresh'];
            $this->accessToken = $response['access'];
            return $response;
        } else {
            throw new Exception($response['detail'] ?? 'Unknown error occurred');
        }
    }
    
    // Module 2: Get forex rates
    public function getForexRates($auxNo, $respType) {
        if (!$this->accessToken) {
            throw new Exception('Access token is required. Please authenticate first.');
        }
        
        $url = $this->baseUrl . '/forex_rates';
        
        $data = [
            'AUX_NO' => $auxNo,
            'RESP_TYPE' => $respType
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken
        ];
        
        $response = $this->makeRequest($url, $data, $headers);
        return $response;
    }
    
    // Helper method to make HTTP requests
    private function makeRequest($url, $data, $headers = []) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/json'
        ], $headers));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            throw new Exception('CURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception($decodedResponse['detail'] ?? 'Request failed with HTTP code ' . $httpCode);
        }
        
        return $decodedResponse;
    }
}

// Command-line interface functions
function displaySeparator() {
    echo "========================================\n";
}

function prompt($message) {
    echo $message;
    return trim(fgets(STDIN));
}

function displayForexRates($rates) {
    if (isset($rates['FOREX_RATES']) && is_array($rates['FOREX_RATES'])) {
        echo "FOREX RATES:\n";
        displaySeparator();
        
        foreach ($rates['FOREX_RATES'] as $rate) {
            printf("Currency: %s (%s)\n", $rate['DESCRIPTION'], $rate['SHORT_DESCR']);
            printf("Buy Rate: %s\n", $rate['BUY_RATE']);
            printf("Sell Rate: %s\n", $rate['SELL_RATE']);
            displaySeparator();
        }
        
        if (isset($rates['RESPONSE_DETAILS'])) {
            echo "RESPONSE DETAILS:\n";
            printf("Reference: %s\n", $rates['RESPONSE_DETAILS']['RETREF_NO']);
            printf("Code: %s\n", $rates['RESPONSE_DETAILS']['RESP_CODE']);
            printf("Description: %s\n", $rates['RESPONSE_DETAILS']['RESP_DESC']);
            displaySeparator();
        }
    } else {
        echo "No forex rates data found in response.\n";
    }
}

// Main execution
function main() {
    $baseUrl = 'https://switchapidev.centenarybank.co.ug';
    $client = new ForexAPIClient($baseUrl);
    
    echo "Forex API Client\n";
    displaySeparator();
    
    // Module 1: Get tokens
    echo "MODULE 1: GET TOKENS\n";
    displaySeparator();
    
    // Hardcoded credentials as requested
    $username = "testuser";
    $password = "testpass123";
    
    echo "Using predefined credentials\n";
    
    try {
        $tokens = $client->getTokens($username, $password);
        echo "Authentication successful!\n";
        echo "Access Token: " . substr($tokens['access'], 0, 20) . "...\n";
        echo "Refresh Token: " . substr($tokens['refresh'], 0, 20) . "...\n";
        
        // Prompt to continue to Module 2
        echo "\n";
        displaySeparator();
        $continue = prompt("Press Enter to continue to Module 2...");
        
        // Module 2: Get forex rates
        echo "\n";
        displaySeparator();
        echo "MODULE 2: FOREX RATES\n";
        displaySeparator();
        
        $auxNo = prompt("Enter Transaction ID (AUX_NO): ");
        $respType = prompt("Enter Response Type (json/xml/html): ");
        
        $forexRates = $client->getForexRates($auxNo, $respType);
        displayForexRates($forexRates);
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Run the application
if (php_sapi_name() === 'cli') {
    main();
} else {
    echo "This application is designed to run in the command line.\n";
}
?>