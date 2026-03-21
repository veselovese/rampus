<?php
session_start();
require_once('../connect.php');

$current_user_id = $_SESSION['user']['id'];

$current_user_unread_messages_counter = $connect->query("SELECT COUNT(*) AS unread_chats FROM chats ch JOIN messages m ON m.chat_id = ch.id AND (m.read_status = false AND m.user_id_to = $current_user_id) WHERE user_id_1 = $current_user_id OR user_id_2 = $current_user_id GROUP BY ch.id")->num_rows;
echo $current_user_unread_messages_counter == 0 ? '' : $current_user_unread_messages_counter;
