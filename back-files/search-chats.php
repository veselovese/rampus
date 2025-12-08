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
    $sql_chats = "SELECT 
        ch.id AS chat_id,
        friends.friend_id AS interlocutor_id,
        u.id AS user_id, u.first_name AS user_first_name, u.second_name AS user_second_name, u.username AS user_username, u.avatar AS user_avatar, 
        lm.message AS last_message, lm.send_date AS last_message_date, lm.read_status AS last_message_read_status,
    IFNULL(SUM(CASE WHEN m.read_status = 0 AND m.user_id_to = $current_user_id THEN 1 END), 0) AS unread_messages,
    CASE WHEN ch.id IS NOT NULL THEN 1 ELSE 0 END AS chat_exists
    FROM (
        SELECT 
            CASE 
                WHEN user_id_1 = $current_user_id THEN user_id_2
                ELSE user_id_1
            END AS friend_id
        FROM friends
        WHERE user_id_1 = $current_user_id OR user_id_2 = $current_user_id
    ) friends   
    JOIN users u ON u.id = friends.friend_id
    LEFT JOIN chats ch ON 
        (ch.user_id_1 = $current_user_id AND ch.user_id_2 = friends.friend_id) OR
        (ch.user_id_2 = $current_user_id AND ch.user_id_1 = friends.friend_id)
    LEFT JOIN messages lm ON ch.last_message_id = lm.id
    LEFT JOIN messages m ON ch.id = m.chat_id
    GROUP BY 
        friends.friend_id,
        u.id, u.first_name, u.second_name, u.username, u.avatar,
        ch.id, lm.message, lm.send_date, lm.read_status, lm.user_id_from
    ORDER BY 
        CASE WHEN lm.send_date IS NULL THEN 1 ELSE 0 END,
        lm.send_date DESC,
        u.first_name, u.second_name";
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
        $counter -= 1;
        $chat_id = $row_chats['chat_id'];
        $username = $row_chats['user_username'];
        $avatar = $row_chats['user_avatar'];
        $first_name = $row_chats['user_first_name'];
        $second_name = $row_chats['user_second_name'];
        $unread_messages = $row_chats['unread_messages'];
        $read_status = $row_chats['last_message_read_status'];
        $last_message = $row_chats['last_message'];
        $last_message_date = $row_chats['last_message_date'];
        echo "<li class='user' onclick='openChatWithUser(event, `$username`)'>";
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
            echo "</div>";
            echo "<div class='date-and-message-counter'>";
            echo "<span class='last-message-date'>$last_message_date</span>";
            if ($unread_messages > 0) echo "<span class='unread-messages'>$unread_messages</span>";
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
