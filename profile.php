<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/like-or-dislike.php');
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $email = $row["email"];
            $description = $row["description"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $avatar = $row["avatar"];
        }
    }

    $sql = "SELECT posts.likes AS post_likes
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $id";
    $sql_comment_counter = "SELECT comments.id
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    JOIN users ON users.id = posts.user_id
                    WHERE posts.user_id = $id";
    $sql_commented_counter = "SELECT comments.id
                    FROM comments
                    WHERE comments.user_id = $id";
    $sql_liked_counter = "SELECT likes_on_posts.id
                    FROM likes_on_posts
                    WHERE likes_on_posts.user_id = $id";
    $result = $connect->query($sql);
    $posts_count = $result->num_rows;
    $comment_count = $connect->query($sql_comment_counter)->num_rows;
    $commented_count = $connect->query($sql_commented_counter)->num_rows;
    $liked_count = $connect->query($sql_liked_counter)->num_rows;
    $likes_count = 0;
    if ($posts_count > 0) {
        while ($row = $result->fetch_assoc()) {
            $post_likes = $row["post_likes"];
            $likes_count += $post_likes;
        }
    }

    $blossom = ($posts_count + $likes_count * 0.3 + $comment_count * 0.4 + $liked_count * 0.2 + $commented_count * 0.3 + ($result_friend_1->num_rows + $result_friend_2->num_rows) * 0.7) / 10;
    $user_level = intval($blossom);
    $user_progress = round($blossom - $user_level, 2) * 100;
    $user_level += 1;
    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $id");

    $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
    $result_top = $connect->query($sql_top);
    $top_count = 0;
    if ($result_top->num_rows > 0) {
        while ($row = $result_top->fetch_assoc()) {
            $current_id = $row["id"];
            $top_count += 1;
            if ($current_id == $id) {
                break;
            }
        }
    }

    $sql_trophies = "SELECT * FROM trophies WHERE user_id = $id";
    $result_trophies = $connect->query($sql_trophies);
    $result_trophies_m = $connect->query($sql_trophies);

    $unread_posts = $_SESSION['user']['unread_posts'];
    $posts_counter = $connect->query("SELECT * FROM posts WHERE user_id = $id")->num_rows;
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=200">
    <link rel="stylesheet" href="css/profile.css?v=200">
    <title>Профиль в Rampus (Рампус)</title>
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
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Профиль пользователя в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile" class="menu-profile">
                                <?php if ($username == 'rampus') { ?>
                                    <img class='menu-status' src="pics/SuperUserIcon.svg">
                                <?php } else {
                                    switch ($top_count) {
                                        case 1:
                                            echo "<img class='menu-status' src='pics/BlossomFirstIcon.svg'>";
                                            break;
                                        case 2:
                                            echo "<img class='menu-status' src='pics/BlossomSecondIcon.svg'>";
                                            break;
                                        case 3:
                                            echo "<img class='menu-status' src='pics/BlossomThirdIcon.svg'>";
                                            break;
                                    }
                                } ?>
                                <img class="menu-avatar" src="uploads/avatar/thin_<?= $avatar ?>">
                                <div>
                                    <p><?= $first_name ?></p>
                                    <p>@<?= $username ?></p>
                                </div>
                            </a></li>
                        <p class="menu-title">Общение</p>
                        <li><a href="./wall"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' clip-rule='evenodd' d='M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z' />
                                    <path d='M12 7H23C25.2091 7 27 8.79086 27 11V23H12C9.79086 23 8 21.2091 8 19V11C8 8.79086 9.79086 7 12 7Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Стена
                                <?php if ($unread_posts > 0) { ?>
                                    <span class="notification-in-menu"><?= $unread_posts ?></span>
                                <?php } ?>
                            </a></li>
                        <li><a href="./people"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Люди</a></li>
                        <li><a href="./friends"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linejoin='round' />
                                </svg>
                                Друзья
                                <?php if ($result_request_to->num_rows > 0) { ?>
                                    <span class="notification-in-menu"><?= $result_request_to->num_rows ?></span>
                                <?php } ?>
                            </a></li>
                        <p class="menu-title">Возможности</p>
                        <li><a href="./edit">
                                <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' clip-rule='evenodd' d='M10.6852 0.137016C10.3343 0 9.88958 0 9.00001 0C8.11043 0 7.66569 0 7.31479 0.137016C6.84701 0.319707 6.47535 0.670122 6.28158 1.11117C6.19313 1.31251 6.1585 1.54665 6.14496 1.88819C6.12506 2.39011 5.85204 2.8547 5.39068 3.10584C4.92933 3.35696 4.36608 3.34759 3.89509 3.11288C3.57459 2.95317 3.34221 2.86436 3.11304 2.83592C2.61103 2.7736 2.10333 2.90186 1.70162 3.19248C1.40034 3.41044 1.17795 3.7736 0.733186 4.49991L0.733167 4.49994L0.733124 4.50001C0.288375 5.2263 0.0659982 5.58944 0.0164284 5.94442C-0.0496573 6.41772 0.08638 6.89639 0.394624 7.27515C0.53531 7.44804 0.733043 7.5933 1.03993 7.7751C1.49108 8.0424 1.78135 8.49771 1.78133 9C1.7813 9.50229 1.49103 9.95751 1.03992 10.2247C0.732995 10.4066 0.535234 10.552 0.394529 10.7248C0.0862847 11.1036 -0.0497527 11.5822 0.0163426 12.0555C0.0659044 12.4105 0.288293 12.7737 0.733071 13.5L0.73355 13.5008C1.17802 14.2266 1.40036 14.5896 1.70152 14.8074C2.10323 15.098 2.61093 15.2263 3.11295 15.164C3.3421 15.1356 3.57447 15.0467 3.89494 14.8871C4.36596 14.6524 4.92925 14.643 5.39064 14.8941C5.85203 15.1453 6.12506 15.6099 6.14496 16.1119C6.15851 16.4534 6.19313 16.6875 6.28158 16.8889C6.39517 17.1474 6.56988 17.3748 6.78926 17.5555C7.30922 16.6913 7.93605 15.8862 8.66116 15.1611C9.51192 14.3104 10.4728 13.5949 11.5102 13.0285C11.1816 12.2513 11 11.3969 11 10.5C11 7.08819 13.6286 4.29036 16.9711 4.02119C16.7064 3.60165 16.5246 3.35618 16.2985 3.19254C15.8968 2.90192 15.389 2.77366 14.887 2.83598C14.6579 2.86442 14.4256 2.95322 14.105 3.11292C13.634 3.34763 13.0707 3.35702 12.6094 3.10586C12.1479 2.85472 11.8749 2.39009 11.8551 1.88815C11.8415 1.54663 11.8069 1.3125 11.7184 1.11117C11.5247 0.670122 11.153 0.319707 10.6852 0.137016ZM14.1455 13.4996C14.109 13.4588 14.0732 13.4173 14.0382 13.3752Z' />
                                    <path d='M21.25 10.5C21.25 12.5711 19.5711 14.25 17.5 14.25C15.4289 14.25 13.75 12.5711 13.75 10.5C13.75 8.42893 15.4289 6.75 17.5 6.75C19.5711 6.75 21.25 8.42893 21.25 10.5ZM10.6057 17.1057C11.2822 16.4292 12.0479 15.8623 12.8752 15.4166C14.0826 16.5527 15.7103 17.25 17.5 17.25C19.2897 17.25 20.9174 16.5527 22.1248 15.4166C22.9521 15.8623 23.7177 16.4292 24.3943 17.1057C26.0452 18.7566 27.0429 20.9385 27.2211 23.25H17.5L7.77887 23.25C7.95711 20.9385 8.95483 18.7566 10.6057 17.1057Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Настройки
                                <svg class="pointer" width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg></a></li>
                        <li><a id="exit" href="./exit"><svg width='26' height='26' viewBox='0 0 26 26' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M13 17.5L17.5 13M17.5 13L13 8.5M17.5 13H1M1 5.87203V5.8003C1 4.12014 1 3.27943 1.32698 2.6377C1.61459 2.07321 2.07321 1.61459 2.6377 1.32698C3.27943 1 4.12014 1 5.8003 1H20.2003C21.8805 1 22.7194 1 23.3611 1.32698C23.9256 1.61459 24.3857 2.07321 24.6733 2.6377C25 3.2788 25 4.11848 25 5.79536V20.2054C25 21.8823 25 22.7208 24.6733 23.3619C24.3857 23.9263 23.9256 24.3857 23.3611 24.6733C22.72 25 21.8815 25 20.2047 25H5.79536C4.11848 25 3.2788 25 2.6377 24.6733C2.07321 24.3857 1.61459 23.9259 1.32698 23.3614C1 22.7197 1 21.8801 1 20.2V20.125' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Выйти</a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <div class="profile-back"></div>
                            <div class="profile-userinfo">
                                <img class="avatar" src="uploads/avatar/small_<?= $avatar ?>">
                                <div class="textinfo">
                                    <p class='first-and-second-names'><?= $first_name . " " . $second_name ?></p>
                                    <div>
                                        <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $username ?>")'>@<?= $username ?></p>
                                        <span id="copy-link-status">Копировать ссылку</span>
                                    </div>
                                    <?php if ($description != '') { ?>
                                        <p class="description"><?= $description ?></p>
                                    <?php } else { ?>
                                        <a class="description" href="./edit">Добавить описание</a>
                                    <?php } ?>
                                </div>
                                <div class='div-show-three-dots-popup in-profile' onclick='showPopupUserInfo(<?= $id ?>)' id='div-show-three-dots-popup_$i'>
                                    <img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>
                                </div>
                                <div class='three-dots-popup' id='three-dots-popup_user-info'>
                                    <span class='three-dots-popup-li copy-link' onclick='copyLinkToUser("<?= $username ?>")'>Копировать ссылку</span>
                                    <a class='three-dots-popup-li edit-profile' href='edit'>Редактировать</a>
                                    <a class='three-dots-popup-li exit-profile' href='exit'>Выйти</a>
                                </div>
                            </div>
                        </div>
                        <a href="./blossom" class="blossom-level mobile">
                            <div class="blossom-title">
                                <img src="pics/BlossomIcon.svg">
                                Цветение
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                            </div>
                            <div class="progress-div">
                                <progress value="<?= $user_progress ?>" max="100"></progress>
                                <span class="progress" style="--r:<?= $user_progress ?>%"><?= $user_progress ?>%</span>
                            </div>
                            <div class="level">
                                <span><?= $user_level ?> уровень</span>
                                <span><?= $user_level + 1 ?></span>
                            </div>
                        </a>
                        <a href="./trophy" class="case mobile">
                            <div class="case-title">
                                <img src="pics/CaseIcon.svg">
                                Трофеи
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                            </div>
                            <div class="case-trophies">
                                <?php if ($result_trophies_m->num_rows > 0) {
                                    while ($row = $result_trophies_m->fetch_assoc()) {
                                        $trophy_name_m = $row["name"];
                                        $trophy_description_m = $row["description"];
                                        $trophy_image_m = $row["image"];
                                        echo "<div class='trophy'>";
                                        echo "<img src='$trophy_image_m'>";
                                        echo "<span>$trophy_description_m</span>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<span class='trophy'>Нет трофеев</span>";
                                }
                                ?>
                            </div>
                        </a>
                        <div class="user-friends">
                            <div class="section" onclick="openFriendsPage(event)">
                                <div class="friends-info">
                                    <img src="pics/FriendsIcon.svg">
                                    <p>Друзья</p>
                                    <div>
                                        <span><?= $result_friend_1->num_rows + $result_friend_2->num_rows ?></span>
                                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                        </svg>
                                    </div>
                                </div>
                                <?php if (($result_friend_1->num_rows > 0) || ($result_friend_2->num_rows > 0)) {
                                    echo "<div class='friends'>";
                                    if ($result_friend_1->num_rows > 0) {
                                        while ($row_friend_1 = $result_friend_1->fetch_assoc()) {
                                            $friend_username = $row_friend_1["username"];
                                            $avatar = $row_friend_1["avatar"];
                                            $first_name = $row_friend_1["first_name"];
                                            echo "<a class='current-friend' href='./user/$friend_username'>";
                                            echo "<img src='uploads/avatar/thin_$avatar'>";
                                            if ($friend_username == 'rampus') {
                                                echo "<p class='rampus'>$first_name</p>";
                                            } else {
                                                echo "<p>$first_name</p>";
                                            }
                                            echo "</a>";
                                        }
                                    }
                                    if ($result_friend_2->num_rows > 0) {
                                        while ($row_friend_2 = $result_friend_2->fetch_assoc()) {
                                            $friend_username = $row_friend_2["username"];
                                            $avatar = $row_friend_2["avatar"];
                                            $first_name = $row_friend_2["first_name"];
                                            echo "<a class='current-friend' href='./user/$friend_username'>";
                                            echo "<img src='uploads/avatar/thin_$avatar'>";
                                            if ($friend_username == 'rampus') {
                                                echo "<p class='rampus'>$first_name</p>";
                                            } else {
                                                echo "<p>$first_name</p>";
                                            }
                                            echo "</a>";
                                        }
                                    }
                                    echo "</div>";
                                }
                                echo "</div>";
                                if ($result_request_to->num_rows > 0) { ?>
                                    <div class='div-line'></div>
                                    <a class="requests" href="./requests">
                                        <p>Заявки</p>
                                        <div>
                                            <span><?= $result_request_to->num_rows ?></span>
                                            <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                            </svg>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="third-part-mobile">
                                <div class="profile__counters">
                                    <div class="counters-title">
                                        <img src="pics/ParamIcon.svg">
                                        Показатели
                                    </div>
                                    <div class="profile__counters-div">
                                        <div class="profile__posts">
                                            Посты
                                            <span> <?= $posts_count ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__likes">
                                            Лайки
                                            <span><?= $likes_count ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Комментарии
                                            <span><?= $comment_count ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile__user-posts">
                                <div id="yours-posts-div">
                                    <p>Ваши посты<span><?= $posts_counter ?></span></p>
                                    <button class="create-new-post" id="create-new-post">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.34788 13.1488C6.34789 13.509 6.63983 13.8009 7.00002 13.8009C7.36021 13.8009 7.6522 13.5089 7.65219 13.1488L6.34788 13.1488ZM7.65217 7C7.65217 6.6398 7.36023 6.34786 7.00004 6.34786C6.63984 6.34786 6.3479 6.6398 6.3479 7H7.65217ZM6.3479 7C6.3479 7.3602 6.63984 7.65214 7.00004 7.65214C7.36023 7.65214 7.65217 7.3602 7.65217 7H6.3479ZM7.65219 0.85125C7.6522 0.491061 7.36021 0.199068 7.00002 0.199074C6.63983 0.19908 6.34789 0.491024 6.34788 0.851213L7.65219 0.85125ZM7.00004 6.34786C6.63984 6.34786 6.3479 6.6398 6.3479 7C6.3479 7.3602 6.63984 7.65214 7.00004 7.65214V6.34786ZM13.1488 7.65214C13.509 7.65214 13.8009 7.36019 13.8009 7C13.8009 6.63981 13.509 6.34786 13.1488 6.34786V7.65214ZM7.00004 7.65214C7.36023 7.65214 7.65217 7.3602 7.65217 7C7.65217 6.6398 7.36023 6.34786 7.00004 6.34786V7.65214ZM0.851267 6.34782C0.491085 6.34782 0.199092 6.63982 0.199092 7C0.199092 7.36018 0.491085 7.65218 0.851268 7.65218L0.851267 6.34782ZM7.65219 13.1488L7.65217 7H6.3479L6.34788 13.1488L7.65219 13.1488ZM7.65217 7L7.65219 0.85125L6.34788 0.851213L6.3479 7H7.65217ZM7.00004 7.65214H13.1488V6.34786H7.00004V7.65214ZM7.00004 6.34786L0.851267 6.34782L0.851268 7.65218L7.00004 7.65214V6.34786Z" />
                                        </svg>
                                        <span id="create-new-post-title">Написать пост</span>
                                    </button>
                                </div>
                                <div class="profile__new-post" id="profile__new-post">
                                    <form action="./back-files/add" method="post" autocomplete="off">
                                        <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                        <label for="textarea-post" id="textarea-post_label">Что-то ещё не рассказали?</label>
                                        <input type="hidden" required name="post" id="textarea-post_input" value="">
                                        <input type="hidden" required name="post-source" value="source-profile">
                                        <button disabled class="" type="submit" id="textarea-post_sumbit"><img src="pics/SendIcon.svg"></button>
                                    </form>
                                </div>
                                <div id="success-render-posts">
                                </div>
                            </div>
                            <nav class="first-part-mobile">
                                <ul>
                                    <li><a href="./wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                                <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                            </svg>
                                            Стена
                                        </a></li>
                                    <li><a href="./profile">
                                            <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                            </svg>
                                            ****</a></li>
                                    <li><a href="./people">
                                            <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                            </svg>
                                            Люди</a></li>
                                    <li id="active"><a href="./profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                                <circle cx="14" cy="6" r="6" />
                                            </svg>
                                            Профиль
                                        </a></li>
                                </ul>
                            </nav>
                        </div>
                        <div class="third-part">
                            <div>
                                <a href="./blossom" class="blossom-level">
                                    <div class="blossom-title">
                                        <img src="pics/BlossomIcon.svg">
                                        Цветение
                                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-div">
                                        <progress value="<?= $user_progress ?>" max="100"></progress>
                                        <span class="progress" style="--r:<?= $user_progress ?>%"><?= $user_progress ?>%</span>
                                    </div>
                                    <div class="level">
                                        <span><?= $user_level ?> уровень</span>
                                        <span><?= $user_level + 1 ?></span>
                                    </div>
                                </a>
                                <a href="./trophy" class="case">
                                    <div class="case-title">
                                        <img src="pics/CaseIcon.svg">
                                        Трофеи
                                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                        </svg>
                                    </div>
                                    <div class="case-trophies">
                                        <?php if ($result_trophies->num_rows > 0) {
                                            while ($row = $result_trophies->fetch_assoc()) {
                                                $trophy_name = $row["name"];
                                                $trophy_description = $row["description"];
                                                $trophy_image = $row["image"];
                                                echo "<div class='trophy'>";
                                                echo "<img src='$trophy_image'>";
                                                echo "<span>$trophy_description</span>";
                                                echo "</div>";
                                            }
                                        } else {
                                            echo "<span class='trophy'>Нет трофеев</span>";
                                        }
                                        ?>
                                    </div>
                                </a>
                                <div class="profile__counters">
                                    <div class="counters-title">
                                        <img src="pics/ParamIcon.svg">
                                        Показатели
                                    </div>
                                    <div class="profile__counters-div">
                                        <div class="profile__posts">
                                            Посты
                                            <span> <?= $posts_count ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__likes">
                                            Лайки
                                            <span><?= $likes_count ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Комментарии
                                            <span><?= $comment_count ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=200"></script>
<script src="js/profile.js?v=200"></script>
</body>

</html>