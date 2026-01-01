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
                                        <div onclick='addImageToMessage()' class="message-image-icon">
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
                        <nav class="first-part-mobile">
                            <ul>
                                <li>
                                    <a href="../wall">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                                            <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                                        </svg>
                                        Стена
                                    </a>
                                </li>
                                <li>
                                    <a href="../users">
                                        <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                            <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                        </svg>
                                        Люди
                                    </a>
                                </li>
                                <li>
                                    <a href="../trophies">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                                        </svg>
                                        Трофеи
                                    </a>
                                </li>
                                <li>
                                    <a href="../profile">
                                        <!-- <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg> -->
                                        <img class="menu-avatar" src="../uploads/avatar/thin_<?= $current_avatar ?>">
                                        Профиль
                                    </a>
                                </li>
                            </ul>
                        </nav>
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