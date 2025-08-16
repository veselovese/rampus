<?php
session_start();
require_once('connect.php');
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
$counter = count($friends_id) - 1;
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
                echo "<li class='user' onclick='openChatWithUser(event, `$username`)'>";
                echo "<img src='uploads/avatar/thin_$avatar'>";
                echo "<div class='current-user-info'>";
                if ($username == 'rampus') {
                    echo "<p class='rampus'>$first_name $second_name<img src=pics/SuperUserIcon.svg></p>";
                } else {
                    echo "<p>$first_name $second_name</p>";
                }
                echo "<p>@$username</p>";
                echo "</div>";
                echo "</li>";
                if ($counter > 0) {
                    echo "<div class='div-line'></div>";
                }
            }
        }
    }
}
