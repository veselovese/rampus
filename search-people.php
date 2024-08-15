<?php
session_start();
require('connect.php');
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
$counter = $result_people->num_rows - 1;
if ($result_people->num_rows > 0) {
    while ($row_people = $result_people->fetch_assoc()) {
        $id = $row_people['id'];
        if ($id != $current_user_id) {
            $counter -= 1;
            $username = $row_people['username'];
            $avatar = $row_people['avatar'];
            $first_name = $row_people['first_name'];
            $second_name = $row_people['second_name'];
            echo "<li class='user' onclick='openOtherUserProfile(event, `$username`)'>";
            echo "<img src='uploads/avatar/thin_$avatar'>";
            echo "<div class='current-user-info'>";
            if ($username == 'rampus') {
                echo "<p class='rampus'>$first_name $second_name<img src=pics/SuperUserIcon.svg></p>";
            } else {
                echo "<p>$first_name $second_name</p>";
            }
            echo "<p>@$username</p>";
            echo "</div>";
            if (in_array($id, $friends_id)) {
                echo "<svg class='friend-status' width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path fill-rule='evenodd' clip-rule='evenodd' d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034ZM13.314 12.1549C13.314 12.1549 13.314 12.1549 13.314 12.1549C13.3224 12.1761 13.331 12.1973 13.3397 12.2184' />
                    <path fill-rule='evenodd' clip-rule='evenodd' d='M17.5 15C19.9852 15 22 12.9852 22 10.5C22 8.01477 19.9852 6 17.5 6C15.0148 6 13 8.01477 13 10.5C13 12.9852 15.0148 15 17.5 15ZM10.0754 16.5754C10.8165 15.8342 11.6572 15.2153 12.5659 14.7316C13.7579 16.1204 15.5262 17 17.5 17C19.4738 17 21.2421 16.1204 22.4341 14.7316C23.3428 15.2153 24.1835 15.8342 24.9246 16.5754C26.8938 18.5446 28 21.2152 28 24H17.5H7C7 21.2152 8.1062 18.5446 10.0754 16.5754Z' />
                    </svg>";
            }
            echo "</li>";
            if ($counter > 0) {
                echo "<div class='div-line'></div>";
            }
        }
    }
}
