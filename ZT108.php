<?php
// T108 Invoice Details Query System
class EfrisT108InvoiceQuery {
    // API Configuration
    private $apiUrl = "https://efris-api.ug/api/v1/invoice/details";
    private $appId = "AP04";
    private $interfaceCode = "T108";
    private $taxpayerID = "1000000000"; // Example TIN
    
    // Query invoice details
    public function getInvoiceDetails($invoiceNo) {
        // Validate input
        if (empty($invoiceNo)) {
            return ['success' => false, 'error' => 'Invoice number is required'];
        }
        
        // Build request payload
        $request = [
            'invoiceNo' => $invoiceNo
        ];
        
        // Send to EFRIS API (simulated)
        $response = $this->sendApiRequest($request);
        
        return $response;
    }
    
    // Simulate API request
    private function sendApiRequest($request) {
        // Simulate API processing delay
        sleep(1);
        
        // For demo purposes, return sample data if invoice matches example
        if ($request['invoiceNo'] === "22598049632407113016") {
            return [
                'success' => true,
                'data' => $this->getSampleResponse()
            ];
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => '404',
                    'message' => 'Invoice not found'
                ]
            ];
        }
    }
    
    // Sample response data based on documentation
    private function getSampleResponse() {
        return [
            "sellerDetails" => [
                "tin" => "201905081705",
                "ninBrn" => "201905081705",
                "legalName" => "zhangsan",
                "businessName" => "lisi",
                "address" => "beijin",
                "mobilePhone" => "15501234567",
                "linePhone" => "010-6689666",
                "emailAddress" => "123456@163.com",
                "placeOfBusiness" => "beijin",
                "branchId" => "207300908813650312",
                "branchName" => "KATUSIIME EVEALYNE SPARE PARTS",
                "branchCode" => "00"
            ],
            "basicInformation" => [
                "invoiceId" => "1000002",
                "invoiceNo" => "00000000001",
                "oriInvoiceNo" => "00000000002",
                "antifakeCode" => "201905081711",
                "deviceNo" => "201905081234",
                "issuedDate" => "08/05/2019 17:13:12",
                "oriIssuedDate" => "08/05/2019 17:13:12",
                "oriGrossAmount" => "9247",
                "operator" => "aisino",
                "currency" => "UGX",
                "invoiceType" => "1",
                "invoiceKind" => "1",
                "dataSource" => "101",
                "isInvalid" => "1",
                "isRefund" => "1",
                "invoiceIndustryCode" => "102",
                "currencyRate" => "3700.12"
            ],
            "buyerDetails" => [
                "buyerTin" => "201905081705",
                "buyerNinBrn" => "201905081705",
                "buyerLegalName" => "zhangsan",
                "buyerBusinessName" => "lisi",
                "buyerAddress" => "beijin",
                "buyerEmail" => "123456@163.com",
                "buyerMobilePhone" => "15501234567",
                "buyerLinePhone" => "010-6689666",
                "buyerPlaceOfBusi" => "beijin",
                "buyerType" => "1",
                "buyerCitizenship" => "1",
                "buyerSector" => "1"
            ],
            "goodsDetails" => [
                [
                    "invoiceItemId" => "231242354564645214",
                    "item" => "apple",
                    "itemCode" => "101",
                    "qty" => "2",
                    "unitOfMeasure" => "kg",
                    "unitPrice" => "150.00",
                    "total" => "300.00",
                    "taxRate" => "0.18",
                    "tax" => "54.00",
                    "discountTotal" => "18.00",
                    "orderNumber" => "1"
                ],
                [
                    "invoiceItemId" => "231242354564645215",
                    "item" => "car",
                    "itemCode" => "101",
                    "qty" => "1",
                    "unitOfMeasure" => "pc",
                    "unitPrice" => "8947.00",
                    "total" => "8947.00",
                    "taxRate" => "0.18",
                    "tax" => "1610.46",
                    "orderNumber" => "2"
                ]
            ],
            "taxDetails" => [
                [
                    "taxCategoryCode" => "01",
                    "netAmount" => "3813.55",
                    "taxRate" => "0.18",
                    "taxAmount" => "686.45",
                    "grossAmount" => "4500.00"
                ],
                [
                    "taxCategoryCode" => "05",
                    "netAmount" => "1818.18",
                    "taxRate" => "0.1",
                    "taxAmount" => "181.82",
                    "grossAmount" => "2000.00"
                ]
            ],
            "summary" => [
                "netAmount" => "8379",
                "taxAmount" => "868",
                "grossAmount" => "9247",
                "itemCount" => "2",
                "modeCode" => "0",
                "remarks" => "This is another remark test.",
                "qrCode" => "asdfghjkl"
            ],
            "payWay" => [
                [
                    "paymentMode" => "101",
                    "paymentAmount" => "5000.00",
                    "orderNumber" => "a"
                ],
                [
                    "paymentMode" => "102",
                    "paymentAmount" => "4247.00",
                    "orderNumber" => "b"
                ]
            ],
            "creditNoteExtend" => [
                "preGrossAmount" => "9247",
                "preTaxAmount" => "868",
                "preNetAmount" => "8379"
            ]
        ];
    }
}

