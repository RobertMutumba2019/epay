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

class ApprovalMatrix {
    public $id = 0;
    public $page = "APPROVAL MATRIX";

    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo '<h1><center style="color:red;">YOU DON\'T HAVE ACCESS TO THIS PAGE</center></h1>';
            header("Refresh: 1; url=dashboard.php");
            exit;
        }
    }

    public function id($id) {
        $this->id = $id;
    }

    public static function getLinks() {
        $page = "APPROVAL MATRIX";
        return [
            [
                "link_name"    => "Add Approval Matrix",
                "link_address" => "?action=add",
                "link_icon"    => "fa-plus",
                "link_page"    => $page,
                "link_right"   => "A",
            ],
            [
                "link_name"    => "All Approval",
                "link_address" => "?action=all",
                "link_icon"    => "fa-eye",
                "link_page"    => $page,
                "link_right"   => "V",
            ],
            [
                "link_name"    => "Import Approval Matrix",
                "link_address" => "?action=import",
                "link_icon"    => "fa-upload",
                "link_page"    => $page,
                "link_right"   => "V",
            ]
        ];
    }

    public function deleteApprovalMatrixAction($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM approval_matrix WHERE ap_id = ?");
            $stmt->execute([$id]);
            echo '<div style="color:green;">Approval matrix deleted successfully!</div>';
            header("Refresh: 3; url=?action=all");
        } catch (PDOException $e) {
            echo '<div style="color:red;">Error: ' . $e->getMessage() . '</div>';
        }
    }

    public function importApprovalMatrixAction() {
        global $pdo;
        $time = time();
        $user = $_SESSION['user_id'];
        $errors = [];

        if (isset($_POST['uploaddata'])) {
            $filename = $_FILES["uploadFile"]["tmp_name"];
            if ($_FILES["uploadFile"]["size"] > 0) {
                $valid_name = "CODE";
                $file = fopen($filename, "r");
                $count = 0;

                while (($emapData = fgetcsv($file, 10000, ",")) !== false) {
                    $count++;
                    $col_1 = strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[0]))));
                    $col_2 = strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[1]))));

                    if ($count == 1) {
                        if ($col_1 != $valid_name) {
                            $errors[] = "Invalid Template";
                        }
                    } else {
                        if (empty($col_1)) {
                            $errors[] = "Cell <b>A" . $count . "</b> should not be empty";
                        }
                        if (empty($col_2)) {
                            $errors[] = "Cell <b>B" . $count . "</b> should not be empty";
                        }

                        // Check for duplicates
                        $stmt = $pdo->prepare("SELECT ap_id FROM approval_matrix WHERE ap_code = ?");
                        $stmt->execute([$col_1]);
                        if ($stmt->fetch()) {
                            $errors[] = "Cell <b>A" . $count . "</b> already exists <b>$col_1-$col_2</b>";
                        }
                    }
                }
                fclose($file);

                if (empty($errors)) {
                    $file = fopen($filename, "r");
                    $count = 0;
                    while (($emapData = fgetcsv($file, 10000, ",")) !== false) {
                        $count++;
                        if ($count >= 2) {
                            $col_1 = strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[0]))));
                            $col_2 = strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[1]))));
                            $stmt = $pdo->prepare("INSERT INTO approval_matrix (ap_date_added, ap_code, ap_unit_code, ap_added_by) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$time, $col_1, $col_2, $user]);
                        }
                    }
                    fclose($file);
                    echo '<div style="color:green;">Approval matrix imported successfully!</div>';
                    header("Refresh: 3; url=?action=all");
                } else {
                    echo '<div style="color:red;">' . implode("<br>", $errors) . '</div>';
                }
            } else {
                echo '<div style="color:red;">Please attach a file</div>';
            }
        }

        ?>
        <div class="container">
            <div class="col-md-4">
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="uploadFile" class="form-control">
                    <br>
                    <button type="submit" class="btn btn-primary" name="uploaddata"><i style="font-size:12px;" class="fa fa-upload"></i> Upload</button>
                </form>
                <br><br>
                <a href="ApprovalMatrix.csv">Download Template</a>
            </div>
        </div>
        <?php
    }

    public function AddApprovalMatrixAction() {
        global $pdo;
        $errors = [];

        if (isset($_POST['submit'])) {
            $code = $_POST['code'] ?? '';
            $unit_name = $_POST['unit_name'] ?? '';
            $time = time();
            $user = $_SESSION['user_id'];

            if (empty($unit_name)) {
                $errors[] = "Enter Unit Code";
            }

            $stmt = $pdo->prepare("SELECT ap_id FROM approval_matrix WHERE ap_code = ?");
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $errors[] = "Code ($code) already exists";
            }

            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO approval_matrix (ap_date_added, ap_code, ap_unit_code, ap_added_by) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$time, $code, $unit_name, $user]);
                    echo '<div style="color:green;">Approval matrix added successfully!</div>';
                    header("Refresh: 3; url=?action=all");
                } catch (PDOException $e) {
                    echo '<div style="color:red;">Error: ' . $e->getMessage() . '</div>';
                }
            } else {
                echo '<div style="color:red;">' . implode("<br>", $errors) . '</div>';
            }
        }

        // Generate next code
        $stmt = $pdo->prepare("SELECT ap_code FROM approval_matrix WHERE ap_code IS NOT NULL ORDER BY ap_code DESC LIMIT 1");
        $stmt->execute();
        $last_code = $stmt->fetchColumn();
        $last_code = $last_code ? (int)$last_code + 1 : 1;
        $code = str_pad($last_code, 4, "0", STR_PAD_LEFT);

        ?>
        <div class="col-md-5">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Approval Matrix</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form role="form" action="" method="post">
                                    <div id="must">All fields with asterisk(*) are mandatory.</div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label style="display: none">Code<span class="must">*</span></label>
                                                <div class="form-line">
                                                    <input type="hidden" name="code" id="code" value="<?php echo htmlspecialchars($code); ?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Unit Name<span class="must">*</span></label>
                                                <div class="form-line">
                                                    <input class="form-control" id="unit_name" type="text" name="unit_name" placeholder="Enter Unit Name" value="<?php echo htmlspecialchars($_POST['unit_name'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <br>
                                            <button type="submit" name="submit" style="width: 100px;" class="form-control btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    public function AllApprovalMatrixAction() {
        global $pdo;
        ?>
        <div class="col-md-12">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM approval_matrix");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$rows) {
                    echo '<p>No approval matrix entries found.</p>';
                } else {
                    echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th width="30px">No.</th>';
                    echo '<th>Code</th>';
                    echo '<th>Unit Code</th>';
                    echo '<th>Date Added</th>';
                    echo '<th>Added By</th>';
                    echo '<th style="width:15%">Action</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    $i = 1;
                    foreach ($rows as $row) {
                        echo '<tr>';
                        echo '<td><center>' . ($i++) . '.</center></td>';
                        echo '<td>' . htmlspecialchars($row['ap_code']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['ap_unit_code']) . '</td>';
                        echo '<td>' . date('Y-m-d H:i:s', $row['ap_date_added']) . '</td>';
                        echo '<td>' . htmlspecialchars($this->full_name($row['ap_added_by'])) . '</td>';
                        echo '<td>';
                        echo '<a class="btn btn-success btn-xs" href="?action=edit&id=' . $row['ap_id'] . '">Edit</a> ';
                        echo '<a class="btn btn-xs btn-danger" href="?action=delete&id=' . $row['ap_id'] . '">Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                }
            } catch (PDOException $e) {
                echo '<div style="color:red;">Error: ' . $e->getMessage() . '</div>';
            }
            ?>
        </div>
        <?php
    }

    public function EditApprovalMatrixAction($id) {
        global $pdo;
        $errors = [];

        try {
            $stmt = $pdo->prepare("SELECT * FROM approval_matrix WHERE ap_id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                echo '<div style="color:red;">Approval matrix not found.</div>';
                return;
            }

            if (isset($_POST['submit'])) {
                $code = $_POST['code'] ?? '';
                $unit_name = $_POST['unit_name'] ?? '';
                $time = time();
                $user = $_SESSION['user_id'];

                if (empty($code)) {
                    $errors[] = "Enter Code";
                }
                if (empty($unit_name)) {
                    $errors[] = "Enter Unit Code";
                }

                if (empty($errors)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE approval_matrix SET ap_date_added = ?, ap_code = ?, ap_unit_code = ?, ap_added_by = ? WHERE ap_id = ?");
                        $stmt->execute([$time, $code, $unit_name, $user, $id]);
                        echo '<div style="color:green;">Approval matrix updated successfully!</div>';
                        header("Refresh: 3; url=?action=all");
                    } catch (PDOException $e) {
                        echo '<div style="color:red;">Error: ' . $e->getMessage() . '</div>';
                    }
                } else {
                    echo '<div style="color:red;">' . implode("<br>", $errors) . '</div>';
                }
            }

            ?>
            <div class="col-md-5">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit Approval Matrix</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" action="" method="post">
                                        <div id="must">All fields with asterisk(*) are mandatory.</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Code<span class="must">*</span></label>
                                                    <div class="form-line">
                                                        <input type="text" name="code" id="code" value="<?php echo htmlspecialchars($row['ap_code']); ?>" class="form-control" placeholder="Enter Code">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Unit Name<span class="must">*</span></label>
                                                    <div class="form-line">
                                                        <input class="form-control" id="unit_name" type="text" name="unit_name" placeholder="Enter Unit Name" value="<?php echo htmlspecialchars($row['ap_unit_code']); ?>">
                                                    </div>
                                                </div>
                                                <br>
                                                <button type="submit" name="submit" style="width: 100px;" class="form-control btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } catch (PDOException $e) {
            echo '<div style="color:red;">Error: ' . $e->getMessage() . '</div>';
        }
    }

    public function full_name($user_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $name = $stmt->fetchColumn();
            return $name ?: "User $user_id";
        } catch (PDOException $e) {
            error_log("Error in full_name: " . $e->getMessage());
            return "User $user_id";
        }
    }
}

