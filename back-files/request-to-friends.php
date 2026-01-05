<?php
session_start();
require_once('connect.php');
require_once('blossoming.php');

$user_id_from = $_SESSION['user']['id'];
$user_id_to = mysqli_real_escape_string($connect, $_POST['user_id_to']);

if ($connect->query("SELECT id FROM requests WHERE user_id_from = $user_id_from AND user_id_to = $user_id_to")->num_rows == 0) {
    $connect->query("INSERT INTO requests (user_id_from, user_id_to) VALUES ($user_id_from, $user_id_to)");
    
    blossoming('request-to-friends', $user_id_from, $connect);
}

