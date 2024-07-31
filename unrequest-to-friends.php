<?php
require('connect.php');

$user_id_from = $_POST['id_from'];
$user_id_to = $_POST['id_to'];

if ($connect->query("SELECT id FROM requests WHERE user_id_from = $user_id_from AND user_id_to = $user_id_to")->num_rows > 0) {
    $connect->query("DELETE FROM requests WHERE user_id_from = $user_id_from AND user_id_to = $user_id_to");
}

