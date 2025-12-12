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
            $current_avatar = $row["avatar"];
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

    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;
    $user_level = intval($blossom);
    $user_progress = round($blossom - $user_level, 2) * 100;

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

    $sql_trophies = "SELECT * FROM trophies WHERE user_id_to = $id";
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
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/profile.css?v=250">
    <title>Профиль в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
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
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <div class="profile-back">
                                <svg width="100%" height="100%" overflow='visible' xmlns:xlink='http://www.w3.org/1999/xlink'>
                                    <defs overflow='visible'>
                                        <pattern id="pattern-bg-user" x="0" y="0" width="42" height="42" overflow='visible' viewBox="0 0 42 56" patternUnits="userSpaceOnUse">
                                            <path d="M3.66912 5.18399C3.45697 4.27252 4.27252 3.45697 5.18399 3.66912L5.49178 3.74076C6.23299 3.91328 6.95574 3.3978 7.03307 2.64147L7.06517 2.32741C7.16026 1.39736 8.19726 0.891631 8.99138 1.38803L9.25954 1.55565C9.90532 1.95932 10.757 1.70697 11.0758 1.0175L11.2082 0.731195C11.6002 -0.116648 12.7463 -0.257751 13.337 0.469097L13.5365 0.714542C14.0168 1.30562 14.9052 1.34374 15.4309 0.795834L15.6492 0.568316C16.2957 -0.105444 17.4266 0.13337 17.7499 1.01191L17.8591 1.30857C18.122 2.02301 18.9507 2.34747 19.6264 2.0005L19.9069 1.85642C20.7378 1.42976 21.7311 2.02261 21.7519 2.95763L21.7589 3.27337C21.7759 4.03375 22.4552 4.60939 23.2076 4.50096L23.52 4.45594C24.4452 4.3226 25.1932 5.20524 24.9093 6.09543L24.8135 6.39603C24.5826 7.11994 25.0389 7.88438 25.7865 8.02624L26.0969 8.08515C27.0162 8.25959 27.4378 9.33638 26.88 10.0853L26.6916 10.3381C26.238 10.9471 26.4218 11.8176 27.0836 12.1943L27.3584 12.3508C28.1722 12.8141 28.2217 13.9684 27.4504 14.4948L27.1899 14.6725C26.5627 15.1006 26.4541 15.9827 26.9583 16.5535L27.1677 16.7906C27.7878 17.4926 27.4599 18.5992 26.5587 18.8461L26.2544 18.9295C25.5215 19.1303 25.1322 19.9284 25.4243 20.6315L25.5456 20.9235C25.9048 21.788 25.235 22.7271 24.3015 22.6678L23.9863 22.6477C23.2272 22.5995 22.5995 23.2272 22.6477 23.9863L22.6678 24.3015C22.7271 25.235 21.788 25.9048 20.9235 25.5456L20.6315 25.4243C19.9284 25.1322 19.1303 25.5215 18.9295 26.2544L18.8461 26.5587C18.5992 27.4599 17.4926 27.7878 16.7906 27.1677L16.5535 26.9583C15.9827 26.4541 15.1006 26.5627 14.6725 27.1899L14.4948 27.4504C13.9684 28.2217 12.8141 28.1722 12.3508 27.3584L12.1943 27.0836C11.8176 26.4218 10.9471 26.238 10.3381 26.6916L10.0853 26.88C9.33638 27.4378 8.25959 27.0162 8.08515 26.0969L8.02624 25.7865C7.88438 25.0389 7.11994 24.5826 6.39603 24.8135L6.09543 24.9093C5.20524 25.1932 4.3226 24.4452 4.45594 23.52L4.50096 23.2076C4.60939 22.4552 4.03375 21.7759 3.27337 21.7589L2.95763 21.7519C2.02261 21.7311 1.42976 20.7378 1.85643 19.9069L2.0005 19.6264C2.34747 18.9507 2.02301 18.122 1.30857 17.8591L1.01191 17.7499C0.133369 17.4266 -0.105444 16.2957 0.568316 15.6492L0.795833 15.4309C1.34374 14.9052 1.30562 14.0168 0.714542 13.5365L0.469098 13.337C-0.257751 12.7463 -0.116648 11.6002 0.731195 11.2082L1.0175 11.0758C1.70697 10.757 1.95933 9.90532 1.55565 9.25953L1.38803 8.99137C0.891633 8.19726 1.39736 7.16026 2.32741 7.06517L2.64147 7.03306C3.3978 6.95574 3.91328 6.23299 3.74076 5.49177L3.66912 5.18399Z" fill="#D5A021" />
                                            <path d="M11.2175 8.3326L19.4602 16.5753L17.8312 18.2043L11.3313 11.7044L10.6793 15.0176L8.53278 14.7148L9.4875 10.0626L11.2175 8.3326Z" fill="white" />
                                        </pattern>
                                    </defs>
                                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-bg-user)"></rect>
                                </svg>
                            </div>
                            <div class="profile-userinfo">
                                <img class="avatar" src="uploads/avatar/small_<?= $current_avatar ?>">
                                <div class="textinfo">
                                    <?php if ($first_name && $second_name) { ?>
                                        <p class='first-and-second-names'><?= $first_name . " " . $second_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $username ?>")'>@<?= $username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else if ($first_name) { ?>
                                        <p class='first-and-second-names'><?= $first_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $username ?>")'>@<?= $username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else if ($second_name) { ?>
                                        <p class='first-and-second-names'><?= $second_name ?></p>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $username ?>")'>@<?= $username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else { ?>
                                        <div>
                                            <p class="username without-first-and-second-names" onclick='copyLinkToUserAddReturnMessage("<?= $username ?>")'>@<span><?= $username ?></span></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($description != '') { ?>
                                        <p class="description"><?= $description ?></p>
                                    <?php } else { ?>
                                        <a class="description" href="./edit">Добавить описание</a>
                                    <?php } ?>
                                </div>
                                <div class='div-show-three-dots-popup main-in-profile' onclick='showPopupUserInfo(<?= $id ?>)' id='div-show-three-dots-popup_<?= $id ?>'>
                                    <img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>
                                </div>
                                <div class='three-dots-popup' id='three-dots-popup_user-info'>
                                    <span class='three-dots-popup-li copy-link' onclick='copyLinkToUser("<?= $username ?>")'>Копировать ссылку</span>
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
                                <progress value="<?= $user_progress ?>" max="100"></progress>
                                <span class="progress" style="--r:<?= $user_progress ?>%"><?= $user_progress ?>%</span>
                            </div>
                            <div class="level">
                                <span><?= $user_level ?> уровень</span>
                                <span><?= $user_level + 1 ?></span>
                            </div>
                        </a>
                        <a href="./case" class="case mobile">
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
                                            $friend_id = $row_friend["user_id"];
                                            $friend_in_top = findUserPositionInTop($friend_id, $connect);
                                            $friend_username = $row_friend["user_username"];
                                            $friend_first_name = $row_friend["user_first_name"];
                                            $friend_second_name = $row_friend["user_second_name"];
                                            $friend_avatar = $row_friend["user_avatar"];
                                            echo "<a class='current-friend' href='./user/$friend_username'>";
                                            echo "<img class='friend-avatar' src='uploads/avatar/thin_$friend_avatar'>";
                                            echo "<div class='friend-name-and-status'>";
                                            if ($friend_first_name) {
                                                echo $friend_username == 'rampus' || $friend_username == 'help' ? "<p class='rampus'>$friend_first_name</p>" : "<p>$friend_first_name</p>";
                                            } else if ($friend_second_name) {
                                                echo $friend_username == 'rampus' || $friend_username == 'help' ? "<p class='rampus'>$friend_second_name</p>" : "<p>$friend_second_name</p>";
                                            } else if ($friend_username) {
                                                echo $friend_username == 'rampus' || $friend_username == 'help' ? "<p class='rampus'>@$friend_username</p>" : "<p>@$friend_username</p>";
                                            }
                                            if ($friend_username == 'rampus' || $friend_username == 'help') { ?>
                                                <img class='status' src="pics/SuperUserIcon.svg">
                                    <?php } else {
                                                switch ($friend_in_top) {
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
                                    <!-- <p>Ваши посты<span><?= $posts_counter ?></span></p> -->
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
                                            <img class="menu-avatar" src="uploads/avatar/thin_<?= $current_avatar ?>">
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
                                        <progress value="<?= $user_progress ?>" max="100"></progress>
                                        <span class="progress" style="--r:<?= $user_progress ?>%"><?= $user_progress ?>%</span>
                                    </div>
                                    <div class="level">
                                        <span><?= $user_level ?> уровень</span>
                                        <span><?= $user_level + 1 ?></span>
                                    </div>
                                </a>
                                <a href="./case" class="case">
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
<script src="js/main.js?v=250"></script>
<script src="js/profile.js?v=250"></script>
</body>

</html>