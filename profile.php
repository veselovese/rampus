<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/rating-trophies.php');

    require_once('back-files/like-or-dislike.php');
    require_once('back-files/find-user-position-in-top.php');
    require_once('back-files/get-user-friends.php');

    $current_user_id = $_SESSION['user']['id'];
    $current_user_username = $_SESSION['user']['username'];
    $current_user_description = $_SESSION['user']['description'];
    $current_user_first_name = $_SESSION['user']['first_name'];
    $current_user_second_name = $_SESSION['user']['second_name'];
    $current_user_avatar = $_SESSION['user']['avatar'];

    $result_current_user_blossom = $connect->query("SELECT blossom_level, blossom_progress FROM users WHERE id = $current_user_id LIMIT 1");
    if ($result_current_user_blossom->num_rows > 0) {
        $row_current_user_blossom = $result_current_user_blossom->fetch_assoc();
        $current_user_blossom_level = $row_current_user_blossom["blossom_level"];
        $current_user_blossom_progress = $row_current_user_blossom["blossom_progress"];
    }

    $sql_current_user_posts_and_likes_counter = "SELECT SUM(posts.likes) AS current_user_likes_counter, COUNT(*) AS current_user_posts_counter
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $current_user_id";
    $result_current_user_posts_and_likes_counter = $connect->query($sql_current_user_posts_and_likes_counter);
    if ($result_current_user_posts_and_likes_counter->num_rows > 0) {
        $row_current_user_posts_and_likes_counter = $result_current_user_posts_and_likes_counter->fetch_assoc();
        $current_user_posts_counter = $row_current_user_posts_and_likes_counter["current_user_posts_counter"];
        $current_user_likes_counter = $row_current_user_posts_and_likes_counter["current_user_likes_counter"];
    }

    $sql_current_user_comments_counter = "SELECT 1
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    WHERE posts.user_id = $current_user_id";
    $current_user_comments_counter = $connect->query($sql_current_user_comments_counter)->num_rows;

    $sql_current_user_trophies_list = "SELECT name, description, image FROM trophies WHERE user_id_to = $current_user_id";
    $result_current_user_trophies_list = $connect->query($sql_current_user_trophies_list);
    $result_current_user_trophies_list_mobile = $connect->query($sql_current_user_trophies_list);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/profile.css?v=250">
    <title>Профиль в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Профиль в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <div class="profile-back">
                                <?php require_once('components/profile-back.php'); ?>
                            </div>
                            <div class="profile-userinfo">
                                <img class="avatar" src="uploads/avatar/small_<?= $current_user_avatar ?>">
                                <div class="textinfo">
                                    <?php if ($current_user_first_name && $current_user_second_name) { ?>
                                        <p class='first-and-second-names'><?= $current_user_first_name . " " . $current_user_second_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<?= $current_user_username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else if ($current_user_first_name) { ?>
                                        <p class='first-and-second-names'><?= $current_user_first_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<?= $current_user_username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else if ($current_user_second_name) { ?>
                                        <p class='first-and-second-names'><?= $current_user_second_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<?= $current_user_username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else { ?>
                                        <div>
                                            <p class="username without-first-and-second-names" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<span><?= $current_user_username ?></span></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($current_user_description != '') { ?>
                                        <p class="description"><?= $current_user_description ?></p>
                                    <?php } else { ?>
                                        <a class="description" href="./edit">Добавить описание</a>
                                    <?php } ?>
                                </div>
                                <div class='div-show-three-dots-popup main-in-profile' onclick='showPopupUserInfo(<?= $current_user_id ?>)' id='div-show-three-dots-popup_<?= $current_user_id ?>'>
                                    <img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>
                                </div>
                                <div class='three-dots-popup' id='three-dots-popup_user-info'>
                                    <span class='three-dots-popup-li copy-link' onclick='copyLinkToUser("<?= $current_user_username ?>")'>Копировать ссылку</span>
                                    <a class='three-dots-popup-li edit-profile' href='edit'>Редактировать</a>
                                    <a class='three-dots-popup-li exit-profile' href='exit'>Выйти</a>
                                </div>
                            </div>
                        </div>
                        <a class="profile__edit" href="./edit">
                            <div>
                                <img src="pics/EditProfileIcon.svg">
                                <p>Редактировать</p>
                            </div>
                            <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z"></path>
                            </svg>
                        </a>
                        <a href="./blossom" class="blossom-level mobile">
                            <div class="blossom-title">
                                <img src="pics/BlossomIcon.svg">
                                Цветение
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                            </div>
                            <div class="progress-div">
                                <progress value="<?= $current_user_blossom_progress ?>" max="100"></progress>
                                <span class="progress" style="--r:<?= $current_user_blossom_progress ?>%"><?= $current_user_blossom_progress ?>%</span>
                            </div>
                            <div class="level">
                                <span><?= $current_user_blossom_level ?> уровень</span>
                                <span><?= $current_user_blossom_level + 1 ?></span>
                            </div>
                        </a>
                        <a href="./trophies" class="case mobile">
                            <div class="case-title">
                                <img src="pics/CaseIcon.svg">
                                Трофеи
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                            </div>
                            <div class="case-trophies">
                                <?php if ($result_current_user_trophies_list_mobile->num_rows > 0) {
                                    while ($row = $result_current_user_trophies_list_mobile->fetch_assoc()) {
                                        $trophy_name_m = $row["name"];
                                        $trophy_description_m = $row["description"];
                                        $trophy_image_m = $row["image"];
                                        echo "<div class='trophy'>";
                                        echo "<img src='$trophy_image_m'>";
                                        echo "<span>$trophy_name_m</span>";
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
                                        <span><?= $result_friend->num_rows ?></span>
                                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                        </svg>
                                    </div>
                                </div>
                                <?php if (($result_friend->num_rows > 0)) {
                                    echo "<div class='friends'>";
                                    if ($result_friend->num_rows > 0) {
                                        while ($row_friend = $result_friend->fetch_assoc()) {
                                            $other_user_id = $row_friend["user_id"];
                                            $other_user_in_top = findUserPositionInTop($other_user_id, $connect);
                                            $other_user_username = $row_friend["user_username"];
                                            $other_user_first_name = $row_friend["user_first_name"];
                                            $other_user_second_name = $row_friend["user_second_name"];
                                            $other_user_avatar = $row_friend["user_avatar"];
                                            echo "<a class='current-friend' href='./user/$other_user_username'>";
                                            echo "<img class='friend-avatar' src='uploads/avatar/thin_$other_user_avatar'>";
                                            echo "<div class='friend-name-and-status'>";
                                            if ($other_user_first_name) {
                                                echo $other_user_username == 'rampus' || $other_user_username == 'help' ? "<p class='rampus'>$other_user_first_name</p>" : "<p>$other_user_first_name</p>";
                                            } else if ($other_user_second_name) {
                                                echo $other_user_username == 'rampus' || $other_user_username == 'help' ? "<p class='rampus'>$other_user_second_name</p>" : "<p>$friend_second_name</p>";
                                            } else if ($other_user_username) {
                                                echo $other_user_username == 'rampus' || $other_user_username == 'help' ? "<p class='rampus'>@$other_user_username</p>" : "<p>@$other_user_username</p>";
                                            }
                                            require('components/other-users-status.php');
                                            echo "</div>";
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
                                            <span> <?= $current_user_posts_counter ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__likes">
                                            Лайки
                                            <span><?= $current_user_likes_counter ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Комментарии
                                            <span><?= $current_user_comments_counter ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile__user-posts">
                                <div id="yours-posts-div">
                                    <fieldset class="show-user-posts-mode-fieldset" id="watch-user-posts-mode-fieldset">
                                        <legend>Режим отображения постов</legend>
                                        <div>
                                            <input type="radio" id="show-posts" name="watch-user-posts-mode-fieldset" value="show-posts" checked="">
                                            <label for="show-posts" id="mode__show-posts"><svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z" stroke-width="2.5"></path>
                                                </svg>
                                                Посты</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="show-reposts" name="watch-user-posts-mode-fieldset" value="show-reposts">
                                            <label for="show-reposts" id="mode__show-reposts"><svg width="27" height="22" viewBox="0 0 27 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Репосты</label>
                                        </div>
                                    </fieldset>
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
                                    <li>
                                        <a href="./wall">
                                            <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                                                <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                                            </svg>
                                            Стена
                                        </a>
                                    </li>
                                    <li>
                                        <a href="./users">
                                            <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                                <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                            </svg>
                                            Люди
                                        </a>
                                    </li>
                                    <li>
                                        <a href="./trophies">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                                            </svg>
                                            Трофеи
                                        </a>
                                    </li>
                                    <li id="active">
                                        <a href="./profile">
                                            <!-- <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg> -->
                                            <img class="menu-avatar" src="uploads/avatar/thin_<?= $current_user_avatar ?>">
                                            Профиль
                                        </a>
                                    </li>
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
                                        <progress value="<?= $current_user_blossom_progress ?>" max="100"></progress>
                                        <span class="progress" style="--r:<?= $current_user_blossom_progress ?>%"><?= $current_user_blossom_progress ?>%</span>
                                    </div>
                                    <div class="level">
                                        <span><?= $current_user_blossom_level ?> уровень</span>
                                        <span><?= $current_user_blossom_level + 1 ?></span>
                                    </div>
                                </a>
                                <a href="./trophies" class="case">
                                    <div class="case-title">
                                        <img src="pics/CaseIcon.svg">
                                        Трофеи
                                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                        </svg>
                                    </div>
                                    <div class="case-trophies">
                                        <?php if ($result_current_user_trophies_list->num_rows > 0) {
                                            while ($row = $result_current_user_trophies_list->fetch_assoc()) {
                                                $trophy_name = $row["name"];
                                                $trophy_description = $row["description"];
                                                $trophy_image = $row["image"];
                                                echo "<div class='trophy'>";
                                                echo "<img src='$trophy_image'>";
                                                echo "<span>$trophy_name</span>";
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
                                            <span> <?= $current_user_posts_counter ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__likes">
                                            Лайки
                                            <span><?= $current_user_likes_counter ?></span>
                                        </div>
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Комментарии
                                            <span><?= $current_user_comments_counter ?></span>
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
<script src="js/main.js?v=250"></script>
<script src="js/profile.js?v=250"></script>
</body>

</html>