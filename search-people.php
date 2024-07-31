<?php
session_start();
require('connect.php');
$current_user_id = $_SESSION['user']['id'];

if (isset($_POST["people"])) {
    $sql_people = "SELECT *
    FROM users WHERE users.username LIKE '%" . $_POST["people"] . "%' OR users.first_name LIKE '%" . $_POST["people"] . "%' OR users.second_name LIKE '%" . $_POST["people"] . "%'";
} else {
    $sql_people = "SELECT *
    FROM users";
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
            echo "<img class='three-dots show-three-dots-popup' onclick='showPopupOtherUserInfo($id)' src='pics/ThreeDotsIcon.svg'>";
            echo "<div class='three-dots-popup' id='three-dots-popup_other-user-info_$id'>";
            echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToOtherUser($id, `$username`)'>Копировать ссылку</span>";
            echo "<a class='three-dots-popup-li open-profile' href='./user/$username'>Открыть профиль</a>";
            echo "</div>";
            echo "<img src='uploads/avatar/thin_$avatar'>";
            echo "<div class='current-user-info'>";
            echo "<p>$first_name $second_name</p>";
            echo "<p>@$username</p>";
            echo "</div>";
            echo "</li>";
            if ($counter > 0) {
                echo "<div class='div-line'></div>";
            }
        }
    }
} 
