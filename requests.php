<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require_once('back-files/find-user-position-in-top.php');

    $current_user_id = $_SESSION['user']['id'];

    $result_request_to = $connect->query("SELECT u.id, u.username, u.first_name, u.second_name, u.avatar FROM requests r JOIN users u ON r.user_id_from = u.id WHERE user_id_to = $current_user_id");
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300beta">
    <link rel="stylesheet" href="css/people.css?v=300beta">
    <title>Заявки в друзья в Рампус</title>
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Заявки в друзья в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=friends");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/back-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="friends__users">
                            <p>Заявки в друзья</p>
                            <?php if ($result_request_to->num_rows > 0) {
                                echo "<ul>";
                                $counter = $result_request_to->num_rows;
                                while ($row_request = $result_request_to->fetch_assoc()) {
                                    $counter -= 1;
                                    $other_user_id = $row_request['id'];
                                    $other_user_in_top = findUserPositionInTop($other_user_id, $connect);
                                    $other_user_username = $row_request['username'];
                                    $other_user_avatar = $row_request['avatar'];
                                    $other_user_first_name = $row_request['first_name'];
                                    $other_user_second_name = $row_request['second_name'];
                                    echo "<li class='user requests' onclick='openOtherUserProfile(event, `$other_user_username`)'>";
                                    echo "<img class='other-user-avatar' src='uploads/avatar/thin_$other_user_avatar'>";
                                    echo "<div class='current-user-info'>";
                                    $trust_mark = $other_user_username == 'rampus' || $other_user_username == 'help' ? ' trust' : '';
                                    if ($other_user_first_name || $other_user_second_name) {
                                        echo "<p class='$trust_mark'>$other_user_first_name $other_user_second_name</p>";
                                    }
                                    echo "<p class='$trust_mark'>@$other_user_username</p>";
                                    if ($other_user_username == 'rampus' || $other_user_username == 'help') {
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
                                    echo "<div class='answer-to-request-div not-friend' id='other-user-friends-buttons'>";
                                    echo "<button type='button' class='request to-friends hide' id='already-in-friends_$other_user_id'>В друзьях</button>";
                                    echo "<div class='request-buttons'>";
                                    echo "<button type='button' class='request to-friends' id='apply-request-to-friends_$other_user_id'>Принять</button>";
                                    echo "<button type='button' class='request un-to-friends' id='unrequest-to-friends_$other_user_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
                </svg></button>";
                                    echo "<button type='button' class='request sended hide' id='request-sended_$other_user_id'>Отправлена</button>";
                                    echo "<button type='button' class='request un-to-friends hide' id='unsend-request-to-friends_$other_user_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
                        </svg></button>";
                                    echo "</div>";
                                    echo "<button type='button' class='request to-friends hide' id='request-to-friends_$other_user_id'>Отклонена</button>";
                                    echo "</div>";
                                    echo "</li>";
                                    if ($counter > 0) {
                                        echo "<div class='div-line'></div>";
                                    }
                                }
                                echo "</ul>";
                            } else { ?>
                                <p>У тебя ещё нет заявок в друзья</p>
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
<script src="js/main.js?v=300beta"></script>
<script src="js/people.js?v=300beta"></script>
<script src="js/friends.js?v=300beta"></script>
</body>

</html>