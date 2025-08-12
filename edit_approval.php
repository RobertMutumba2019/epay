<?php
checkAccess();
$id = (int)($_GET['id'] ?? 0);

$result = $conn->query("SELECT * FROM approval_matrix WHERE ap_id = $id");
if (!$result->num_rows) {
    die("Not found.");
}
$row = $result->fetch_assoc();

if ($_POST['submit'] ?? null) {
    $code = clean($_POST['code']);
    $unit_name = clean($_POST['unit_name']);
    $time = time();

    $errors = [];
    if (empty($code)) $errors[] = "Code required.";
    if (empty($unit_name)) $errors[] = "Unit Name required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE approval_matrix SET ap_code=?, ap_unit_code=?, ap_date_added=? WHERE ap_id=?");
        $stmt->bind_param("ssii", $code, $unit_name, $time, $id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Updated!</div>";
            echo "<meta http-equiv='refresh' content='2;url=approval_matrix.php'>";
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
        <label>Code *</label>
        <input type="text" name="code" value="<?= htmlspecialchars($row['ap_code']) ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Unit Name *</label>
        <input type="text" name="unit_name" value="<?= htmlspecialchars($row['ap_unit_code']) ?>" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Update</button>
    <a href="approval_matrix.php" class="btn btn-default">Cancel</a>
</form>