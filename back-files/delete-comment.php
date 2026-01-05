<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];
$comment_id = mysqli_real_escape_string($connect, $_POST['comment_id']);
$other_id = $connect->query("SELECT user_id FROM comments WHERE id = $comment_id")->fetch_assoc()['user_id'];

$connect->query("DELETE FROM comments WHERE id = $comment_id AND user_id = $user_id");

blossoming('delete-self-comment', $user_id,  $connect);
blossoming('comment-deleted-under-post-by', $other_id, $connect);
