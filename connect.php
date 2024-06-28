<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "rampus";

$connect = mysqli_connect($host, $username, $password, $database);
$connect->set_charset('utf8mb4');
if (mysqli_connect_errno()) echo mysqli_connect_error(); 
