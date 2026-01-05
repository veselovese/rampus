<?php
session_start();

require_once('back-files/connect.php');

$username = $_GET['username'];

if (!isset($_SESSION['user'])) {
    header("Location: ../auth");
    exit();
} else {
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require('back-files/get-chat_id.php');
    require('back-files/friends/get-friend-status.php');

    $user_id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $user_id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $current_username = $row["username"];
            $current_first_name = $row["first_name"];
            $current_second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
        }
    }
    if ($username == $current_username) {
        header("Location: ../profile");
        exit();
    }

    $result = $connect->query("SELECT * FROM users WHERE username = '$username'");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $other_id = $row["id"];
            $other_username = $row["username"];
            $other_email = $row["email"];
            $other_description = $row["description"];
            $other_first_name = $row["first_name"];
            $other_second_name = $row["second_name"];
            $other_avatar = $row["avatar"];
        }
    } else {
        header("Location: ../profile");
        exit();
    }

    $unread_posts = $_SESSION['user']['unread_posts'];

    $chat_id = getChatId($user_id, $other_id);
    $friend_status = getFriendStatus($other_id, $connect);
    $user_in_top = findUserPositionInTop($user_id, $connect);
    $other_user_in_top = findUserPositionInTop($other_id, $connect);
    $unread_posts = $_SESSION['user']['unread_posts'];
    $user_level = $connect->query("SELECT blossom_level FROM users WHERE id = '$user_id'")->fetch_assoc()['blossom_level'];
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../css/main.css?v=250">
    <link rel="stylesheet" href="../css/people.css?v=250">
    <link rel="stylesheet" href="../css/chats.css?v=250">
    <title>Чат с пользователем в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="../favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicons/favicon-16x16.png">
    <link rel="manifest" href="../favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Чат с пользователем в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="chat chat-with-user">
                            <div class="user-info">
                                <a class="chat__user-back" href="../chats"><svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                    </svg>
                                    Назад</a>
                                <?php
                                echo "<div class='chat__other-user-info'>";
                                $trust_mark = $other_username == 'rampus' || $other_username == 'help' ? ' trust' : '';
                                if ($other_first_name || $other_second_name) {
                                    echo "<a href='../user/$other_username' class='chat__user-names$trust_mark'>$other_first_name $other_second_name</a>";
                                } else {
                                    echo "<a href='../user/$other_username' class='chat__user-names$trust_mark'>@<span>$other_username</span></a>";
                                }
                                if ($other_username == 'rampus' || $other_username == 'help') { ?>
                                    <img class='menu-status' src="../pics/SuperUserIcon.svg">
                                <?php } else {
                                    switch ($other_user_in_top) {
                                        case 1:
                                            echo "<img class='menu-status' src='../pics/BlossomFirstIcon.svg'>";
                                            break;
                                        case 2:
                                            echo "<img class='menu-status' src='../pics/BlossomSecondIcon.svg'>";
                                            break;
                                        case 3:
                                            echo "<img class='menu-status' src='../pics/BlossomThirdIcon.svg'>";
                                            break;
                                    }
                                }
                                echo "</div>";
                                echo "<a href='../user/$other_username'><img class='chat__user-avatar' src='../uploads/avatar/thin_$other_avatar'></a>"; ?>
                            </div>
                            <div id="success-load-chat" class="loading">
                            </div>
                            <svg id="chat-loading" class="loading" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z" />
                            </svg>
                            <div class="chat__new-message <?php echo $friend_status != 'friends' ? 'disabled' : '' ?>">
                                <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                    <div class="current-message-image-div">
                                        <img class="message-image" id="current-message-image" src="">
                                        <div class="message-image-delete" onclick="clearMessageImage()">
                                            <svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
                                            </svg>
                                        </div>
                                    </div>
                                    <div contenteditable="true" id="textarea-message" role="textbox" onkeyup="textareaMessage(event)" onkeydown="textareaMessagePlaceholder(event)"></div>
                                    <label for="textarea-message" id="textarea-message_label">Чел, ты будешь в шоке..</label>
                                    <input type="hidden" required name="message" id="textarea-message_input" value="">
                                    <input type="hidden" required name="chat_id" id="chatid-message_input" value="<?= $chat_id ?>">
                                    <input type="hidden" required name="user_id_to" id="useridto-message_input" value="<?= $other_id ?>">
                                    <input type="hidden" required name="current-user-id" id="currentuserid_input" value="<?= $user_id ?>">
                                    <input type="file" name="message-image" id="message-image">
                                    <div class="messagearea-buttons">
                                        <div class="message-image-icon disabled">
                                            <svg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M10.0631 2.47923C10.0631 2.47924 10.0631 2.47923 10.0631 2.47923L2.96613 9.8425C1.19829 11.6767 1.19827 14.6607 2.96614 16.4949C4.72012 18.3147 7.55291 18.3147 9.30686 16.4949L16.6998 8.82461C16.9662 8.5482 17.4063 8.54009 17.6827 8.80651C17.9591 9.07293 17.9672 9.51298 17.7008 9.78939L10.3078 17.4597C8.00712 19.8468 4.26589 19.8468 1.96515 17.4597C-0.321693 15.087 -0.321723 11.2504 1.96515 8.87772L9.06213 1.51444C10.6871 -0.171463 13.3326 -0.171475 14.9576 1.51443C16.5687 3.18595 16.5687 5.88581 14.9577 7.55734L7.87205 14.9088C6.92284 15.8936 5.37294 15.8937 4.42377 14.9088C3.48851 13.9384 3.48844 12.3753 4.42377 11.405C4.42378 11.405 4.42376 11.405 4.42377 11.405L10.8834 4.70303C11.1498 4.42662 11.5898 4.41852 11.8663 4.68493C12.1427 4.95135 12.1508 5.3914 11.8844 5.66782L5.42476 12.3698C5.00853 12.8016 5.00845 13.5121 5.42477 13.9441C5.82715 14.3616 6.4686 14.3616 6.87106 13.944C6.87106 13.944 6.87106 13.9441 6.87106 13.944L13.9567 6.59257C15.0487 5.45954 15.0487 3.61228 13.9567 2.47923M10.0631 2.47923C11.1413 1.3606 12.8784 1.36061 13.9567 2.47923Z' />
                                            </svg>
                                            <span>Фото</span>
                                        </div>
                                        <button disabled class="" type="button" id="textarea-message_sumbit">
                                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 14C0 6.26801 6.26801 0 14 0C21.7319 0 28 6.26801 28 14C28 21.7319 21.7319 28 14 28C6.26801 28 0 21.7319 0 14ZM12.6 19.6C12.6 20.3732 13.2268 21 14 21C14.7732 21 15.4 20.3732 15.4 19.6V11.7799L17.2101 13.5899C17.7568 14.1366 18.6432 14.1366 19.1899 13.5899C19.7366 13.0432 19.7366 12.1568 19.1899 11.6101L15.1117 7.5319C15.0907 7.5108 15.0692 7.49043 15.0472 7.47078C14.7907 7.18197 14.4166 7 14 7C13.5834 7 13.2093 7.18197 12.9528 7.47078C12.9308 7.49042 12.9093 7.5108 12.8883 7.5319L8.81005 11.6101C8.26332 12.1568 8.26332 13.0432 8.81005 13.5899C9.35679 14.1366 10.2432 14.1366 10.79 13.5899L12.6 11.7799V19.6Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="third-part">
                        <div>
                            <p class="third-part-title">Чаты</p>
                            <ul class='chats_recent-chats' id="success-recent-chats-widget">
                            </ul>
                        </div>
                    </div>
            </section>
    </main>
<?php } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../js/main.js?v=250"></script>
<script src="../js/chat.js?v=250"></script>
<script src="../js/friends.js?v=250"></script>
</body>

</html>