// Handle routing
$action = $_GET['action'] ?? 'all';
$id = $_GET['id'] ?? 0;

$matrix = new ApprovalMatrix();
if ($id) {
    $matrix->id($id);
}

// Basic CSS for styling
echo '
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .container { max-width: 1200px; margin: auto; }
    .col-md-4, .col-md-5, .col-md-12 { margin-bottom: 20px; }
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
</style>
';

// Navigation links
$links = ApprovalMatrix::getLinks();
echo '<div class="container"><div class="col-md-12"><h2>Approval Matrix</h2>';
foreach ($links as $link) {
    echo '<a href="' . $link['link_address'] . '" class="btn btn-primary" style="margin-right:10px;"><i class="fa fa-' . $link['link_icon'] . '"></i> ' . $link['link_name'] . '</a>';
}
echo '</div></div>';

// Route actions
switch ($action) {
    case 'add':
        $matrix->AddApprovalMatrixAction();
        break;
    case 'edit':
        if ($id) {
            $matrix->EditApprovalMatrixAction($id);
        } else {
            echo '<div style="color:red;">Invalid ID</div>';
        }
        break;
    case 'delete':
        if ($id) {
            $matrix->deleteApprovalMatrixAction($id);
        } else {
            echo '<div style="color:red;">Invalid ID</div>';
        }
        break;
    case 'import':
        $matrix->importApprovalMatrixAction();
        break;
    case 'all':
    default:
        $matrix->AllApprovalMatrixAction();
        break;
}
?>