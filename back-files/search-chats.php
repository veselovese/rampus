<?php
session_start();
require_once('connect.php');
date_default_timezone_set('Europe/Moscow');
$today = date('Y-m-d', time());
$yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
$beforeyesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
$month_list = array(1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
$current_user_id = $_SESSION['user']['id'];

if (isset($_POST["people"])) {
    $sql_chats = "SELECT *
    FROM users WHERE users.username LIKE '%" . $_POST["people"] . "%' OR users.first_name LIKE '%" . $_POST["people"] . "%' OR users.second_name LIKE '%" . $_POST["people"] . "%' ORDER BY first_name";
} else {
    $sql_chats = "SELECT ch.id AS chat_id, ch.user_id_1, ch.user_id_2, ch.last_message, ch.last_message_date,
    u1.id AS user1_id, u1.first_name AS user1_first_name, u1.second_name AS user1_second_name, u1.username AS user1_username, u1.avatar AS user1_avatar,
    u2.id AS user2_id, u2.first_name AS user2_first_name, u2.second_name AS user2_second_name, u2.username AS user2_username, u2.avatar AS user2_avatar, 
    IFNULL(m.chat_id, UUID()) as group_chat_id, COUNT(m.chat_id) AS unread_messages
    FROM chats ch
    JOIN messages m ON ch.id = m.chat_id AND m.read_status = 0  
    RIGHT JOIN users u1 ON ch.user_id_1 = u1.id
    RIGHT JOIN users u2 ON ch.user_id_2 = u2.id
    WHERE (u1.id = $current_user_id OR u2.id = $current_user_id)
    AND ((u1.id OR u2.id IN (SELECT user_id_1 FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $current_user_id))
    OR (u1.id OR u2.id IN (SELECT user_id_2 FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $current_user_id)))
    GROUP BY group_chat_id
    ORDER BY last_message_date DESC";
}

$result_friend_1 = $connect->query("SELECT user_id_1 FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $current_user_id");
$result_friend_2 = $connect->query("SELECT user_id_2 FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $current_user_id");
$friends_id = array();
if ($result_friend_1->num_rows > 0) {
    while ($row_friend = $result_friend_1->fetch_assoc()) {
        $friends_id[] = $row_friend['user_id_1'];
    }
}
if ($result_friend_2->num_rows > 0) {
    while ($row_friend = $result_friend_2->fetch_assoc()) {
        $friends_id[] = $row_friend['user_id_2'];
    }
}

$result_chats = $connect->query($sql_chats);
$counter = count($friends_id);
if ($result_chats->num_rows > 0) {
    while ($row_chats = $result_chats->fetch_assoc()) {
        $user1_id = $row_chats['user1_id'];
        $user2_id = $row_chats['user2_id'];
        $i = ($user1_id && $user1_id == $current_user_id || !$user1_id) ? 'user2' : 'user1';
        $counter -= 1;
        $chat_id = $row_chats['group_chat_id'];
        $username = $row_chats[$i . '_username'];
        $avatar = $row_chats[$i . '_avatar'];
        $first_name = $row_chats[$i . '_first_name'];
        $second_name = $row_chats[$i . '_second_name'];
        $unread_messages = $row_chats['unread_messages'];
        $last_message = $row_chats['last_message'];
        $last_message_date = $row_chats['last_message_date'];
        echo "<li class='user' onclick='openChatWithUser(event, `$username`)'>";
        echo $chat_id;
        echo "<img src='uploads/avatar/thin_$avatar'>";
        echo "<div class='current-chat-info'>";
        echo "<div class='current-user-info'>";
        if ($username == 'rampus') {
            echo "<p class='rampus'>$first_name $second_name<img src=pics/SuperUserIcon.svg></p>";
        } else {
            if ($first_name || $second_name) {
                echo "<p class='chat__user-info'>$first_name $second_name</p>";
            } else {
                echo "<p class='chat__user-info'>@<span>$username</span></p>";
            }
        }
        if ($last_message) {
            $massage_date_db = date_format(date_create($last_message_date), 'Y-m-d');
            switch ($massage_date_db) {
                case $today:
                    $last_message_date = date_format(date_create($last_message_date), 'G:i');
                    break;
                case $yesterday:
                    $last_message_date = date_format(date_create($last_message_date), 'вчера');
                    break;
                case $beforeyesterday:
                    $last_message_date = date_format(date_create($last_message_date), 'позавчера');
                    break;
                default:
                    $last_message_date = date_format(date_create($last_message_date), 'j ') . $month_list[date_format(date_create($last_message_date), 'n')];
                    break;
            }
            echo "<span>$last_message</span>";
            echo "<span>непроч: $unread_messages</span>";
            echo "</div>";
            echo "<div class='date-and-message-counter'>";
            echo "<span class='last-message-date'>$last_message_date</span>";
            echo "</div>";
        } else {
            echo "<span class='no-message-yet'>Сообщений ещё нет</span>";
            echo "</div>";
        }
        echo "</div>";
        echo "</li>";
        if ($counter > 0) {
            echo "<div class='div-line'></div>";
        }
    }
}
