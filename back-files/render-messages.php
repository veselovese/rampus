<?php
session_start();
require_once('connect.php');
$current_user_id = $_SESSION['user']['id'];

if (isset($_POST["username"])) {
    $other_username = $_POST["username"];
    $result = $connect->query("SELECT id FROM users WHERE username = '$other_username'");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $other_user_id = $row["id"];
        }
    }
    $sql_messages = "SELECT *, DATE_FORMAT(send_date, '%e %M') AS send_date_day, DATE_FORMAT(send_date, '%k:%i') AS send_date_time
    FROM messages WHERE (user_id_from = $current_user_id AND user_id_to = $other_user_id) OR (user_id_to = $current_user_id AND user_id_from = $other_user_id) ORDER BY send_date DESC";

    $result_messages = $connect->query($sql_messages);
    if ($result_messages->num_rows > 0) {
        $chat_id = $connect->query("SELECT id FROM chats WHERE (user_id_1 = $other_user_id AND user_id_2 = $current_user_id) OR (user_id_2 = $other_user_id AND user_id_1 = $current_user_id)")->fetch_assoc()['id'];
        $last_send_date_day = '';
        while ($row_messages = $result_messages->fetch_assoc()) {
            $id_from = $row_messages['user_id_from'];
            $id_to = $row_messages['user_id_to'];
            $message = $row_messages['message'];
            $read_status = $row_messages['read_status'];
            $send_date_time = $row_messages['send_date_time'];
            $send_date_day = $row_messages['send_date_day'];
            if (($last_send_date_day != $send_date_day) && ($last_send_date_day != '')) {
                echo "<span class='send_date-day'>$last_send_date_day</span>";
            }
            if ($id_from == $current_user_id) {
                echo "<div class='message your-message'>";
            } else {
                echo "<div class='message other-message'>";
            }
            echo "<p>$message</p>";
            echo "<div class='status-and-date'>";
            echo "<span class='send_date-time'>$send_date_time</span>";
            if ($read_status) {
                echo "<svg class='has-read' width='19' height='11' viewBox='0 0 19 11' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M13.6066 0.197855C13.5033 0.122771 13.3849 0.067211 13.2581 0.0343828C13.1314 0.0015545 12.9988 -0.0078924 12.868 0.00658704C12.7372 0.0210665 12.6109 0.0591858 12.4962 0.118745C12.3816 0.178304 12.2809 0.258124 12.2 0.353598L5.21691 8.59881L1.79518 4.76937C1.71492 4.67125 1.61402 4.58895 1.49845 4.52734C1.38289 4.46572 1.25502 4.42606 1.12245 4.4107C0.989871 4.39534 0.855286 4.4046 0.72668 4.43792C0.598075 4.47125 0.478071 4.52796 0.373791 4.6047C0.269511 4.68143 0.183081 4.77662 0.119632 4.88462C0.0561819 4.99262 0.0170058 5.11123 0.00442861 5.2334C-0.00814854 5.35557 0.00612981 5.47882 0.0464158 5.59583C0.0867018 5.71283 0.152174 5.82122 0.238947 5.91454L4.39888 10.6601C4.49278 10.7664 4.61141 10.852 4.74606 10.9107C4.88071 10.9693 5.02797 10.9996 5.177 10.9991C5.3352 11.0056 5.4928 10.9774 5.63678 10.9168C5.78076 10.8563 5.90698 10.7652 6.005 10.651L13.8161 1.48961C13.8957 1.39252 13.9535 1.28185 13.9861 1.16404C14.0187 1.04624 14.0255 0.923655 14.006 0.803431C13.9865 0.683208 13.9411 0.567749 13.8726 0.463781C13.804 0.359814 13.7136 0.269417 13.6066 0.197855ZM18.5945 0.197855C18.4913 0.122771 18.3728 0.067211 18.2461 0.0343828C18.1193 0.0015545 17.9867 -0.0078924 17.8559 0.00658704C17.7251 0.0210665 17.5988 0.0591858 17.4841 0.118745C17.3695 0.178304 17.2688 0.258124 17.188 0.353598L10.2048 8.59881L9.59631 7.91171L8.33935 9.39585L9.4367 10.651C9.53059 10.7572 9.64922 10.8429 9.78388 10.9015C9.91853 10.9602 10.0658 10.9904 10.2148 10.9899C10.3647 10.9893 10.5124 10.9577 10.6471 10.8974C10.7818 10.8371 10.9 10.7498 10.9929 10.6418L18.804 1.48044C18.8821 1.38364 18.9386 1.27365 18.9704 1.15676C19.0021 1.03987 19.0084 0.918381 18.989 0.799247C18.9695 0.680113 18.9247 0.565674 18.857 0.462479C18.7893 0.359284 18.7001 0.26936 18.5945 0.197855Z' />
                        </svg>";
            } else {
                echo "<svg class='not-read' width='14' height='11' viewBox='0 0 14 11' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M13.5902 0.197855C13.4871 0.122771 13.3688 0.067211 13.2422 0.0343828C13.1155 0.0015545 12.9831 -0.0078924 12.8525 0.00658704C12.7219 0.0210665 12.5957 0.0591858 12.4812 0.118745C12.3667 0.178304 12.2661 0.258124 12.1853 0.353598L5.21062 8.59881L1.79302 4.76937C1.71286 4.67125 1.61207 4.58895 1.49665 4.52734C1.38122 4.46572 1.25351 4.42606 1.1211 4.4107C0.988679 4.39534 0.854256 4.4046 0.725805 4.43792C0.597355 4.47125 0.477495 4.52796 0.373341 4.6047C0.269187 4.68143 0.182861 4.77662 0.119487 4.88462C0.0561142 4.99262 0.0169853 5.11123 0.00442328 5.2334C-0.00813873 5.35557 0.00612243 5.47882 0.0463599 5.59583C0.0865974 5.71283 0.151991 5.82122 0.23866 5.91454L4.39359 10.6601C4.48736 10.7664 4.60585 10.852 4.74034 10.9107C4.87484 10.9693 5.02191 10.9996 5.17077 10.9991C5.32877 11.0056 5.48619 10.9774 5.62999 10.9168C5.7738 10.8563 5.89986 10.7652 5.99777 10.651L13.7995 1.48961C13.879 1.39252 13.9367 1.28185 13.9693 1.16404C14.0019 1.04624 14.0086 0.923655 13.9891 0.803431C13.9697 0.683208 13.9244 0.567749 13.8559 0.463781C13.7874 0.359814 13.6971 0.269417 13.5902 0.197855Z' />
                        </svg>";
            }
            echo "</div>";
            echo "</div>";
            $last_send_date_day = $send_date_day;
        }
        echo "<span class='send_date-day'>$last_send_date_day</span>";

        $connect->query("UPDATE messages
    SET read_status = 1
    WHERE user_id_to = $current_user_id AND chat_id = $chat_id");
    } else {
        echo "<p class='no-dialog'>У вас нет диалога. Начните общение первым!</p>";
    }
}
