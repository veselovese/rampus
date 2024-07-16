<?php
session_start();
require('connect.php');
$result_search_hashtag = '';

if (isset($_POST["hashtag"])) {
    $sql_hashtag = "SELECT hashtags.name AS hashtag_name
    FROM hashtags WHERE hashtags.name LIKE '%". $_POST["hashtag"] . "%'";
} else {
    $sql_hashtag = "SELECT hashtags.name AS hashtag_name
    FROM hashtags";
}

$result_hashtag = $connect->query($sql_hashtag);
if ($result_hashtag->num_rows > 0) {
    while ($row_hashtag = $result_hashtag->fetch_assoc()) {
        $hashtag_name = $row_hashtag['hashtag_name'];
        if (($_POST['get'] == $hashtag_name)) {
            echo "<li id ='checked'><a href='./wall'>#" . $hashtag_name . "</a></li>";
        } else {
            echo "<li><a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></li>";
        }
    }
} else {
    echo "Ничего не найдено";
}
?>