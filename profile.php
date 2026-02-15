<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require_once('back-files/like-or-dislike.php');
    require_once('back-files/find-user-position-in-top.php');
    require_once('back-files/get-user-friends.php');

    $current_user_id = $_SESSION['user']['id'];
    $current_user_username = $_SESSION['user']['username'];
    $current_user_description = $_SESSION['user']['description'];
    $current_user_first_name = $_SESSION['user']['first_name'];
    $current_user_second_name = $_SESSION['user']['second_name'];
    $current_user_avatar = $_SESSION['user']['avatar'];
    $other_user_plat_status = $_SESSION['user']['plat_status'];
    $current_user_unrated_status = $_SESSION['user']['unrated_status'];

    $result_current_user_blossom = $connect->query("SELECT blossom_level, blossom_progress FROM users WHERE id = $current_user_id LIMIT 1");
    if ($result_current_user_blossom->num_rows > 0) {
        $row_current_user_blossom = $result_current_user_blossom->fetch_assoc();
        $current_user_blossom_level = $row_current_user_blossom["blossom_level"];
        $current_user_blossom_progress = $row_current_user_blossom["blossom_progress"];
    }

    $sql_current_user_posts_and_likes_counter = "SELECT IF(SUM(posts.likes), SUM(posts.likes), 0) AS current_user_likes_counter, IF(SUM(posts.reposts), SUM(posts.reposts), 0) AS current_user_reposts_counter, COUNT(*) AS current_user_posts_counter
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $current_user_id";
    $result_current_user_posts_and_likes_counter = $connect->query($sql_current_user_posts_and_likes_counter);
    if ($result_current_user_posts_and_likes_counter->num_rows > 0) {
        $row_current_user_posts_and_likes_counter = $result_current_user_posts_and_likes_counter->fetch_assoc();
        $current_user_posts_counter = $row_current_user_posts_and_likes_counter["current_user_posts_counter"];
        $current_user_likes_counter = $row_current_user_posts_and_likes_counter["current_user_likes_counter"];
        $current_user_reposts_counter = $row_current_user_posts_and_likes_counter["current_user_reposts_counter"];
    }

    $sql_current_user_comments_counter = "SELECT 1
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    WHERE posts.user_id = $current_user_id";
    $current_user_comments_counter = $connect->query($sql_current_user_comments_counter)->num_rows;

    $sql_current_user_trophies_list = "SELECT name, description, image FROM trophies WHERE user_id_to = $current_user_id";
    $result_current_user_trophies_list = $connect->query($sql_current_user_trophies_list);
    $result_current_user_trophies_list_mobile = $connect->query($sql_current_user_trophies_list);

    $sql_current_user_personal_trophies_list = "SELECT t.name, t.description, t.image, stfu.unique_number FROM sponsored_trophies_from_users stfu JOIN trophies t ON stfu.trophy_id = t.id WHERE stfu.user_id = $current_user_id";
    $result_current_user_personal_trophies_list = $connect->query($sql_current_user_personal_trophies_list);
    $result_current_user_personal_trophies_list_mobile = $connect->query($sql_current_user_personal_trophies_list);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=310">
    <link rel="stylesheet" href="css/profile.css?v=310">
    <title>Профиль в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
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
                                    <?php if ($current_user_first_name || $current_user_second_name) { ?>
                                        <div class="f-and-s-names-and-plat">
                                            <p class='first-and-second-names'><?= $current_user_first_name . " " . $current_user_second_name ?></p>
                                            <?php require('components/plat-status.php'); ?>
                                        </div>
                                        <div>
                                            <p class="username" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<?= $current_user_username ?></p>
                                            <span id="copy-link-status">Копировать ссылку</span>
                                        </div>
                                    <?php } else { ?>
                                        <div>
                                            <div class="f-and-s-names-and-plat">
                                                <p class="username without-first-and-second-names" onclick='copyLinkToUserAddReturnMessage("<?= $current_user_username ?>")'>@<span><?= $current_user_username ?></span></p>
                                                <?php require('components/plat-status.php'); ?>
                                            </div>
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
                        <?php if (!$current_user_unrated_status) { ?>
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
                                    <?php if ($result_current_user_personal_trophies_list_mobile->num_rows > 0) {
                                        while ($row = $result_current_user_personal_trophies_list_mobile->fetch_assoc()) {
                                            $trophy_name_m = $row["name"];
                                            $trophy_unique_number_m = $row["unique_number"];
                                            $trophy_image_m = $row["image"];
                                            echo "<div class='trophy'>";
                                            echo "<img src='$trophy_image_m'>";
                                            echo "<span>$trophy_name_m #$trophy_unique_number_m</span>";
                                            echo "</div>";
                                        }
                                    }
                                    if ($result_current_user_trophies_list_mobile->num_rows > 0) {
                                        while ($row = $result_current_user_trophies_list_mobile->fetch_assoc()) {
                                            $trophy_name_m = $row["name"];
                                            $trophy_description_m = $row["description"];
                                            $trophy_image_m = $row["image"];
                                            echo "<div class='trophy'>";
                                            echo "<img src='$trophy_image_m'>";
                                            echo "<span>$trophy_name_m</span>";
                                            echo "</div>";
                                        }
                                    } else if ($result_current_user_personal_trophies_list_mobile->num_rows == 0 && $result_current_user_trophies_list_mobile->num_rows == 0) {
                                        echo "<span class='trophy'>Нет трофеев</span>";
                                    }
                                    ?>
                                </div>
                            </a>
                        <?php } ?>
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
                                            $other_user_verify_status = $row_friend["user_verify_status"];
                                            echo "<a class='current-friend' href='./user/$other_user_username'>";
                                            echo "<img class='friend-avatar' src='uploads/avatar/thin_$other_user_avatar'>";
                                            echo "<div class='friend-name-and-status'>";
                                            if ($other_user_first_name) {
                                                echo $other_user_verify_status ? "<p class='rampus'>$other_user_first_name</p>" : "<p>$other_user_first_name</p>";
                                            } else if ($other_user_second_name) {
                                                echo $other_user_verify_status ? "<p class='rampus'>$other_user_second_name</p>" : "<p>$friend_second_name</p>";
                                            } else if ($other_user_username) {
                                                echo $other_user_verify_status ? "<p class='rampus'>@$other_user_username</p>" : "<p>@$other_user_username</p>";
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
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Репосты
                                            <span><?= $current_user_reposts_counter ?></span>
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
                                </div>
                                <div id="success-render-posts">
                                </div>
                                <svg id="profile-loading-main" class="loading" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z" />
                                </svg>
                            </div>
                            <?php require_once('components/mobile-main-menu.php'); ?>
                        </div>
                        <div class="third-part">
                            <div>
                                <?php if (!$current_user_unrated_status) { ?>
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
                                            <?php if ($result_current_user_personal_trophies_list->num_rows > 0) {
                                                while ($row = $result_current_user_personal_trophies_list->fetch_assoc()) {
                                                    $trophy_name = $row["name"];
                                                    $trophy_unique_number = $row["unique_number"];
                                                    $trophy_image = $row["image"];
                                                    echo "<div class='trophy'>";
                                                    echo "<img src='$trophy_image'>";
                                                    echo "<span>$trophy_name #$trophy_unique_number</span>";
                                                    echo "</div>";
                                                }
                                            }
                                            if ($result_current_user_trophies_list->num_rows > 0) {
                                                while ($row = $result_current_user_trophies_list->fetch_assoc()) {
                                                    $trophy_name = $row["name"];
                                                    $trophy_description = $row["description"];
                                                    $trophy_image = $row["image"];
                                                    echo "<div class='trophy'>";
                                                    echo "<img src='$trophy_image'>";
                                                    echo "<span>$trophy_name</span>";
                                                    echo "</div>";
                                                }
                                            } else if ($result_current_user_personal_trophies_list->num_rows == 0 && $result_current_user_trophies_list->num_rows == 0) {
                                                echo "<span class='trophy'>Нет трофеев</span>";
                                            }
                                            ?>
                                        </div>
                                    </a>
                                <?php } ?>
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
                                        <div class="div-line"></div>
                                        <div class="profile__comments">
                                            Репосты
                                            <span><?= $current_user_reposts_counter ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=310"></script>
<script src="js/profile.js?v=310"></script>
<script src="js/posts-actions-profile.js?v=310"></script>
</body>

</html>