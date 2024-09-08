<?php
session_start();
require('connect.php');

$first_name = $_POST['first_name'];
$second_name = $_POST['second_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$password_1 = $_POST['password_1'];
$password_2 = $_POST['password_2'];

$resultUsername = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
$resultEmail = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");

if (!preg_match('/^(?=.*[0-9])(?=.*).{8,}$/', $password_1) or strlen($password_1)<8) {
    $_SESSION['message'] = 'Пароль минимум 8 символов, должны быть числа, знаки !? и буквы латинского алфавита';
    header('Location: ./reg');
} else if (mysqli_num_rows($resultUsername) > 0) {
    $_SESSION['message'] = 'Такой логин уже существует';
    header('Location: ./reg');
} else if (mysqli_num_rows($resultEmail) > 0) {
    $_SESSION['message'] = 'Такая почта уже зарегистрирована';
    header('Location: ./reg');
} else if ($password_1 === $password_2) {
    $password = md5($password_1);
    mysqli_query($connect, "INSERT INTO `users` (`id`, `first_name`, `second_name`, `username`, `email`, `password`) VALUES (NULL, '$first_name', '$second_name', '$username', '$email', '$password')");

    $_SESSION['message'] = 'Регистрация прошла успешно';
    header('Location: ./');

} else {
    $_SESSION['message'] = 'Пароли не совпадают';
    header('Location: ./reg');
}