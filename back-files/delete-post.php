<?php
session_start();
require_once('connect.php');

$user_id = $_SESSION['user']['id'];
$post_id = $_GET["post"];
$source = $_GET["source"];
$hashtag_id = $connect->query("SELECT hashtag_id FROM posts WHERE id = $post_id AND user_id = $user_id")->fetch_assoc()['hashtag_id'];

if ($hashtag_id > 0) {
    if ($connect->query("SELECT id FROM posts WHERE hashtag_id = $hashtag_id")->num_rows == 1) {
        $connect->query("DELETE FROM hashtags WHERE id = $hashtag_id");
    }
}

$blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
$blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
$blossom = $blossom_level + $blossom_progress / 100;
$blossom -= 0.1;

$other_users_id_comments = $connect->query("SELECT user_id FROM comments WHERE post_id = $post_id");

if ($other_users_id_comments->num_rows > 0) {
    while ($row = $other_users_id_comments->fetch_assoc()) {
        $other_id = $row['user_id'];
        $blossom -= 0.04;
        
        if ($other_id == $user_id) {
            $blossom -= 0.03;
        }
    }
}

$other_users_id_likes = $connect->query("SELECT user_id FROM likes_on_posts WHERE post_id = $post_id");

if ($other_users_id_likes->num_rows > 0) {
    while ($row = $other_users_id_likes->fetch_assoc()) {
        $other_id = $row['user_id'];
        $blossom -= 0.03;
        
        if ($other_id == $user_id) {
            $blossom -= 0.02;
        }
    }
}

$user_level = intval($blossom);
$user_progress = ($blossom - $user_level) * 100;

$connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
$connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");

$connect->query("DELETE FROM posts WHERE id = $post_id AND user_id = $user_id");
$connect->query("DELETE FROM comments WHERE post_id = $post_id AND user_id = $user_id");
$connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id");

if ($source == 'profile') {
    header('Location: ./profile');
} else if ($source == 'wall') {
    header('Location: ./wall');
}
