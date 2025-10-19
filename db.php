<?php
$host = "localhost";
$user = "remote_user"; // your db user
$password = "password"; // your db password
$dbname = "pawpet";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>