<?php
session_start();

// Mock user authentication
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Mock user ID for testing; replace with proper login check
}

// Database connection (replace with your credentials)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=rest", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

class MasterApproval {
    public function order($label, $type) {
        global $pdo;
        echo '<h3>' . htmlspecialchars($label) . '</h3>';

        if (isset($_POST['add_role']) && $_POST['type'] == $type) {
            $role_id = $_POST['role_id'];
            $stmt = $pdo->prepare("SELECT MAX(app_count) AS max_count FROM master_approval_order WHERE app_type = ?");
            $stmt->execute([$type]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max = $row['max_count'] ?? 0;
            $count = $max + 1;
            $stmt = $pdo->prepare("INSERT INTO master_approval_order (app_type, app_role_id, app_count) VALUES (?, ?, ?)");
            $stmt->execute([$type, $role_id, $count]);
            echo '<div style="color:green;">Role added successfully!</div>';
            header("Refresh: 3");
        }

        echo '<form method="post">';
        echo '<input type="hidden" name="type" value="' . htmlspecialchars($type) . '">';
        echo '<label>Select Role:</label><br>';
        echo '<select name="role_id" class="form-control">';
        $stmt = $pdo->query("SELECT ap_id, ap_code, ap_unit_code FROM approval_matrix");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['ap_id'] . '">' . htmlspecialchars($row['ap_code'] . ' - ' . $row['ap_unit_code']) . '</option>';
        }
        echo '</select><br>';
        echo '<button type="submit" name="add_role" class="btn btn-primary">Add Role</button>';
        echo '</form>';
    }
}

class ApprovalOrder {
    public $page = "APPROVAL ORDER";
    public $id = "";

