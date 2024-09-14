<?php
require_once('connect.php');

$users_blossom = $connect->query("SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC LIMIT 3");
if ($users_blossom->num_rows > 0) {
    $level = 0;
    while ($row = $users_blossom->fetch_assoc()) {
        $level += 1;
        $user_id = $row['id'];
        if ($connect->query("SELECT * FROM trophies WHERE user_id = $user_id AND id = $level")->num_rows == 0) {
            $connect->query("UPDATE trophies SET user_id = $user_id, get_date = NOW() WHERE id = $level");
        }
    }
}
