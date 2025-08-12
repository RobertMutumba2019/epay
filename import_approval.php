<?php
checkAccess();

if (isset($_POST['uploaddata'])) {
    if ($_FILES['uploadFile']['size'] > 0) {
        $file = $_FILES['uploadFile']['tmp_name'];
        $handle = fopen($file, "r");
        $headers = fgetcsv($handle);
        if ($headers[0] !== 'CODE') {
            echo "<div class='alert alert-danger'>Invalid template. First cell must be 'CODE'</div>";
        } else {
            $errors = [];
            $count = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $count++;
                if ($count === 1) continue; // Skip header

                $code = strtoupper(trim($data[0] ?? ''));
                $unit = strtoupper(trim($data[1] ?? ''));

                if (empty($code)) $errors[] = "Row $count: Code is empty.";
                if (empty($unit)) $errors[] = "Row $count: Unit is empty.";

                $check = $conn->query("SELECT 1 FROM approval_matrix WHERE ap_code = '$code'");
                if ($check->num_rows) $errors[] = "Row $count: Code '$code' already exists.";
            }
            fclose($handle);

            if (empty($errors)) {
                // Reopen file to insert
                $handle = fopen($_FILES['uploadFile']['tmp_name'], "r");
                fgetcsv($handle); // Skip header
                $user = user_id();
                $time = time();
                while (($data = fgetcsv($handle)) !== false) {
                    $code = strtoupper(trim($data[0]));
                    $unit = strtoupper(trim($data[1]));
                    if ($code && $unit) {
                        $stmt = $conn->prepare("INSERT IGNORE INTO approval_matrix (ap_date_added, ap_code, ap_unit_code, ap_added_by) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $time, $code, $unit, $user);
                        $stmt->execute();
                    }
                }
                fclose($handle);
                echo "<div class='alert alert-success'>Import successful!</div>";
                echo "<meta http-equiv='refresh' content='2;url=approval_matrix.php'>";
            } else {
                foreach ($errors as $e) {
                    echo "<div class='alert alert-danger'>$e</div>";
                }
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Please attach a file.</div>";
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="uploadFile" class="form-control" accept=".csv" required>
    <br>
    <button type="submit" name="uploaddata" class="btn btn-primary">Upload CSV</button>
    <a href="<?= base_url() ?>templates/ApprovalMatrix.csv" class="btn btn-link">Download Template</a>
</form>