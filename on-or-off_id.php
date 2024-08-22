<?php
session_start();
require('connect.php');

if (isset($_POST["id"])) {
    $search_id = $_POST['id'];
    if (preg_match('/[^a-zA-Z0-9_]/', $search_id)) {
        echo 'rus';
    } else if (preg_match('/[a-zA-Z0-9_]{17,}/', $search_id)) {
        echo 'length';
    } else {
        $sql_id = "SELECT users.username AS id
        FROM users WHERE users.username = '$search_id'";
        $result_id = $connect->query($sql_id);
        if ($result_id->num_rows > 0) {
            echo 'have';
        } else {
            echo '0';
        }
    }
}
