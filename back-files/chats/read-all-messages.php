<?php
session_start();
require_once('../connect.php');
$current_user_id = $_SESSION['user']['id'];
$user_id_to = $_POST['user_id_to'];
$chat_id = $_POST['chat_id'];

if ($current_user_id != $user_id_to) {
    $connect->query("UPDATE messages
    SET read_status = 1
    WHERE user_id_to = $current_user_id AND chat_id = $chat_id");
    echo true;
} else {
    echo false;
}

