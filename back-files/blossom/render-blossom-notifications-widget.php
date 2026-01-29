<?php
session_start();
require_once('../connect.php');

date_default_timezone_set('Europe/Moscow');
$today = date('Y-m-d', time());
$yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
$beforeyesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
$month_list = array(1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
$weeks_list = array(0 => 'вс', 1 => 'пн', 2 => 'вт', 3 => 'ср', 4 => 'чт', 5 => 'пт', 6 => 'сб');

$current_user_id = $_SESSION['user']['id'];

$sql_blossom_notifications = "SELECT action_type, blossom_change, notify_date FROM blossom_notifications WHERE user_id = $current_user_id ORDER BY notify_date DESC LIMIT 5";

$result_blossom_notifications = $connect->query($sql_blossom_notifications);
if ($result_blossom_notifications->num_rows > 0) {
    while ($row_blossom_notifications = $result_blossom_notifications->fetch_assoc()) {
        $notify_action_type = $row_blossom_notifications['action_type'];
        $notify_blossom_change = $row_blossom_notifications['blossom_change'];
        $notify_date = $row_blossom_notifications['notify_date'];
        $notify_date_db = date_format(date_create($notify_date), 'Y-m-d');
        switch ($notify_date_db) {
            case $today:
                $notify_date = date_format(date_create($notify_date), 'сегодня в G:i');
                break;
            case $yesterday:
                $notify_date = $weeks_list[date_format(date_create($notify_date), 'вчера в G:i')];
                break;
            case $beforeyesterday:
                $notify_date = $weeks_list[date_format(date_create($notify_date), 'позавчера в G:i')];
                break;
            default:
                $notify_date = date_format(date_create($notify_date), 'j ') . $month_list[date_format(date_create($notify_date), 'n')] . date_format(date_create($notify_date), ' в G:i');
                break;
        }
        switch ($notify_action_type) {
            case 'request-to-friends':
                $notify_name = 'Вы отправили заявку в друзья';
                break;
            case 'unrequest-to-friends':
                $notify_name = 'Отменили заявку в друзья';
                break;
            case 'add-to-friends':
                $notify_name = 'Добавили в друзья';
                break;
            case 'delete-from-friends':
                $notify_name = 'Удалили из друзей';
                break;
            case 'add-post':
                $notify_name = 'Вы сделали новый пост';
                break;
            case 'delete-post':
                $notify_name = 'Вы удалили пост';
                break;
            case 'has-commented':
                $notify_name = 'Вы оставили комментарий';
                break;
            case 'delete-self-comment':
                $notify_name = 'Вы удалили комментарий';
                break;
            case 'is-commented-by':
                $notify_name = 'Вас прокомментировали';
                break;
            case 'comment-deleted-under-post-by':
                $notify_name = 'Удалили комментарий под вашим постом';
                break;
            case 'like-post':
                $notify_name = 'Вы лайкнули пост';
                break;
            case 'dislike-post':
                $notify_name = 'Вы дизлайкнули пост';
                break;
            case 'is-liked-by':
                $notify_name = 'Вас лайкнули';
                break;
            case 'is-disliked-by':
                $notify_name = 'Вас дизлайкнули';
                break;
            case 'repost-post':
                $notify_name = 'Вы сделали репост';
                break;
            case 'unrepost-post':
                $notify_name = 'Вы удалили репост';
                break;
            case 'is-reposted-by':
                $notify_name = 'Вас репостнули';
                break;
            case 'is-unreposted-by':
                $notify_name = 'Удалили репост вашего поста';
                break;
            case 'grab-trophy':
                $notify_name = 'Вы забрали трофей';
                break;
            case 'lose-trophy':
                $notify_name = 'Вы потеряли трофей';
                break;
        }
        $plus_or_minus = $notify_blossom_change > 0 ? 'plus' : 'minus';
        echo "<li class='blossom-notify $plus_or_minus'>";
        echo "<div class='blossom-notify__info'>";
        echo "<p>$notify_name</p>";
        echo "<p>$notify_date</p>";
        echo "</div>";
        echo "<span class='blossom-notify__blossom-change $plus_or_minus'>";
        echo $plus_or_minus == 'plus' ? '+' . $notify_blossom_change : $notify_blossom_change;
        echo "</span>";
        echo "</li>";
    }
} else {
    echo "<li class='blossom-notify'>";
    echo "<div class='blossom-notify__info'>";
    echo "<p>Изменений ещё не было</p>";
    echo "<p>Но они скоро появятся</p>";
    echo "</div>";
    echo "</li>";
}
