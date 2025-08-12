<?php
session_start();

$host = 'localhost';
$db   = 'access';
$user = 'root'; // default for XAMPP
$pass = '';     // default for XAMPP

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper: Get current user ID
function user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
}

// Helper: Return base URL
function base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    return "$protocol://$host$dir/";
}

// Helper: Redirect
function redirect($url) {
    header("Location: " . base_url() . $url);
    exit();
}

// Helper: Sanitize input
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim(strip_tags($data)));
}
?>