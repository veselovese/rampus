<?php
function getChatId($user_id_1, $user_id_2)
{
    require('connect.php');
    $result_chat_id = $connect->query("SELECT id
    FROM chats
    WHERE (user_id_1 = $user_id_1 AND user_id_2 = $user_id_2) OR (user_id_1 = $user_id_2 AND user_id_2 = $user_id_1)");
    if ($result_chat_id->num_rows > 0) {
        return $result_chat_id->fetch_assoc()['id'];
    }
    return 0;
}
