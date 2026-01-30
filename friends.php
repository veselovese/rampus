<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $current_user_id = $_SESSION['user']['id'];

    $friends_counter = $result_friend->num_rows;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300before">
    <link rel="stylesheet" href="css/people.css?v=300before">
    <title>Друзья в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Друзья пользователя в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="friends__users">
                            <p>Друзья<span><?= $friends_counter ?></span></p>
                            <?php if ($result_request_to->num_rows > 0) { ?>
                                <div class="request_to">
                                    <div class="request_to-div">
                                        <a href="./requests" class="friends-title link">Заявки в друзья<span style="margin-left: 0.625rem;"><?= $result_request_to->num_rows ?></span></a>
                                        <a href="./requests.php" class="open-requests-page">
                                            Все
                                            <svg class='pointer' width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="users-requests">
                                        <?php
                                        $requests_counter = 0;
                                        while ($row_request = $result_request_to->fetch_assoc()) {
                                            $other_user_id = $row_request['user_id'];
                                            $other_user_in_top = findUserPositionInTop($other_user_id, $connect);
                                            $other_user_username = $row_request['user_username'];
                                            $other_user_avatar = $row_request['user_avatar'];
                                            $other_user_first_name = $row_request['user_first_name'];
                                            $other_user_second_name = $row_request['user_second_name'];
                                            $other_user_verify_status = $row_request['user_verify_status'];
                                            if ($requests_counter < 3) {
                                                echo "<div class='request-from-user' onclick='openOtherUserProfile(event, `$other_user_username`)'>";
                                                echo "<div class='only-user-info'>";
                                                echo "<img class='other-user-avatar' src='uploads/avatar/thin_$other_user_avatar'>";
                                                echo "<div class='user-info'>";
                                                $trust_mark = $other_user_verify_status ? ' trust' : '';
                                                if ($other_user_first_name || $other_user_second_name) {
                                                    echo "<p class='$trust_mark'>$other_user_first_name $other_user_second_name</p>";
                                                }
                                                echo "<p class='$trust_mark'>@$other_user_username</p>";
                                                if ($other_user_verify_status) {
                                                    echo "<img class='status' src='pics/SuperUserIcon.svg'>";
                                                } else {
                                                    switch ($other_user_in_top) {
                                                        case 1:
                                                            echo "<img class='status' src='pics/BlossomFirstIcon.svg'>";
                                                            break;
                                                        case 2:
                                                            echo "<img class='status' src='pics/BlossomSecondIcon.svg'>";
                                                            break;
                                                        case 3:
                                                            echo "<img class='status' src='pics/BlossomThirdIcon.svg'>";
                                                            break;
                                                    }
                                                }
                                                echo "</div>";
                                                echo "</div>";
                                                echo "<div class='answer-to-requests' id='popup_answer-to-request_$other_user_id'>";
                                                echo "<span class='answer-to-requests-li request' id='add-to-friends_$other_user_id' onclick='addToFriendsRequestPage($other_user_id)'><svg width='17' height='12' viewBox='0 0 17 12' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M1 6.45458L5.62964 11L15.8148 1' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/>
</svg>
Принять</span>";
                                                echo "<span class='answer-to-requests-li unrequest' id='unrequest-from-friends_$other_user_id' onclick='unrequestToFriendsRequestPage($other_user_id)'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg>
</span>";
                                                echo "</div>";
                                                echo "</div>";
                                            } else {
                                                $requests_counter++;
                                                break;
                                            }
                                            $requests_counter++;
                                        } ?>
                                    </div>

                                </div>
                            <?php } ?>
                            <?php if ($result_friend->num_rows > 0) {
                                echo "<ul>";
                                if ($result_friend->num_rows > 0) {
                                    while ($row_friend = $result_friend->fetch_assoc()) {
                                        $friends_counter -= 1;
                                        $other_user_id = $row_friend['user_id'];
                                        $other_user_in_top = findUserPositionInTop($other_user_id, $connect);
                                        $other_user_username = $row_friend['user_username'];
                                        $avatar = $row_friend['user_avatar'];
                                        $other_user_first_name = $row_friend['user_first_name'];
                                        $other_user_second_name = $row_friend['user_second_name'];
                                        $other_user_verify_status = $row_friend['user_verify_status'];
                                        echo "<li class='user' onclick='openOtherUserProfile(event, `$other_user_username`)'>";
                                        echo "<img class='other-user-avatar' src='uploads/avatar/thin_$avatar'>";
                                        echo "<div class='current-user-info'>";
                                        $trust_mark = $other_user_verify_status ? ' trust' : '';
                                        if ($other_user_first_name || $other_user_second_name) {
                                            echo "<p class='$trust_mark'>$other_user_first_name $other_user_second_name</p>";
                                        }
                                        echo "<p class='$trust_mark'>@$other_user_username</p>";
                                        if ($other_user_verify_status) {
                                            echo "<img class='status' src='pics/SuperUserIcon.svg'>";
                                        } else {
                                            switch ($other_user_in_top) {
                                                case 1:
                                                    echo "<img class='status' src='pics/BlossomFirstIcon.svg'>";
                                                    break;
                                                case 2:
                                                    echo "<img class='status' src='pics/BlossomSecondIcon.svg'>";
                                                    break;
                                                case 3:
                                                    echo "<img class='status' src='pics/BlossomThirdIcon.svg'>";
                                                    break;
                                            }
                                        }
                                        echo "</div>";
                                        echo "</li>";
                                        if ($friends_counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                echo "</ul>";
                            } else { ?>
                                <p>Не расстраивайся, но у тебя нет друзей</p>
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
<script src="js/main.js?v=300before"></script>
<script src="js/people.js?v=300before"></script>
<script src="js/friends.js?v=300before"></script>
</body>

</html>