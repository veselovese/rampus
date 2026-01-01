<?php
function haveIMakeRepost($repost_id)
{
    require('connect.php');
    $current_user_id = $_SESSION['user']['id'];

    if ($connect->query("SELECT 1 FROM reposts WHERE post_id = $repost_id AND user_id = $current_user_id")->num_rows == 0) {
        return false;
    } else {
        return true;
    }
}
