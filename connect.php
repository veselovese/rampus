<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "rampus";

$connect = mysqli_connect($host, $username, $password, $database);
$connect->set_charset('utf8mb4');
$connect->query("SET @@lc_time_names = ru_RU;");
if (mysqli_connect_errno()) echo mysqli_connect_error(); 
