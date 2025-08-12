<?php
checkAccess();

if ($_POST['submit'] ?? null) {
    $unit_name = clean($_POST['unit_name']);
    $time = time();
    $user = user_id();

    // Auto-generate code
    $res = $conn->query("SELECT ap_code FROM approval_matrix WHERE ap_code IS NOT NULL ORDER BY ap_code DESC LIMIT 1");
    $last = $res->fetch_assoc();
    $last_code = (int)($last['ap_code'] ?? 0);
    $code = str_pad($last_code + 1, 3, '0', STR_PAD_LEFT);

    $errors = [];

    if (empty($unit_name)) {
        $errors[] = "Unit Name is required.";
    }

    if ($conn->query("SELECT 1 FROM approval_matrix WHERE ap_code='$code'")->num_rows) {
        $errors[] = "Code already exists.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO approval_matrix (ap_date_added, ap_code, ap_unit_code, ap_added_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $time, $code, $unit_name, $user);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Saved!</div>";
            echo "<meta http-equiv='refresh' content='2;url=approval_matrix.php'>";
        } else {
            echo "<div class='alert alert-danger'>DB Error: " . $conn->error . "</div>";
        }
    } else {
        foreach ($errors as $e) {
            echo "<div class='alert alert-danger'>$e</div>";
        }
    }
}
?>

<form method="post">
    <div class="form-group">
        <label>Unit Name *</label>
        <input type="text" name="unit_name" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Save</button>
    <a href="approval_matrix.php" class="btn btn-default">Cancel</a>
</form>