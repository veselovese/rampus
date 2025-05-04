<?php
require_once('connect.php');

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$resultUsername = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
$resultEmail = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");

if (mysqli_num_rows($resultEmail) > 0) {
    echo "emailAlready";
} else if (mysqli_num_rows($resultUsername) > 0) {
    echo "usernameAlready";
} else {
    $password = md5($password);
    mysqli_query($connect, "INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES (NULL, '$username', '$email', '$password')");
    echo "done";  
}