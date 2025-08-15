<?php
// T119 Taxpayer Information Query System
class EfrisT119TaxpayerQuery {
    // API Configuration
    private $apiUrl = "https://efris-api.ug/api/v1/taxpayer/info";
    private $appId = "AP04";
    private $interfaceCode = "T119";
    private $taxpayerID = "1000000000"; // Example TIN
    
    // Query taxpayer information
    public function getTaxpayerInfo($tin = null, $ninBrn = null) {
        // Validate input
        if (empty($tin) && empty($ninBrn)) {
            return ['success' => false, 'error' => 'Either TIN or NIN/BRN is required'];
        }
        
        // Build request payload
        $request = [];
        if (!empty($tin)) {
            $request['tin'] = $tin;
        }
        if (!empty($ninBrn)) {
            $request['ninBrn'] = $ninBrn;
        }
        
        // Send to EFRIS API (simulated)
        $response = $this->sendApiRequest($request);
        
        return $response;
    }
    
    // Simulate API request
    private function sendApiRequest($request) {
        // Simulate API processing delay
        sleep(1);
        
        // For demo purposes, return sample data if TIN matches example
        if (isset($request['tin']) && $request['tin'] === "7777777777") {
            return [
                'success' => true,
                'data' => $this->getSampleResponse()
            ];
        } elseif (isset($request['ninBrn']) && $request['ninBrn'] === "7777777777") {
            return [
                'success' => true,
                'data' => $this->getSampleResponse()
            ];
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => '404',
                    'message' => 'Taxpayer not found'
                ]
            ];
        }
    }
    
    // Sample response data based on documentation
    private function getSampleResponse() {
        return [
            "taxpayer" => [
                "tin" => "123456",
                "ninBrn" => "2222",
                "legalName" => "admin",
                "businessName" => "1",
                "contactNumber" => "18888888888",
                "contactEmail" => "123@qq.com",
                "address" => "beijing",
                "taxpayerType" => "201",
                "governmentTIN" => "1"
            ]
        ];
    }
}

// Handle form submission
$query = new EfrisT119TaxpayerQuery();
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tin = !empty($_POST['tin']) ? $_POST['tin'] : null;
    $ninBrn = !empty($_POST['ninBrn']) ? $_POST['ninBrn'] : null;
    $result = $query->getTaxpayerInfo($tin, $ninBrn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFRIS T119 Taxpayer Information Query</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        h1 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
        }
        .taxpayer-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #e1e8ed;
            padding-bottom: 10px;
        }
        .info-label {
            flex: 1;
            font-weight: 600;
            color: #34495e;
        }
        .info-value {
            flex: 2;
        }
        .info-box {
            background-color: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .search-options {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .search-option {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .search-option h3 {
            margin-top: 0;
            color: #3498db;
        }
        .search-option p {
            margin-bottom: 15px;
            color: #7f8c8d;
        }
        .highlight {
            background-color: #fff9c4;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: 600;
        }
        .taxpayer-card {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .taxpayer-card h2 {
            margin-top: 0;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .taxpayer-card .info-row {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .taxpayer-card .info-label {
            color: rgba(255,255,255,0.8);
        }
        .taxpayer-card .info-value {
            color: white;
        }
        .tax-type {
            display: inline-block;
            background-color: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>EFRIS T119 Taxpayer Information Query</h1>
        
        <div class="info-box">
            <strong>Interface Code T119:</strong> Query taxpayer information by TIN or NIN/BRN.
            This interface retrieves detailed taxpayer registration information from the EFRIS system.
        </div>
        
        <div class="search-options">
            <div class="search-option">
                <h3>Search by TIN</h3>
                <p>Enter the Taxpayer Identification Number to retrieve taxpayer information.</p>
                <div class="form-group">
                    <label for="tin">Taxpayer Identification Number (TIN)</label>
                    <input type="text" id="tin" name="tin" placeholder="Enter TIN">
                </div>
            </div>
            
            <div class="search-option">
                <h3>Search by NIN/BRN</h3>
                <p>Enter the National Identification Number or Business Registration Number.</p>
                <div class="form-group">
                    <label for="ninBrn">NIN / BRN</label>
                    <input type="text" id="ninBrn" name="ninBrn" placeholder="Enter NIN or BRN">
                </div>
            </div>
        </div>
        
        <form method="post">
            <button type="submit">Query Taxpayer Information</button>
        </form>
        
        <?php if ($result): ?>
        <div class="result">
            <?php if ($result['success']): ?>
                <div class="success">
                    <h3>Taxpayer Information Retrieved Successfully</h3>
                    
                    <div class="taxpayer-card">
                        <h2><?= $result['data']['taxpayer']['legalName'] ?></h2>
                        <div class="info-row">
                            <div class="info-label">TIN:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['tin'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NIN/BRN:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['ninBrn'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Business Name:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['businessName'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contact Number:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['contactNumber'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email Address:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['contactEmail'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Address:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['address'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Taxpayer Type:</div>
                            <div class="info-value">
                                <?= $result['data']['taxpayer']['taxpayerType'] ?>
                                <span class="tax-type">Type <?= $result['data']['taxpayer']['taxpayerType'] ?></span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Government TIN:</div>
                            <div class="info-value"><?= $result['data']['taxpayer']['governmentTIN'] ?></div>
                        </div>
                    </div>
                    
                    <div class="taxpayer-info">
                        <h3>Taxpayer Information Details</h3>
                        <p>This taxpayer is registered with the Uganda Revenue Authority under the EFRIS system. 
                        The information displayed above is current as of the last update.</p>
                        
                        <h4>Key Information:</h4>
                        <ul>
                            <li><span class="highlight">Legal Name:</span> <?= $result['data']['taxpayer']['legalName'] ?></li>
                            <li><span class="highlight">Business Name:</span> <?= $result['data']['taxpayer']['businessName'] ?></li>
                            <li><span class="highlight">Contact:</span> <?= $result['data']['taxpayer']['contactNumber'] ?> / <?= $result['data']['taxpayer']['contactEmail'] ?></li>
                            <li><span class="highlight">Address:</span> <?= $result['data']['taxpayer']['address'] ?></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="error">
                    <h3>Error Retrieving Taxpayer Information</h3>
                    <p><strong>Error Code:</strong> <?= $result['error']['code'] ?></p>
                    <p><?= $result['error']['message'] ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Add some interactivity for better UX
        document.addEventListener('DOMContentLoaded', function() {
            const tinInput = document.getElementById('tin');
            const ninBrnInput = document.getElementById('ninBrn');
            
            // Clear one field when the other is being typed in
            tinInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    ninBrnInput.value = '';
                }
            });
            
            ninBrnInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    tinInput.value = '';
                }
            });
        });
    </script>
</body>
</html>