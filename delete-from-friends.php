<?php
require('connect.php');

$user_id_from = $_POST['id_from'];
$user_id_to = $_POST['id_to'];

if ($connect->query("SELECT id FROM friends WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from)")->num_rows > 0) {
    $connect->query("DELETE FROM friends WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from)");
}

