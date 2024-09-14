<?php
require_once('connect.php');

$user_id_from = $_POST['id_from'];
$user_id_to = $_POST['id_to'];

if ($connect->query("SELECT id FROM friends WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from)")->num_rows > 0) {
    $connect->query("DELETE FROM friends WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from)");

    $blossom_level_1 = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id_from")->fetch_assoc()['blossom_level'];
    $blossom_progress_1 = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id_from")->fetch_assoc()['blossom_progress'];
    $blossom_1 = $blossom_level_1 + $blossom_progress_1 / 100;
    $blossom_1 -= 0.07;

    $user_level_1 = intval($blossom_1);
    $user_progress_1 = ($blossom_1 - $user_level_1) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level_1 WHERE id = $user_id_from");
    $connect->query("UPDATE users SET blossom_progress = $user_progress_1 WHERE id = $user_id_from");

    $blossom_level_2 = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id_to")->fetch_assoc()['blossom_level'];
    $blossom_progress_2 = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id_to")->fetch_assoc()['blossom_progress'];
    $blossom_2 = $blossom_level_2 + $blossom_progress_2 / 100;
    $blossom_2 -= 0.07;

    $user_level_2 = intval($blossom_2);
    $user_progress_2 = ($blossom_2 - $user_level_2) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level_2 WHERE id = $user_id_to");
    $connect->query("UPDATE users SET blossom_progress = $user_progress_2 WHERE id = $user_id_to");
}

