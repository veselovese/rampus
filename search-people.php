<?php
session_start();
require('connect.php');

if (isset($_POST["people"])) {
    $sql_people = "SELECT users.username AS username
    FROM users WHERE users.username LIKE '%" . $_POST["people"] . "%' OR users.first_name LIKE '%" . $_POST["people"] . "%' OR users.second_name LIKE '%" . $_POST["people"] . "%'";
} else {
    $sql_people = "SELECT users.username AS username
    FROM users";
}

$result_people = $connect->query($sql_people);
$counter = $result_people->num_rows;
if ($counter > 0) {
    while ($row_people = $result_people->fetch_assoc()) {
        $counter -= 1;
        $username = $row_people['username'];
        echo "<li><a href='./user/$username'>" . $username . "</a></li>";
        if ($counter > 0) {
            echo "<div class='div-line'></div>";
        }
    }
} else {
    echo "Ничего не найдено";
}
