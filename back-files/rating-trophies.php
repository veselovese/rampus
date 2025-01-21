<?php
require_once('connect.php');

# Места в рейтинге
$users_blossom = $connect->query("SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC LIMIT 3");
if ($users_blossom->num_rows > 0) {
    $level = 0;
    while ($row = $users_blossom->fetch_assoc()) {
        $level += 1;
        $user_id_to = $row['id'];
        $trophy_rating = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND id = $level");
        if ($trophy_rating->num_rows == 0) {
            $user_id_from = $connect->query("SELECT user_id_to FROM trophies WHERE id = $level")->fetch_assoc()['user_id_to'];
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, get_date = NOW() WHERE id = $level");
        }
    }
}

# Самый популярный пост по лайкам
$popular_likes = $connect->query("SELECT id, user_id, likes FROM posts ORDER BY likes DESC LIMIT 1");
if ($popular_likes->num_rows > 0) {
    $row = $popular_likes->fetch_assoc();
    $user_id_to = $row['user_id'];
    $post_id = $row['id'];
    $post_likes = $row['likes'];
    $trophy_likes = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND description = $post_likes AND id = 4");
    if ($trophy_likes->num_rows == 0) {
        $user_id_to_check = $connect->query("SELECT user_id_to FROM trophies WHERE id = 4")->fetch_assoc()['user_id_to'];
        $user_id_from_check = $connect->query("SELECT user_id_from FROM trophies WHERE id = 4")->fetch_assoc()['user_id_from'];
        $user_id_from = $user_id_to_check != $user_id_to ? $user_id_to_check : $user_id_from_check;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = $post_id, description = $post_likes, get_date = NOW() WHERE id = 4");
    }
}
