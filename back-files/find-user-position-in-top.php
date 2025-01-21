<?php
function findUserPositionInTop($user_id, $connect) {
    $result_top = $connect->query("SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC");
    $user_in_top = 0;
    if ($result_top->num_rows > 0) {
        while ($row = $result_top->fetch_assoc()) {
            $current_id = $row["id"];
            $user_in_top += 1;
            if ($user_id == $current_id) {
                return $user_in_top;
            }
        }
    }
}