// Handle form submission
$query = new EfrisT108InvoiceQuery();
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoiceNo'])) {
    $result = $query->getInvoiceDetails($_POST['invoiceNo']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFRIS T108 Invoice Details Query</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
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
        .invoice-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #e1e8ed;
            border-radius: 6px;
        }
        .invoice-section h3 {
            margin-top: 0;
            color: #3498db;
            border-bottom: 1px solid #e1e8ed;
            padding-bottom: 8px;
        }
        .data-row {
            display: flex;
            margin-bottom: 8px;
        }
        .data-label {
            flex: 1;
            font-weight: 600;
        }
        .data-value {
            flex: 2;
        }
        .goods-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .goods-table th, .goods-table td {
            border: 1px solid #e1e8ed;
            padding: 8px;
            text-align: left;
        }
        .goods-table th {
            background-color: #f8f9fa;
        }
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .tax-table th, .tax-table td {
            border: 1px solid #e1e8ed;
            padding: 8px;
            text-align: left;
        }
        .tax-table th {
            background-color: #f8f9fa;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-label {
            font-weight: 600;
        }
        .summary-value {
            font-weight: 700;
        }
        .payment-method {
            display: inline-block;
            background-color: #e8f4fc;
            padding: 8px 12px;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .tab-container {
            margin-top: 20px;
        }
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #e1e8ed;
            margin-bottom: 15px;
        }
        .tab-button {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #7f8c8d;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }
        .tab-button.active {
            color: #3498db;
            border-bottom: 2px solid #3498db;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>EFRIS T108 Invoice Details Query</h1>
        
        <div class="info-box">
            <strong>Interface Code T108:</strong> Query detailed invoice information from EFRIS using the invoice number.
            This interface retrieves comprehensive invoice data including seller/buyer details, line items, taxes, and payment information.
        </div>
        
        <form method="post">
            <div class="form-group">
                <label for="invoiceNo">Invoice Number</label>
                <input type="text" id="invoiceNo" name="invoiceNo" placeholder="Enter invoice number" required>
                <small>For demo, use: 22598049632407113016</small>
            </div>
            <button type="submit">Query Invoice Details</button>
        </form>
        
        <?php if ($result): ?>
        <div class="result">
            <?php if ($result['success']): ?>
                <div class="success">
                    <h3>Invoice Details Retrieved Successfully</h3>
                    
                    <div class="tab-container">
                        <div class="tab-buttons">
                            <button class="tab-button active" onclick="openTab(event, 'basic-tab')">Basic Info</button>
                            <button class="tab-button" onclick="openTab(event, 'parties-tab')">Parties</button>
                            <button class="tab-button" onclick="openTab(event, 'items-tab')">Items</button>
                            <button class="tab-button" onclick="openTab(event, 'tax-tab')">Taxes</button>
                            <button class="tab-button" onclick="openTab(event, 'payment-tab')">Payment</button>
                        </div>
                        
                        <div id="basic-tab" class="tab-content active">
                            <div class="invoice-section">
                                <h3>Basic Information</h3>
                                <div class="data-row">
                                    <div class="data-label">Invoice Number:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['invoiceNo'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Original Invoice No:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['oriInvoiceNo'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Issue Date:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['issuedDate'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Currency:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['currency'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Operator:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['operator'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Device No:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['deviceNo'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Invoice Type:</div>
                                    <div class="data-value"><?= $result['data']['basicInformation']['invoiceType'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Remarks:</div>
                                    <div class="data-value"><?= $result['data']['summary']['remarks'] ?></div>
                                </div>
                            </div>
                            
                            <div class="summary-box">
                                <h3>Invoice Summary</h3>
                                <div class="summary-row">
                                    <span class="summary-label">Net Amount:</span>
                                    <span class="summary-value"><?= number_format($result['data']['summary']['netAmount'], 2) ?> <?= $result['data']['basicInformation']['currency'] ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Tax Amount:</span>
                                    <span class="summary-value"><?= number_format($result['data']['summary']['taxAmount'], 2) ?> <?= $result['data']['basicInformation']['currency'] ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Gross Amount:</span>
                                    <span class="summary-value"><?= number_format($result['data']['summary']['grossAmount'], 2) ?> <?= $result['data']['basicInformation']['currency'] ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Item Count:</span>
                                    <span class="summary-value"><?= $result['data']['summary']['itemCount'] ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="parties-tab" class="tab-content">
                            <div class="invoice-section">
                                <h3>Seller Details</h3>
                                <div class="data-row">
                                    <div class="data-label">TIN:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['tin'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Legal Name:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['legalName'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Business Name:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['businessName'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Address:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['address'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Mobile Phone:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['mobilePhone'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Email:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['emailAddress'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Branch Name:</div>
                                    <div class="data-value"><?= $result['data']['sellerDetails']['branchName'] ?></div>
                                </div>
                            </div>
                            
                            <div class="invoice-section">
                                <h3>Buyer Details</h3>
                                <div class="data-row">
                                    <div class="data-label">TIN:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerTin'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Legal Name:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerLegalName'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Business Name:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerBusinessName'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Address:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerAddress'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Mobile Phone:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerMobilePhone'] ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Email:</div>
                                    <div class="data-value"><?= $result['data']['buyerDetails']['buyerEmail'] ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="items-tab" class="tab-content">
                            <div class="invoice-section">
                                <h3>Goods/Services Details</h3>
                                <table class="goods-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Code</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                            <th>Tax Rate</th>
                                            <th>Tax Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['data']['goodsDetails'] as $item): ?>
                                        <tr>
                                            <td><?= $item['item'] ?></td>
                                            <td><?= $item['itemCode'] ?></td>
                                            <td><?= $item['qty'] ?> <?= $item['unitOfMeasure'] ?></td>
                                            <td><?= number_format($item['unitPrice'], 2) ?></td>
                                            <td><?= number_format($item['total'], 2) ?></td>
                                            <td><?= $item['taxRate'] * 100 ?>%</td>
                                            <td><?= number_format($item['tax'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="tax-tab" class="tab-content">
                            <div class="invoice-section">
                                <h3>Tax Details</h3>
                                <table class="tax-table">
                                    <thead>
                                        <tr>
                                            <th>Tax Category</th>
                                            <th>Net Amount</th>
                                            <th>Tax Rate</th>
                                            <th>Tax Amount</th>
                                            <th>Gross Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['data']['taxDetails'] as $tax): ?>
                                        <tr>
                                            <td><?= $tax['taxCategoryCode'] ?></td>
                                            <td><?= number_format($tax['netAmount'], 2) ?></td>
                                            <td><?= $tax['taxRate'] * 100 ?>%</td>
                                            <td><?= number_format($tax['taxAmount'], 2) ?></td>
                                            <td><?= number_format($tax['grossAmount'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="payment-tab" class="tab-content">
                            <div class="invoice-section">
                                <h3>Payment Information</h3>
                                <h4>Payment Methods:</h4>
                                <?php foreach ($result['data']['payWay'] as $payment): ?>
                                <div class="payment-method">
                                    <strong>Mode:</strong> <?= $payment['paymentMode'] ?>, 
                                    <strong>Amount:</strong> <?= number_format($payment['paymentAmount'], 2) ?>, 
                                    <strong>Order:</strong> <?= $payment['orderNumber'] ?>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (isset($result['data']['creditNoteExtend'])): ?>
                                <h4>Credit Note Information:</h4>
                                <div class="data-row">
                                    <div class="data-label">Previous Gross Amount:</div>
                                    <div class="data-value"><?= number_format($result['data']['creditNoteExtend']['preGrossAmount'], 2) ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Previous Tax Amount:</div>
                                    <div class="data-value"><?= number_format($result['data']['creditNoteExtend']['preTaxAmount'], 2) ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Previous Net Amount:</div>
                                    <div class="data-value"><?= number_format($result['data']['creditNoteExtend']['preNetAmount'], 2) ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="error">
                    <h3>Error Retrieving Invoice Details</h3>
                    <p><strong>Error Code:</strong> <?= $result['error']['code'] ?></p>
                    <p><?= $result['error']['message'] ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function openTab(evt, tabName) {
            // Hide all tab content
            var tabContents = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove("active");
            }
            
            // Remove active class from all tab buttons
            var tabButtons = document.getElementsByClassName("tab-button");
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            // Show the specific tab content
            document.getElementById(tabName).classList.add("active");
            
            // Add active class to the button that opened the tab
            evt.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>