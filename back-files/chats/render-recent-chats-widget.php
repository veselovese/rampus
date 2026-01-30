<?php
session_start();
require_once('../connect.php');
require('../find-user-position-in-top.php');
date_default_timezone_set('Europe/Moscow');
$today = date('Y-m-d', time());
$yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
$beforeyesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
$month_list = array(1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10', 11 => '11', 12 => '12');
$weeks_list = array(0 => 'вс', 1 => 'пн', 2 => 'вт', 3 => 'ср', 4 => 'чт', 5 => 'пт', 6 => 'сб');
$current_user_id = $_SESSION['user']['id'];
if (isset($_POST['user_id_to'])) $user_id_to = $_POST['user_id_to'];

$sql_chats = "SELECT 
    ch.id AS chat_id,
    CASE 
        WHEN ch.user_id_1 = $current_user_id THEN ch.user_id_2
        ELSE ch.user_id_1
    END AS interlocutor_id,
    u.id AS user_id, u.first_name AS user_first_name, u.second_name AS user_second_name, 
    u.username AS user_username, u.plat_status AS user_plat_status, u.verify_status AS user_verify_status, u.avatar AS user_avatar, 
    lm.message AS last_message, lm.send_date AS last_message_date, 
    lm.read_status AS last_message_read_status,
    IFNULL(SUM(CASE WHEN m.read_status = 0 AND m.user_id_to = $current_user_id THEN 1 END), 0) AS unread_messages,
    CASE WHEN ch.id IS NOT NULL THEN 1 ELSE 0 END AS chat_exists
    FROM (
        SELECT DISTINCT user_id FROM (
            SELECT 
                CASE 
                    WHEN user_id_1 = $current_user_id THEN user_id_2
                    ELSE user_id_1
                END AS user_id
            FROM friends
            WHERE (user_id_1 = $current_user_id OR user_id_2 = $current_user_id)

            UNION ALL

            SELECT 
                id AS user_id
            FROM users
            WHERE verify_status = true 
                AND id != $current_user_id

            UNION ALL

            SELECT DISTINCT
                CASE 
                    WHEN m.user_id_from = $current_user_id THEN m.user_id_to
                    ELSE m.user_id_from
                END AS user_id
            FROM messages m
            WHERE m.user_id_from = $current_user_id OR m.user_id_to = $current_user_id
        ) combined_ids
    ) friends   
    JOIN users u ON u.id = friends.user_id
    LEFT JOIN chats ch ON 
        (ch.user_id_1 = $current_user_id AND ch.user_id_2 = friends.user_id) OR
        (ch.user_id_2 = $current_user_id AND ch.user_id_1 = friends.user_id)
    LEFT JOIN messages lm ON ch.last_message_id = lm.id
    LEFT JOIN messages m ON ch.id = m.chat_id
    GROUP BY 
        friends.user_id,
        u.id, u.first_name, u.second_name, u.username, u.avatar,
        ch.id, lm.message, lm.send_date, lm.read_status, lm.user_id_from
    ORDER BY 
        CASE WHEN lm.send_date IS NULL THEN 1 ELSE 0 END,
        lm.send_date DESC,
        u.first_name, u.second_name LIMIT 5";

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
$counter = min(count($friends_id) - 1, 5);
if ($result_chats->num_rows > 0) {
    while ($row_chats = $result_chats->fetch_assoc()) {
        $counter -= 1;
        $user_id = $row_chats['user_id'];
        $user_in_top = findUserPositionInTop($user_id, $connect);
        $chat_id = $row_chats['chat_id'];
        $username = $row_chats['user_username'];
        $verify_status = $row_chats['user_verify_status'];
        $avatar = $row_chats['user_avatar'];
        $first_name = $row_chats['user_first_name'];
        $second_name = $row_chats['user_second_name'];
        $unread_messages = $row_chats['unread_messages'];
        $read_status = $row_chats['last_message_read_status'];
        $last_message = $row_chats['last_message'];
        $last_message_date = $row_chats['last_message_date'];
        $current = $user_id_to == $user_id ? ' current' : '';
        echo "<li>";
        echo "<a class='recent-chat$current' href='$username'>";
        echo "<img src='../uploads/avatar/thin_$avatar'>";
        echo "<div class='current-chat-info'>";
        echo "<div class='recent-chat__user-info'>";
        echo "<div class='recent-chat__user-name-and-status'>";
        $trust_mark = $verify_status ? 'trust' : '';
        if ($first_name || $second_name) {
            echo "<p class='recent-main-name $trust_mark'>$first_name $second_name</p>";
        } else {
            echo "<p class='recent-main-name $trust_mark'>@<span>$username</span></p>";
        }
        if ($verify_status) { ?>
            <img class='status' src="../pics/SuperUserIcon.svg">
<?php } else {
            switch ($user_in_top) {
                case 1:
                    echo "<img class='status' src='../pics/BlossomFirstIcon.svg'>";
                    break;
                case 2:
                    echo "<img class='status' src='../pics/BlossomSecondIcon.svg'>";
                    break;
                case 3:
                    echo "<img class='status' src='../pics/BlossomThirdIcon.svg'>";
                    break;
            }
        }
        echo "</div>";
        if ($last_message) {
            $massage_date_db = date_format(date_create($last_message_date), 'Y-m-d');
            switch ($massage_date_db) {
                case $today:
                    $last_message_date = date_format(date_create($last_message_date), 'G:i');
                    break;
                case $yesterday:
                    $last_message_date = $weeks_list[date_format(date_create($last_message_date), 'w')];
                    break;
                case $beforeyesterday:
                    $last_message_date = $weeks_list[date_format(date_create($last_message_date), 'w')];
                    break;
                default:
                    $last_message_date = date_format(date_create($last_message_date), 'j.') . $month_list[date_format(date_create($last_message_date), 'n')];
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
        echo "</a>";
        echo "</li>";
        if ($counter > 0) {
            echo "<div class='div-line'></div>";
        }
    }
}
