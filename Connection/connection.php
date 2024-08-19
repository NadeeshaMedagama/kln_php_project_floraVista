<?php 

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../Logger/logger.php';

// Database connection parameters
$host = "127.0.0.1";
$username = "thush";
$password = "thush";
$database = "kln_php";

// Connect to the database
$connection = mysqli_connect($host, $username, $password, $database);

if (mysqli_connect_errno()) {
    logger("ERROR", mysqli_connect_error());
    die("MySQL connection error: " . mysqli_connect_error());
}

?>

