<?php
$servername = "localhost";
$username = "root";
$password = "391S=O9/mJm+";
$database = "db_kurse";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>