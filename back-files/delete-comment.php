<?php
session_start();
require_once('connect.php');

$user_id = $_SESSION['user']['id'];
$comment_id = $_GET['comment'];

$connect->query("DELETE FROM comments WHERE id = $comment_id AND user_id = $user_id");

header('Location: ../wall');