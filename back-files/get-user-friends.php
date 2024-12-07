<?php
require_once('connect.php');
$current_user_id = $_SESSION['user']['id'];

$result_friend_1 = $connect->query("SELECT * FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $current_user_id");
$result_friend_2 = $connect->query("SELECT * FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $current_user_id");

$result_request_to = $connect->query("SELECT * FROM requests JOIN users ON requests.user_id_from = users.id WHERE user_id_to = $current_user_id ORDER BY req_date DESC");
