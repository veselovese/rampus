<?php
session_start();
require_once('connect.php');

$email_or_username = $_POST['email_or_username'];
$password = md5($_POST['password']);
$request = $_GET['request'];

$check_user_by_email = mysqli_query($connect, "SELECT * FROM `users` WHERE `email` = '$email_or_username' AND `password` = '$password'");
$check_user_by_username = mysqli_query($connect, "SELECT * FROM `users` WHERE `username` = '$email_or_username' AND `password` = '$password'");
if (mysqli_num_rows($check_user_by_email) > 0) {

    $user = mysqli_fetch_assoc($check_user_by_email);
    $last_auth_date = $user['last_auth_date'];
    $unchecked_posts = mysqli_query($connect, "SELECT * FROM `posts` WHERE `post_date` >= '$last_auth_date'")->num_rows;
    
    $_SESSION['user'] = [
        "id" => $user['id'],
        "unchecked_posts" => $unchecked_posts
    ];
    $id = $user['id'];
    $connect->query("UPDATE users SET last_auth_date = NOW() WHERE id = $id");
    
    if ($request == 'wall') {
        header('Location: ../wall');
        exit();
    } else {
        header('Location: ../profile');
        exit();
    }
} else if (mysqli_num_rows($check_user_by_username) > 0) {
    
    $user = mysqli_fetch_assoc($check_user_by_username);
    $last_auth_date = $user['last_auth_date'];
    $unchecked_posts = mysqli_query($connect, "SELECT * FROM `posts` WHERE `post_date` >= '$last_auth_date'")->num_rows;
    
    $_SESSION['user'] = [
        "id" => $user['id'],
        "unchecked_posts" => $unchecked_posts
    ];
    $id = $user['id'];
    $connect->query("UPDATE users SET last_auth_date = NOW() WHERE id = $id");
    
    if ($request == 'wall') {
        header('Location: ../wall');
        exit();
    } else {
        header('Location: ../profile');
        exit();
    }
} else {
    $_SESSION['message'] = 'Неверный логин или пароль';
    header('Location: ../auth');
    exit();
}
