<?php
session_start();
require('connect.php');

$user_id = $_SESSION['user']['id'];
$comment = $_POST['comment'];
$comment_id = $_POST['comment_id'];

$connect->query("INSERT INTO comments (post_id, user_id, text) VALUES ($comment_id, $user_id, '$comment')");

header('Location: ./wall#post-' . $comment_id);
