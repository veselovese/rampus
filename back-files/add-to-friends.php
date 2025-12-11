<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id_1 = $_SESSION['user']['id'];
$user_id_2 = $_POST['user_id_2'];

if ($connect->query("SELECT 1 FROM friends WHERE (user_id_1 = $user_id_1 AND user_id_2 = $user_id_2) OR (user_id_1 = $user_id_2 AND user_id_2 = $user_id_1) LIMIT 1")->num_rows == 0) {
    $connect->query("INSERT INTO friends (user_id_1, user_id_2) VALUES ($user_id_1, $user_id_2)");
    $connect->query("DELETE FROM requests WHERE (user_id_from = $user_id_1 AND user_id_to = $user_id_2) OR (user_id_from = $user_id_2 AND user_id_to = $user_id_1)");

    blossoming($user_id_1, 'request-to-friends', $connect);
    blossoming($user_id_2, 'request-to-friends', $connect);
}

