<?php
require_once('connect.php');
$current_user_id = $_SESSION['user']['id'];

$result_friend = $connect->query("SELECT u.id AS user_id, u.username AS user_username, u.first_name AS user_first_name, u.second_name AS user_second_name, u.avatar AS user_avatar, u.verify_status AS user_verify_status
FROM
(
        SELECT 
            CASE 
                WHEN user_id_1 = $current_user_id THEN user_id_2
                ELSE user_id_1
            END AS friend_id, friend_date
        FROM friends
        WHERE user_id_1 = $current_user_id OR user_id_2 = $current_user_id
    ) friends   
    JOIN users u ON u.id = friends.friend_id
    ORDER BY friends.friend_date DESC");

$result_request_to = $connect->query("SELECT u.id AS user_id, u.username AS user_username, u.first_name AS user_first_name, u.second_name AS user_second_name, u.avatar AS user_avatar, u.verify_status AS user_verify_status FROM requests JOIN users u ON requests.user_id_from = u.id WHERE user_id_to = $current_user_id ORDER BY req_date DESC");