    public function id($id) {
        $this->id = $id;
    }

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            echo '<h1><center style="color:red;">YOU DON\'T HAVE ACCESS TO THIS PAGE</center></h1>';
            header("Refresh: 1; url=dashboard.php");
            exit;
        }
    }

    public static function getLinks() {
        $page = "APPROVAL ORDER";
        return [
            [
                "link_name"    => "Project/Div/Procurement Unit",
                "link_address" => "?action=procurement-unit",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Open General HQ Payment",
                "link_address" => "?action=open-general-hq",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Leasing Payment",
                "link_address" => "?action=leasing-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Project Payment",
                "link_address" => "?action=project-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Corporate Services Payment",
                "link_address" => "?action=corporate-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Mortgage Payment",
                "link_address" => "?action=mortgage-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Fixed Asset Payment",
                "link_address" => "?action=fixed-asset-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Stores Payment",
                "link_address" => "?action=stores-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Donation Payment",
                "link_address" => "?action=donation-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Staff Payment",
                "link_address" => "?action=staff-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Staff Payment Paid at Branch",
                "link_address" => "?action=staff-payment-paid-at-branch",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "GL to GL Payment",
                "link_address" => "?action=gl-to-gl",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Imported Service Payment",
                "link_address" => "?action=services-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Imported Goods Payment",
                "link_address" => "?action=goods-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "TAX ON IMPORTED GOODS PAYMENT",
                "link_address" => "?action=tax-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Payments that require Apportionment",
                "link_address" => "?action=apportionment-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Payments that require Apportionment from file",
                "link_address" => "?action=apportionment-file-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Payments that require Apportionment from file Without Budget Check",
                "link_address" => "?action=apportionment-file-no-budget-check-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Reversal Payments",
                "link_address" => "?action=reversal-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "Dividends Payments",
                "link_address" => "?action=dividends-payment",
                "link_icon"    => "fa-edit",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
        ];
    }

    private function displayList($type) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT app_role_id, ap_code, ap_unit_code FROM approval_matrix, master_approval_order WHERE app_role_id = ap_id AND app_type = ? ORDER BY app_count ASC");
        $stmt->execute([$type]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            echo '<ol>';
            foreach ($rows as $row) {
                echo '<li style="font-weight:bold;">' . htmlspecialchars($row['ap_code'] . ' - ' . $row['ap_unit_code']) . '</li>';
                $stmt2 = $pdo->prepare("SELECT name, email FROM approval_group, users WHERE apg_user = id AND apg_name = ?");
                $stmt2->execute([$row['app_role_id']]);
                $users = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                if ($users) {
                    foreach ($users as $user) {
                        echo '<div style="float:left;margin:2px; background-color:#D74B48;color:white;padding:2px 10px;border-radius:5px;">' . htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['email']) . ')</div>';
                    }
                }
                echo '<div style="clear:both"></div>';
            }
            echo '</ol>';
        } else {
            echo '<p>No approvals found for this type.</p>';
        }
    }

    private function displaySection($label, $type, $col_left = 6, $col_right = 6) {
        echo '<div class="col-md-12">';
        echo '<hr>';
        echo '<div class="col-md-' . $col_left . '">';
        $masterApproval = new MasterApproval();
        $masterApproval->order($label, $type);
        echo '</div>';
        echo '<div class="col-md-' . $col_right . '">';
        $this->displayList($type);
        echo '</div>';
        echo '</div>';
    }

    public function StoresPaymentAction() {
        echo '<div class="row">';
        $this->displaySection("STORES PAYMENT", "STORES PAYMENT");
        $this->displaySection("STORES PAYMENT BELOW 5M", "STORES PAYMENT BELOW 5M");
        $this->displaySection("STORES PAYMENT BELOW 100M", "STORES PAYMENT BELOW 100M");
        $this->displaySection("STORES PAYMENT ABOVE 100M", "STORES PAYMENT ABOVE 100M");
        echo '</div>';
    }

    public function procurementUnitAction() {
        echo '<div class="row">';
        $this->displaySection("SUPPLIER PAYMENT", "SUPPLIER PAYMENT", 4, 8);
        $this->displaySection("BELOW 5M", "BELOW 5M", 4, 8);
        $this->displaySection("BELOW 100M", "BELOW 100M", 4, 8);
        $this->displaySection("ABOVE 100M", "ABOVE 100M", 4, 8);
        echo '</div>';
    }

    public function openGeneralHqAction() {
        echo '<div class="row">';
        $this->displaySection("OPEN GENERAL HQ PAYMENT", "OPEN GENERAL HQ PAYMENT", 4, 8);
        $this->displaySection("OPEN GENERAL HQ PAYMENT BELOW 5M", "OPEN GENERAL HQ PAYMENT BELOW 5M", 4, 8);
        $this->displaySection("OPEN GENERAL HQ PAYMENT BELOW 100M", "OPEN GENERAL HQ PAYMENT BELOW 100M", 4, 8);
        $this->displaySection("OPEN GENERAL HQ PAYMENT ABOVE 100M", "OPEN GENERAL HQ PAYMENT ABOVE 100M", 4, 8);
        echo '</div>';
    }

    public function ProjectPaymentAction() {
        echo '<div class="row">';
        $this->displaySection("PROJECT PAYMENT", "PROJECT PAYMENT");
        $this->displaySection("PROJECT PAYMENT BELOW 5M", "PROJECT PAYMENT BELOW 5M");
        $this->displaySection("PROJECT PAYMENT BELOW 100M", "PROJECT PAYMENT BELOW 100M");
        $this->displaySection("PROJECT PAYMENT ABOVE 100M", "PROJECT PAYMENT ABOVE 100M");
        echo '</div>';
    }

    public function CorporatePaymentAction() {
        echo '<div class="row">';
        $this->displaySection("CORPORATE PAYMENT", "CORPORATE PAYMENT");
        $this->displaySection("CORPORATE PAYMENT BELOW 5M", "CORPORATE PAYMENT BELOW 5M");
        $this->displaySection("CORPORATE PAYMENT BELOW 100M", "CORPORATE PAYMENT BELOW 100M");
        $this->displaySection("CORPORATE PAYMENT ABOVE 100M", "CORPORATE PAYMENT ABOVE 100M");
        echo '</div>';
    }

    public function MortgagePaymentAction() {
        echo '<div class="row">';
        $this->displaySection("MORTGAGE PAYMENT", "MORTGAGE PAYMENT");
        $this->displaySection("MORTGAGE PAYMENT BELOW 5M", "MORTGAGE PAYMENT BELOW 5M");
        $this->displaySection("MORTGAGE PAYMENT BELOW 100M", "MORTGAGE PAYMENT BELOW 100M");
        $this->displaySection("MORTGAGE PAYMENT ABOVE 100M", "MORTGAGE PAYMENT ABOVE 100M");
        echo '</div>';
    }

    public function below5MAction() {
        echo '<div class="row">';
        $this->displaySection("BELOW 5M", "BELOW 5M", 4, 8);
        echo '</div>';
    }

    public function below100MAction() {
        echo '<div class="row">';
        $this->displaySection("BELOW 100M", "BELOW 100M", 4, 8);
        echo '</div>';
    }

    public function above100MAction() {
        echo '<div class="row">';
        $this->displaySection("ABOVE 100M", "ABOVE 100M", 4, 8);
        echo '</div>';
    }

    // Add similar methods for other payment types as needed. For example:
    // public function LeasingPaymentAction() {
    //     echo '<div class="row">';
    //     $this->displaySection("LEASING PAYMENT", "LEASING PAYMENT");
    //     $this->displaySection("LEASING PAYMENT BELOW 5M", "LEASING PAYMENT BELOW 5M");
    //     $this->displaySection("LEASING PAYMENT BELOW 100M", "LEASING PAYMENT BELOW 100M");
    //     $this->displaySection("LEASING PAYMENT ABOVE 100M", "LEASING PAYMENT ABOVE 100M");
    //     echo '</div>';
    // }
    // Duplicate and modify for donation-payment, staff-payment, etc., replacing the labels and types accordingly.
}

