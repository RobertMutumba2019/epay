<?php
// approval_matrix.inc.php (adapted class, removed access checks, changed buttons to submit)
include_once 'helpers.php';  // Include helpers if not already

class ApprovalMatrix extends BeforeAndAfter {
    public $id = 0;
    public $page = "APPROVAL MATRIX";

    public function __construct() {
        // Removed access checks as we handle via session role
    }

    public function id($id) {
        $this->id = $id;
    }

    public static function getLinks() {
        $page = "APPROVAL MATRIX";
        $links = [
            [
                "link_name" => "Add Approval Matrix",
                "link_address" => "approval-matrix/add-approval-Matrix",
                "link_icon" => "plus",
                "link_page" => $page,
                "link_right" => "A",
            ],
            [
                "link_name" => "All Approval",
                "link_address" => "approval-matrix/all-approval-Matrix",
                "link_icon" => "eye",
                "link_page" => $page,
                "link_right" => "V",
            ],
            [
                "link_name" => "Import Approval Matrix",
                "link_address" => "approval-matrix/import-approval-Matrix",
                "link_icon" => "upload",
                "link_page" => $page,
                "link_right" => "V",
            ]
        ];
        return $links;
    }

    public function deleteApprovalMatrixAction() {
        $id = portion(3);
        $this->deletor("approval_matrix", "ap_id", $id, 'approval-matrix/all-approval-Matrix');
    }

