<?php
checkAccess();
$id = (int)($_GET['id'] ?? 0);
$conn->query("DELETE FROM approval_matrix WHERE ap_id = $id");
redirect('approval_matrix.php');