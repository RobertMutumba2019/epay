<?php
// admin.php (admin dashboard with links)
session_start();
include "db_connect.php";
include "helpers.php";
include "approval_matrix.inc.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .dashboard { max-width: 600px; margin: auto; }
        a { display: block; margin: 10px 0; color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?php echo $_SESSION['username']; ?> (Admin)</h2>
        <h3>Approval Matrix Options</h3>
        <?php
        $links = ApprovalMatrix::getLinks();
        foreach ($links as $link) {
            echo '<a href="approval_matrix.php?action=' . $link['link_address'] . '"><i class="fa fa-' . $link['link_icon'] . '"></i> ' . $link['link_name'] . '</a>';
        }
        ?>
        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>