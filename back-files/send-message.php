<?php
session_start();
require_once('connect.php');

$user_id_from = $_SESSION['user']['id'];
if (isset($_POST['user_id_to'])) $user_id_to = $_POST['user_id_to'];
if (isset($_POST['message'])) $message = $_POST['message'];

if (isset($_POST['message'])) {
    $user_id_from = mysqli_real_escape_string($connect, $user_id_from);
    $user_id_to = mysqli_real_escape_string($connect, $user_id_to);
    $message = mysqli_real_escape_string($connect, $message);

    $sql = "INSERT INTO messages (user_id_from, user_id_to, message) 
        VALUES ('$user_id_from', '$user_id_to', '$message')";

    $result = mysqli_query($connect, $sql);
    if (!$result) {
        echo "Error: " . mysqli_error($connect);
    } else {
        $_SESSION['message'] = 'Сообщение отправлено';
    }
}
exit();
