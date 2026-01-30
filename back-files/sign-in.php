<?php
session_start();
require_once('connect.php');

$email_or_username = mysqli_real_escape_string($connect, $_POST['email_or_username']);
$password = md5(mysqli_real_escape_string($connect, $_POST['password']));
$request = mysqli_real_escape_string($connect, $_GET['request']);

if (mysqli_query($connect, "SELECT id FROM `users` LIMIT 1")) {
    $check_user_by_email = mysqli_query($connect, "SELECT id, username, first_name, second_name, description, avatar, plat_status, verify_status, unread_posts, last_auth_date FROM `users` WHERE `email` = '$email_or_username' AND `password` = '$password' LIMIT 1");
    $check_user_by_username = mysqli_query($connect, "SELECT id, username, first_name, second_name, description, avatar, plat_status, verify_status, unread_posts, last_auth_date FROM `users` WHERE `username` = '$email_or_username' AND `password` = '$password' LIMIT 1");
} else {
    echo '@@@';
    exit();
}

if (mysqli_num_rows($check_user_by_email) > 0 || mysqli_num_rows($check_user_by_username) > 0) {
    if (mysqli_num_rows($check_user_by_email) > 0) {
        $user = mysqli_fetch_assoc($check_user_by_email);
    } else {
        $user = mysqli_fetch_assoc($check_user_by_username);
    }
    $id = $user['id'];
    $first_name = $user['first_name'];
    $second_name = $user['second_name'];
    $description = $user['description'];
    $username = $user['username'];
    $avatar = $user['avatar'];
    $plat_status = $user['plat_status'];
    $verify_status = $user['verify_status'];
    $last_auth_date = $user['last_auth_date'];
    $unread_posts_db = $user['unread_posts'];
    $unread_posts_now = $last_auth_date ? mysqli_query($connect, "SELECT 1 FROM `posts` WHERE `content_date` >= '$last_auth_date'")->num_rows : 0;
    $unread_posts = $unread_posts_now + $unread_posts_db;

    $_SESSION['user'] = [
        "id" => $id,
        "first_name" => $first_name,
        "second_name" => $second_name,
        "description" => $description,
        "username" => $username,
        "avatar" => $avatar,
        "plat_status" => $plat_status,
        "verify_status" => $verify_status,
        "unread_posts" => $unread_posts
    ];

    $connect->query("UPDATE users SET last_auth_date = NOW() WHERE id = $id");
    $connect->query("UPDATE users SET unread_posts = $unread_posts WHERE id = $id");

    echo $username;
} else {
    echo '';
}
