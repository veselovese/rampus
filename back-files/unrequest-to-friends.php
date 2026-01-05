<?php
session_start();
require_once('connect.php');
require_once('blossoming.php');

$user_id_1 = $_SESSION['user']['id'];
$user_id_2 = mysqli_real_escape_string($connect, $_POST['user_id_2']);

if ($connect->query("SELECT 1 FROM requests WHERE user_id_from = $user_id_2 AND user_id_to = $user_id_1 LIMIT 1")->num_rows > 0) {
    $connect->query("DELETE FROM requests WHERE user_id_from = $user_id_2 AND user_id_to = $user_id_1");

    blossoming('unrequest-to-friends', $user_id_2, $connect);
} else if ($connect->query("SELECT 1 FROM requests WHERE user_id_from = $user_id_1 AND user_id_to = $user_id_2 LIMIT 1")->num_rows > 0) {
    $connect->query("DELETE FROM requests WHERE user_id_from = $user_id_1 AND user_id_to = $user_id_2");

    blossoming('unrequest-to-friends', $user_id_1, $connect);
}
