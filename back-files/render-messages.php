<?php
session_start();
require_once('connect.php');
$current_user_id = $_SESSION['user']['id'];

if (isset($_POST["username"])) {
    $other_username = $_POST["username"];
    $result = $connect->query("SELECT * FROM users WHERE username = '$other_username'");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $other_user_id = $row["id"];
        }
    }
    $sql_messages = "SELECT *, DATE_FORMAT(send_date, '%e %M') AS send_date_day, DATE_FORMAT(send_date, '%k:%i') AS send_date_time
    FROM messages WHERE (user_id_from = $current_user_id AND user_id_to = $other_user_id) OR (user_id_to = $current_user_id AND user_id_from = $other_user_id) ORDER BY send_date DESC";
}
$result_messages = $connect->query($sql_messages);
if ($result_messages->num_rows > 0) {
    $last_send_date_day = '';
    while ($row_messages = $result_messages->fetch_assoc()) {
        $id_from = $row_messages['user_id_from'];
        $id_to = $row_messages['user_id_to'];
        $message = $row_messages['message'];
        $send_date_time = $row_messages['send_date_time'];
        $send_date_day = $row_messages['send_date_day'];
        if (($last_send_date_day != $send_date_day) && ($last_send_date_day != '')) {
            echo "<span class='send_date-day'>$last_send_date_day</span>";
        }
        if ($id_from == $current_user_id) {
            echo "<div class='message your-message'>";
            echo "<p>$message</p>";
            echo "<span class='send_date-time'>$send_date_time</span>";
            echo "</div>";
        } else if ($id_from == $other_user_id) {
            echo "<div class='message other-message'>";
            echo "<p>$message</p>";
            echo "<span class='send_date-time'>$send_date_time</span>";
            echo "</div>";
        }
        $last_send_date_day = $send_date_day;
    }
    echo "<span class='send_date-day'>$last_send_date_day</span>";
} else {
    echo "<p class='no-dialog'>У вас нет диалога. Начните общение первым!</p>";
}
