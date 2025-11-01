<?php
$servername = "sql113.infinityfree.com";
$username = "if0_40148312";
$password = "Register890";
$dbname = "if0_40148312_Mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
