<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];
$comment_id = $_GET['comment'];
$other_id = $connect->query("SELECT user_id FROM comments WHERE id = $comment_id")->fetch_assoc()['user_id'];

$connect->query("DELETE FROM comments WHERE id = $comment_id AND user_id = $user_id");

blossoming($user_id, 'deleted-self-comment', $connect);
blossoming($other_id, 'comment-been-deleted-under-post-by', $connect);

header('Location: ../wall');