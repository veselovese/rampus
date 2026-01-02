<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id_1 = $_SESSION['user']['id'];
$user_id_2 = $_POST['user_id_2'];

if ($connect->query("SELECT id FROM friends WHERE (user_id_1 = $user_id_1 AND user_id_2 = $user_id_2) OR (user_id_1 = $user_id_2 AND user_id_2 = $user_id_1)")->num_rows > 0) {
    $connect->query("DELETE FROM friends WHERE (user_id_1 = $user_id_1 AND user_id_2 = $user_id_2) OR (user_id_1 = $user_id_2 AND user_id_2 = $user_id_1)");

    blossoming($user_id_1, 'has-deleted-from-friends', $connect);
    blossoming($user_id_2, 'has-deleted-from-friends', $connect);
}
