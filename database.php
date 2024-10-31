<?php
$servername = "localhost"; 
$username = "root";
$password = ""; 
$dbname = "utilities";

$conn = mysqli_connect("localhost", "root", "", "utilities");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

