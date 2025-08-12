<?php
// approval_matrix.php (router file)
session_start();
include "db_connect.php";
include "helpers.php";
include "approval_matrix.inc.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$am = new ApprovalMatrix();

$action = $_GET['action'] ?? '';
$parts = explode('/', $action);

$subaction = $parts[1] ?? '';

if (isset($parts[2])) {
    $am->id($parts[2]);
}

switch ($subaction) {
    case 'add-approval-Matrix':
        $am->AddApprovalMatrixAction();
        break;
    case 'all-approval-Matrix':
        $am->AllApprovalMatrixAction();
        break;
    case 'import-approval-Matrix':
        $am->importApprovalMatrixAction();
        break;
    case 'edit-approval-matrix':
        $am->EditApprovalMatrixAction();
        break;
    case 'delete-approval-matrix':
        $am->deleteApprovalMatrixAction();
        break;
    default:
        echo "<p>No action specified. Go back to <a href='admin.php'>Admin Dashboard</a>.</p>";
        break;
}
?>