<?php
session_start();
require('connect.php');

$user_id = $_SESSION['user']['id'];
$post_id = $_GET["post"];
$hashtag_id = $connect->query("SELECT hashtag_id FROM posts WHERE id = $post_id AND user_id = $user_id")->fetch_assoc()['hashtag_id'];

if ($hashtag_id > 0) {
    if ($connect->query("SELECT id FROM posts WHERE hashtag_id = $hashtag_id")->num_rows == 1) {
        $connect->query("DELETE FROM hashtags WHERE id = $hashtag_id");
    }
}
$connect->query("DELETE FROM posts WHERE id = $post_id AND user_id = $user_id");
$connect->query("DELETE FROM comments WHERE post_id = $post_id");


header('Location: ./wall');
