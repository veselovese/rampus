<?php
if (isset($_POST['liked'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT * FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];

    $connect->query("INSERT INTO likes_on_posts (post_id, user_id) VALUES ('$post_id', '$user_id')");
    $connect->query("UPDATE posts SET likes = $likes + 1 WHERE id = $post_id");

    $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];
    $other_blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $other_id")->fetch_assoc()['blossom_level'];
    $other_blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $other_id")->fetch_assoc()['blossom_progress'];
    $other_blossom = $other_blossom_level + $other_blossom_progress / 100;
    $other_blossom += 0.03;

    $other_user_level = intval($other_blossom);
    $other_user_progress = ($other_blossom - $other_user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $other_user_level WHERE id = $other_id");
    $connect->query("UPDATE users SET blossom_progress = $other_user_progress WHERE id = $other_id");

    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;
    $blossom += 0.02;

    $user_level = intval($blossom);
    $user_progress = ($blossom - $user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");

    echo $likes + 1;
    exit();
}

if (isset($_POST['unliked'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT * FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];

    $connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id");
    $connect->query("UPDATE posts SET likes = $likes - 1 WHERE id = $post_id");

    $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];
    $other_blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $other_id")->fetch_assoc()['blossom_level'];
    $other_blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $other_id")->fetch_assoc()['blossom_progress'];
    $other_blossom = $other_blossom_level + $other_blossom_progress / 100;
    $other_blossom -= 0.03;

    $other_user_level = intval($other_blossom);
    $other_user_progress = ($other_blossom - $other_user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $other_user_level WHERE id = $other_id");
    $connect->query("UPDATE users SET blossom_progress = $other_user_progress WHERE id = $other_id");

    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;
    $blossom -= 0.02;

    $user_level = intval($blossom);
    $user_progress = ($blossom - $user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");

    echo $likes - 1;
    exit();
}
