<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require_once('back-files/like-or-dislike.php');
    require_once('back-files/find-user-position-in-top.php');

    $current_user_id = $_SESSION['user']['id'];

    $other_user_username = mysqli_real_escape_string($connect, $_GET['username']);
    $result_other_user_id = $connect->query("SELECT id FROM users WHERE username = '$other_user_username' LIMIT 1");
    if ($result_other_user_id->num_rows > 0) {
        $other_user_id = $result_other_user_id->fetch_assoc()["id"];
    }
    $result_other_user_friends_list = $connect->query("SELECT u.id AS user_id, u.username AS user_username, u.first_name AS user_first_name, u.second_name AS user_second_name, u.avatar AS user_avatar, u.verify_status AS user_verify_status
FROM
(
        SELECT 
            CASE 
                WHEN user_id_1 = $other_user_id THEN user_id_2
                ELSE user_id_1
            END AS friend_id
        FROM friends
        WHERE user_id_1 = $other_user_id OR user_id_2 = $other_user_id
    ) friends   
    JOIN users u ON u.id = friends.friend_id");
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../../css/main.css?v=301">
    <link rel="stylesheet" href="../../css/people.css?v=301">
    <title>Друзья пользователя в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="../../favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicons/favicon-16x16.png">
    <link rel="manifest" href="../../favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Друзья пользователя в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: ../auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/back-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="friends__users">
                            <p>Друзья<span>@<?= $other_user_username ?></span></p>
                            <?php if ($result_other_user_friends_list->num_rows > 0) {
                                echo "<ul>";
                                $counter = $result_other_user_friends_list->num_rows;
                                if ($result_other_user_friends_list->num_rows > 0) {
                                    while ($row_other_user_friends_list = $result_other_user_friends_list->fetch_assoc()) {
                                        $counter -= 1;
                                        $other_user_id = $row_other_user_friends_list['user_id'];
                                        $other_user_in_top = findUserPositionInTop($other_user_id, $connect);
                                        $other_user_username = $row_other_user_friends_list['user_username'];
                                        $other_user_avatar = $row_other_user_friends_list['user_avatar'];
                                        $other_user_first_name = $row_other_user_friends_list['user_first_name'];
                                        $other_user_second_name = $row_other_user_friends_list['user_second_name'];
                                        $other_user_verify_status = $row_other_user_friends_list['user_verify_status'];
                                        echo "<li class='user' onclick='openOtherUserProfileFromOtherProfile(event, `$other_user_username`)'>";
                                        echo "<img class='other-user-avatar' src='../../uploads/avatar/thin_$other_user_avatar'>";
                                        echo "<div class='current-user-info'>";
                                        $trust_mark = $other_user_verify_status ? ' trust' : '';
                                        if ($other_user_first_name || $other_user_second_name) {
                                            echo "<p class='$trust_mark'>$other_user_first_name $other_user_second_name</p>";
                                        }
                                        echo "<p class='$trust_mark'>@$other_user_username</p>";
                                        if ($other_user_verify_status) {
                                            echo "<img class='status' src='../../pics/SuperUserIcon.svg'>";
                                        } else {
                                            switch ($other_user_in_top) {
                                                case 1:
                                                    echo "<img class='status' src='../../pics/BlossomFirstIcon.svg'>";
                                                    break;
                                                case 2:
                                                    echo "<img class='status' src='../../pics/BlossomSecondIcon.svg'>";
                                                    break;
                                                case 3:
                                                    echo "<img class='status' src='../../pics/BlossomThirdIcon.svg'>";
                                                    break;
                                            }
                                        }
                                        echo "</div>";
                                        echo "</li>";
                                        if ($counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                echo "</ul>";
                            } else { ?>
                                <p>У пользователя нет друзей</p>
                            <?php } ?>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                    <div class="third-part">
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../../js/main.js?v=301"></script>
<script src="../../js/people.js?v=301"></script>
</body>

</html>