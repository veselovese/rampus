<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];
$post_id = $_POST["post_id"];
$hashtag_id = $connect->query("SELECT hashtag_id FROM posts WHERE id = $post_id AND user_id = $user_id")->fetch_assoc()['hashtag_id'];

if ($hashtag_id > 0) {
    if ($connect->query("SELECT id FROM posts WHERE hashtag_id = $hashtag_id")->num_rows == 1) {
        $connect->query("DELETE FROM hashtags WHERE id = $hashtag_id");
    }
}

blossoming($user_id, 'deleted-self-post', $connect);

$other_users_id_comments = $connect->query("SELECT user_id FROM comments WHERE post_id = $post_id");

if ($other_users_id_comments->num_rows > 0) {
    while ($row = $other_users_id_comments->fetch_assoc()) {
        $other_id = $row['user_id'];

        blossoming($other_id, 'deleted-self-comment', $connect);
        blossoming($user_id, 'comment-been-deleted-under-post-by', $connect);

        $connect->query("DELETE FROM comments WHERE post_id = $post_id AND user_id = $other_id");
    }
}

$other_users_id_likes = $connect->query("SELECT user_id FROM likes_on_posts WHERE post_id = $post_id");

if ($other_users_id_likes->num_rows > 0) {
    while ($row = $other_users_id_likes->fetch_assoc()) {
        $other_id = $row['user_id'];

        blossoming($other_id, 'disliked-post', $connect);
        blossoming($user_id, 'is-disliked-by', $connect);
        
        $connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $other_id");
    }
}

$connect->query("DELETE FROM posts WHERE id = $post_id AND user_id = $user_id");
