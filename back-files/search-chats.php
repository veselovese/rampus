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
    $sql_people = "SELECT *
    FROM users WHERE users.username LIKE '%" . $_POST["people"] . "%' OR users.first_name LIKE '%" . $_POST["people"] . "%' OR users.second_name LIKE '%" . $_POST["people"] . "%' ORDER BY first_name";
} else {
    $sql_people = "SELECT *
    FROM users ORDER BY first_name";
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

$result_people = $connect->query($sql_people);
$counter = count($friends_id);
if ($result_people->num_rows > 0) {
    while ($row_people = $result_people->fetch_assoc()) {
        $id = $row_people['id'];
        if ($id != $current_user_id) {
            if (in_array($id, $friends_id)) {
                $counter -= 1;
                $username = $row_people['username'];
                $avatar = $row_people['avatar'];
                $first_name = $row_people['first_name'];
                $second_name = $row_people['second_name'];
                $sql_last_message = "SELECT message, send_date FROM messages WHERE (user_id_from = $current_user_id AND user_id_to = $id) OR (user_id_to = $current_user_id AND user_id_from = $id) ORDER BY send_date DESC LIMIT 1";
                $result_last_message = $connect->query($sql_last_message);
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
                if ($result_last_message->num_rows > 0) {
                    while ($row_last_message = $result_last_message->fetch_assoc()) {
                        $last_message = $row_last_message['message'];
                        $last_message_date = $row_last_message['send_date'];
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
                        echo "</div>";
                    }
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
    }
}
