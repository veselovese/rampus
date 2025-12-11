<?php

function getFriendStatus($other_id, $connect)
{
    $id = $_SESSION['user']['id'];
    if ($connect->query("SELECT 1 FROM requests WHERE user_id_from = $id AND user_id_to = $other_id LIMIT 1")->num_rows) {
        return "request_from";
    } else if ($connect->query("SELECT 1 FROM requests WHERE user_id_from = $other_id AND user_id_to = $id LIMIT 1")->num_rows) {
        return "request_to";
    } else if ($connect->query("SELECT 1 FROM friends WHERE (user_id_1 = $id AND user_id_2 = $other_id) OR (user_id_2 = $id AND user_id_1 = $other_id) LIMIT 1")->num_rows) {
        return "friends";
    } else {
        return "no-status";
    }
}