    public function importApprovalMatrixAction() {
        $db = new Db();
        $time = time();
        $user = user_id();
        $errors = [];
        if (isset($_POST['uploaddata'])) {
            $filename = $_FILES["uploadFile"]["tmp_name"];
            if ($_FILES["uploadFile"]["size"] > 0) {
                $valid_name = "CODE";
                $file = fopen($filename, "r");
                $count = 0;  // Fixed count initialization
                while (($emapData = fgetcsv($file, 10000, ",")) !== false) {
                    $count++;
                    $col_1 = @strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[0]))));
                    $col_2 = @strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[1]))));

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

                        $application_id = $this->rgf("approval_matrix", $col_1, "ap_code", "ap_id");
                        if ($application_id) {
                            $errors[] = "Cell <b>A" . $count . "</b> already exists <b>" . $col_1 . "-" . $col_2 . "</b>";
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
                            $col_1 = @strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[0]))));
                            $col_2 = @strtoupper(trim(str_replace("'", "\'", strip_tags($emapData[1]))));
                            $db->insert("approval_matrix", ["ap_date_added" => $time, "ap_code" => $col_1, "ap_unit_code" => $col_2, "ap_added_by" => $user]);
                        }
                    }
                    fclose($file);
                    if (!$db->error()) {
                        FeedBack::success();
                        FeedBack::refresh(3, return_url() . 'approval-matrix/all-approval-Matrix');
                    } else {
                        FeedBack::errors($errors);
                    }
                } else {
                    FeedBack::errors($errors);
                }
            } else {
                $errors[] = "Please Attach file";
                FeedBack::errors($errors);
            }
        }
        ?>
        <div class="container">
            <div class="col-md-4">
                <form method="post" enctype="multipart/form-data" action="">
                    <input type="file" name="uploadFile" class="form-control">
                    <br>
                    <button type="submit" class="btn btn-primary" name="uploaddata"><i style="font-size:12px;" class="fa fa-upload"></i> Upload</button>
                </form>
                <br><br>
                <a href="../import file/ApprovalMatrix.csv">Download Template</a>
            </div>
        </div>
        <?php
    }

    public function AddApprovalMatrixAction() {
        if (isset($_POST['submit'])) {
            $code = $_POST['code'];
            $unit_name = $_POST['unit_name'];
            $db = new Db();
            $errors = [];
            if (empty($unit_name)) {
                $errors[] = "Enter Unit Code";
            }
            if ($this->isThere("approval_matrix", ["ap_code" => $code])) {
                $errors[] = "Code($code) already exists";
            }
            if (empty($errors)) {
                $db->insert("approval_matrix", ["ap_date_added" => time(), "ap_code" => $code, "ap_unit_code" => $unit_name, "ap_added_by" => user_id()]);
                if (empty($db->error())) {
                    FeedBack::success();
                    FeedBack::refresh(3, return_url() . 'approval-matrix/all-approval-Matrix');
                } else {
                    FeedBack::error($db->error());
                }
            } else {
                FeedBack::errors($errors);
            }
        }
        ?>
        <div class="col-md-5">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Add Approval Matrix
                    </div>
                    <?php
                    $db = new Db();
                    $ap_code = ($db->select("SELECT ap_code FROM approval_matrix WHERE ap_code IS NOT NULL ORDER BY ap_code DESC LIMIT 1"))[0]['ap_code'] ?? 0;
                    $last_code = ((int)$ap_code) + 1;
                    if ($last_code <= 999) {
                        $code = "0$last_code";
                    } else {
                        $code = $last_code;
                    }
                    ?>
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
                                                    <input type="hidden" name="code" value="<?php echo $code; ?>" class="form-control" placeholder="Enter Code">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Unit Name<span class="must">*</span></label>
                                                <div class="form-line">
                                                    <input class="form-control" type="text" name="unit_name" placeholder="Enter Unit Name" value="<?php echo @$unit_name; ?>">
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
        </div>
        <?php
    }

    public function AllApprovalMatrixAction() {
        ?>
        <div class="col-md-12">
            <?php
            $db = new Db();
            $select = $db->select("SELECT * FROM approval_matrix");
            if ($db->error()) {
                echo $db->error();
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
                $i = 1;
                echo '<tbody>';
                foreach ($select as $row) {
                    extract($row);
                    echo '<tr>';
                    echo '<td><center>' . ($i++) . '.</center></td>';
                    echo '<td>' . ($ap_code) . '</td>';
                    echo '<td>' . ($ap_unit_code) . '</td>';
                    echo '<td>' . FeedBack::date_fm($ap_date_added) . '</td>';
                    echo '<td>' . full_name($ap_added_by) . '</td>';
                    echo '<td>';
                    echo '<a class="btn btn-success btn-xs" href="' . return_url() . 'approval_matrix.php?action=approval-matrix/edit-approval-matrix/' . $ap_id . '">Edit</a> ';
                    echo '<a class="btn btn-xs btn-danger" href="' . return_url() . 'approval_matrix.php?action=approval-matrix/delete-approval-matrix/' . $ap_id . '">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            ?>
        </div>
        <?php
    }

    public function EditApprovalMatrixAction() {
        $id = portion(3);
        $this->id = $id;
        $db = new Db();
        $select = $db->select("SELECT * FROM approval_matrix WHERE ap_id ='$id'");
        if (empty($select)) {
            echo "No record found";
            return;
        }
        extract($select[0]);
        if (isset($_POST['submit'])) {
            $code = $_POST['code'];
            $unit_name = $_POST['unit_name'];
            $errors = [];
            if (empty($code)) {
                $errors[] = "Enter Code";
            }
            if (empty($unit_name)) {
                $errors[] = "Enter Unit Code";
            }
            if (empty($errors)) {
                $db->update("approval_matrix", ["ap_date_added" => time(), "ap_code" => $code, "ap_unit_code" => $unit_name, "ap_added_by" => user_id()], ["ap_id" => $id]);
                if (empty($db->error())) {
                    FeedBack::success();
                    FeedBack::refresh(3, return_url() . 'approval-matrix/all-approval-Matrix');
                } else {
                    FeedBack::error($db->error());
                }
            } else {
                FeedBack::errors($errors);
            }
        }
        ?>
        <div class="col-md-5">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Edit Approval Matrix
                    </div>
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
                                                    <input type="text" name="code" value="<?php echo @$ap_code; ?>" class="form-control" placeholder="Enter Code">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Unit Name<span class="must">*</span></label>
                                                <div class="form-line">
                                                    <input class="form-control" type="text" name="unit_name" placeholder="Enter Item Name" value="<?php echo @$ap_unit_code; ?>">
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
        </div>
        <?php
    }
}
?>