<?php
session_start();
require('connect.php');

if (isset($_POST["id"])) {
    $search_id = $_POST['id'];
    $sql_id = "SELECT users.username AS id
    FROM users WHERE users.username = '$search_id'";
    $result_id = $connect->query($sql_id);
    if ($result_id->num_rows > 0) {
        echo '1';
    } else {
        echo '0';
    }
}

