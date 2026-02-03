<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "ecopoint_db";

$conn = new mysqli($host, $username, $password, $db_name);

if($conn->connect_error){
    die("Error:Connected". $conn->connect_error);
}
