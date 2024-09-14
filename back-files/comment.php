<?php
session_start();
require_once('connect.php');

$user_id = $_SESSION['user']['id'];
$comment = $_POST['comment'];
$post_id = $_POST['comment_id'];

$connect->query("INSERT INTO comments (post_id, user_id, text) VALUES ($post_id, $user_id, '$comment')");

$other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];
$other_blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $other_id")->fetch_assoc()['blossom_level'];
$other_blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $other_id")->fetch_assoc()['blossom_progress'];
$other_blossom = $other_blossom_level + $other_blossom_progress / 100;
$other_blossom += 0.04;

$other_user_level = intval($other_blossom);
$other_user_progress = ($other_blossom - $other_user_level) * 100;

$connect->query("UPDATE users SET blossom_level = $other_user_level WHERE id = $other_id");
$connect->query("UPDATE users SET blossom_progress = $other_user_progress WHERE id = $other_id");

$blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
$blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
$blossom = $blossom_level + $blossom_progress / 100;
$blossom += 0.03;

$user_level = intval($blossom);
$user_progress = ($blossom - $user_level) * 100;

$connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
$connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");

header('Location: ../wall#post-' . $post_id);
exit();
