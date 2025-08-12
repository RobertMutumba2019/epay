<?php
$host = "localhost";
$dbname = "access";
$dbuser = "root"; // Default for XAMPP
$dbpass = ""; // Default for XAMPP (no password)

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
