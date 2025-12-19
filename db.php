<?php
$servername = "localhost";
$username = "root"; // default XAMPP
$password = "";     // default XAMPP
$dbname = "eventdecoration";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>
