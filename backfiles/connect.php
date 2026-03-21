<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "rampus";

$connect = mysqli_connect($host, $username, $password, $database);
if ($connect === false) {
    die("Ошибка: " . mysqli_connect_error());
  } 
$connect->set_charset('utf8mb4');
$connect->query("SET @@lc_time_names = ru_RU;");