// Handle routing
$action = $_GET['action'] ?? 'all';
$id = $_GET['id'] ?? 0;

$order = new ApprovalOrder();
if ($id) {
    $order->id($id);
}

// Basic CSS for styling
echo '
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .container { max-width: 1200px; margin: auto; }
    .col-md-4, .col-md-5, .col-md-6, .col-md-8, .col-md-12 { margin-bottom: 20px; }
    .panel { border: 1px solid #ddd; border-radius: 4px; }
    .panel-heading { background: #f5f5f5; padding: 10px; font-weight: bold; }
    .panel-body { padding: 15px; }
    .form-control { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
    .btn { padding: 8px 16px; border: none; cursor: pointer; }
    .btn-primary { background: #0066cc; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-danger { background: #dc3545; color: white; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .must { color: red; }
    #must { margin-bottom: 10px; }
    hr { border-bottom: 2px solid #000; margin: 20px 0; }
    ol { padding-left: 20px; }
</style>
';

// Navigation links
$links = ApprovalOrder::getLinks();
echo '<div class="container"><div class="col-md-12"><h2>Approval Order</h2>';
foreach ($links as $link) {
    echo '<a href="' . $link['link_address'] . '" class="btn btn-primary" style="margin-right:10px;margin-bottom:10px;"><i class="fa fa-' . $link['link_icon'] . '"></i> ' . htmlspecialchars($link['link_name']) . '</a>';
}
echo '</div></div>';

// Route actions
switch ($action) {
    case 'stores-payment':
        $order->StoresPaymentAction();
        break;
    case 'procurement-unit':
        $order->procurementUnitAction();
        break;
    case 'open-general-hq':
        $order->openGeneralHqAction();
        break;
    case 'project-payment':
        $order->ProjectPaymentAction();
        break;
    case 'corporate-payment':
        $order->CorporatePaymentAction();
        break;
    case 'mortgage-payment':
        $order->MortgagePaymentAction();
        break;
    case 'below-5m':
        $order->below5MAction();
        break;
    case 'below-100m':
        $order->below100MAction();
        break;
    case 'above-100m':
        $order->above100MAction();
        break;
    // Add cases for other actions as you implement their methods
    default:
        echo '<div class="container"><p>Select a payment type from the links above.</p></div>';
        break;
}
?>