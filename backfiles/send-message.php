<?php
session_start();
require_once('connect.php');
require('friends/get-friend-status.php');
$user_id_from = $_SESSION['user']['id'];
if (isset($_POST['user_id_to'])) $user_id_to = $_POST['user_id_to'];
if (isset($_POST['message'])) $message = $_POST['message'];
$friend_status = getFriendStatus($user_id_to, $connect);

if ($user_id_to && $message) {
    $user_id_from = mysqli_real_escape_string($connect, $user_id_from);
    $user_id_to = mysqli_real_escape_string($connect, $user_id_to);

    $sql = "INSERT INTO chats (user_id_1, user_id_2)
    SELECT $user_id_from, $user_id_to 
    WHERE NOT EXISTS (
    SELECT 1 FROM chats WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from))";
    $result = mysqli_query($connect, $sql);

    $user_id_from = mysqli_real_escape_string($connect, $user_id_from);
    $user_id_to = mysqli_real_escape_string($connect, $user_id_to);
    $message = mysqli_real_escape_string($connect, $message);

    $chat_id = $connect->query("SELECT id FROM chats WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from) LIMIT 1")->fetch_assoc()['id'];

    $sql_message = "INSERT INTO messages (user_id_from, user_id_to, message, chat_id) 
        VALUES ('$user_id_from', '$user_id_to', '$message', '$chat_id')";

    $result_message = mysqli_query($connect, $sql_message);
    $message_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];

    if (!$result) {
        echo "Error: " . mysqli_error($connect);
    } else {
        $_SESSION['message'] = 'Сообщение отправлено';
    }

    $sql_chat = "UPDATE chats SET last_message_id = '$message_id' WHERE (user_id_1 = $user_id_from AND user_id_2 = $user_id_to) OR (user_id_1 = $user_id_to AND user_id_2 = $user_id_from) LIMIT 1";
    $result_chat = mysqli_query($connect, $sql_chat);
    if (!$result) {
        echo "Error: " . mysqli_error($connect);
    } else {
        $_SESSION['message'] = 'Чат обновлен';
    }
}
exit();
