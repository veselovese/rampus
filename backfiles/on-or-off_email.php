<?php
require_once('connect.php');

if (isset($_POST["email"])) {
    $search_email = $_POST['email'];
    $sql_email = "SELECT users.email AS email
    FROM users WHERE users.email = '$search_email'";
    $result_email = $connect->query($sql_email);
    if ($result_email->num_rows > 0) {
        echo '1';
    } else {
        echo '0';
    }
}

