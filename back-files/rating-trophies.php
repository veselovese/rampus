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
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = $post_id, stat_number = $post_likes, get_date = NOW() WHERE id = 4");
    }
}

# Самый популярный пост по комментариями
$posts = $connect->query("SELECT id FROM posts");
$max_comments = 0;
if ($posts->num_rows > 0) {
    while ($row = $posts->fetch_assoc()) {
        $current_post_id = $row['id'];
        $comment_counter = $connect->query("SELECT user_id FROM comments WHERE post_id = $current_post_id")->num_rows;
        if ($comment_counter > $max_comments) {
            $user_id_to = $connect->query("SELECT user_id FROM comments WHERE post_id = $current_post_id")->fetch_assoc()['user_id'];
            $max_comments = $comment_counter;
            $post_id = $row['id'];
        }
    }
    $trophy_comments = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND description = $max_comments AND id = 5");
    if ($trophy_comments->num_rows == 0) {
        $user_id_to_check = $connect->query("SELECT user_id_to FROM trophies WHERE id = 5")->fetch_assoc()['user_id_to'];
        $user_id_from_check = $connect->query("SELECT user_id_from FROM trophies WHERE id = 5")->fetch_assoc()['user_id_from'];
        $user_id_from = $user_id_to_check != $user_id_to ? $user_id_to_check : $user_id_from_check;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = $post_id, stat_number = $max_comments, get_date = NOW() WHERE id = 5");
    }
}

# Самое большое количество постов
$users = $connect->query("SELECT id FROM users");
$max_posts = 0;

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        $current_user_id = $row['id'];
        $post_counter = $connect->query("SELECT id FROM posts WHERE user_id = $current_user_id")->num_rows;
        if ($post_counter > $max_posts) {
            $user_id_to = $current_user_id;
            $max_posts = $post_counter;
        }
    }
    $trophy_posts = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND id = 6");
    if ($trophy_posts->num_rows == 0) {
        $user_id_to_check = $connect->query("SELECT user_id_to FROM trophies WHERE id = 6")->fetch_assoc()['user_id_to'];
        $user_id_from_check = $connect->query("SELECT user_id_from FROM trophies WHERE id = 6")->fetch_assoc()['user_id_from'];
        $user_id_from = $user_id_to_check != $user_id_to ? $user_id_to_check : $user_id_from_check;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_posts, get_date = NOW() WHERE id = 6");
    }
}

# Самое большое количество друзей
$users = $connect->query("SELECT id FROM users");
$max_friends = 0;

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        $current_user_id = $row['id'];
        $friend_counter = $connect->query("SELECT id FROM friends WHERE user_id_1 = $current_user_id OR user_id_2 = $current_user_id")->num_rows;
        if ($friend_counter > $max_friends) {
            $user_id_to = $current_user_id;
            $max_friends = $friend_counter;
        }
    }
    $trophy_friends = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND id = 7");
    if ($trophy_friends->num_rows == 0) {
        $user_id_to_check = $connect->query("SELECT user_id_to FROM trophies WHERE id = 7")->fetch_assoc()['user_id_to'];
        $user_id_from_check = $connect->query("SELECT user_id_from FROM trophies WHERE id = 7")->fetch_assoc()['user_id_from'];
        $user_id_from = $user_id_to_check != $user_id_to ? $user_id_to_check : $user_id_from_check;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_friends, get_date = NOW() WHERE id = 7");
    }
}
