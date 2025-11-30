<?php
session_start();
require_once('connect.php');

$email_or_username = mysqli_real_escape_string($connect, $_POST['email_or_username']);
$password = md5(mysqli_real_escape_string($connect, $_POST['password']));
$request = $_GET['request'];

if (mysqli_query($connect, "SELECT id FROM `users` LIMIT 1")) {
    $check_user_by_email = mysqli_query($connect, "SELECT * FROM `users` WHERE `email` = '$email_or_username' AND `password` = '$password'");
    $check_user_by_username = mysqli_query($connect, "SELECT * FROM `users` WHERE `username` = '$email_or_username' AND `password` = '$password'");
} else {
    echo '@@@';
    exit();
}

if (mysqli_num_rows($check_user_by_email) > 0) {

    $user = mysqli_fetch_assoc($check_user_by_email);
    $last_auth_date = $user['last_auth_date'];
    $id = $user['id'];
    $username = $user['username'];
    $unread_posts_now = mysqli_query($connect, "SELECT * FROM `posts` WHERE `content_date` >= '$last_auth_date'")->num_rows;
    $unread_posts_db = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `unread_posts` FROM `users` WHERE `id` = $id"));
    $unread_posts = $unread_posts_now + $unread_posts_db['unread_posts'];

    $_SESSION['user'] = [
        "id" => $id,
        "unread_posts" => $unread_posts
    ];

    $connect->query("UPDATE users SET last_auth_date = NOW() WHERE id = $id");
    $connect->query("UPDATE users SET unread_posts = $unread_posts WHERE id = $id");

    echo $username;

} else if (mysqli_num_rows($check_user_by_username) > 0) {

    $user = mysqli_fetch_assoc($check_user_by_username);
    $last_auth_date = $user['last_auth_date'];
    $id = $user['id'];
    $username = $user['username'];
    $unread_posts_now = mysqli_query($connect, "SELECT * FROM `posts` WHERE `content_date` >= '$last_auth_date'")->num_rows;
    $unread_posts_db = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `unread_posts` FROM `users` WHERE `id` = $id"));
    $unread_posts = $unread_posts_now + $unread_posts_db['unread_posts'];

    $_SESSION['user'] = [
        "id" => $user['id'],
        "unread_posts" => $unread_posts
    ];

    $connect->query("UPDATE users SET last_auth_date = NOW() WHERE id = $id");
    $connect->query("UPDATE users SET unread_posts = $unread_posts WHERE id = $id");

    echo $username;

} else {   
    echo '';
}
