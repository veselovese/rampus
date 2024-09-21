<?php
function getUserFriendsId($user_id, $connect)
{
    $result_friend_1 = $connect->query("SELECT user_id_1 FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $user_id");
    $result_friend_2 = $connect->query("SELECT user_id_2 FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $user_id");

    $friends_id = array();
    if ($result_friend_1->num_rows > 0) {
        while ($row_friend = $result_friend_1->fetch_assoc()) {
            $friends_id[] = $row_friend['user_id_1'];
        }
    }
    if ($result_friend_2->num_rows > 0) {
        while ($row_friend = $result_friend_2->fetch_assoc()) {
            $friends_id[] = $row_friend['user_id_2'];
        }
    }

    return $friends_id;
}
