<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];
$comment = mysqli_real_escape_string($connect, $_POST['comment']);
$post_id = mysqli_real_escape_string($connect, $_POST['comment_id']);

$connect->query("INSERT INTO comments (post_id, user_id, text) VALUES ($post_id, $user_id, '$comment')");

$other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

blossoming('is-commented-by', $other_id, $connect);
blossoming('has-commented', $user_id, $connect);

header('Location: ../wall#post-' . $post_id);
exit();
