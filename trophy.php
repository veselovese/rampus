<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require('back-files/get-trophy-list.php');

    $user_id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $user_id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
        }
    }

    $user_in_top = findUserPositionInTop($user_id, $connect);

    $unread_posts = $_SESSION['user']['unread_posts'];
    $result_request_to_counter = $result_request_to->num_rows;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/trophy.css?v=250">
    <title>Друзья пользователя в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Друзья в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile" class="menu-profile">
                                <?php if ($username == 'rampus') { ?>
                                    <img class='menu-status' src="pics/SuperUserIcon.svg">
                                <?php } else {
                                    switch ($user_in_top) {
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
                        <li><a href="./users"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Люди</a></li>
                        <li><a href="./friends"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linejoin='round' />
                                </svg>
                                Друзья
                                <?php if ($result_request_to_counter > 0) { ?>
                                    <span class="notification-in-menu"><?= $result_request_to_counter ?></span>
                                <?php } ?>
                            </a></li>
                        <li id="active"><a href="./trophy"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                                </svg>
                                Трофеи
                            </a></li>
                        <p class="menu-title">Возможности</p>
                        <li><a href="./edit">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C13.1861 0 13.7791 0 14.2469 0.182688C14.8706 0.426276 15.3662 0.893496 15.6245 1.48156C15.7425 1.75 15.7887 2.06218 15.8067 2.51753C15.8332 3.18679 16.1972 3.80629 16.8125 4.14115C17.4276 4.47602 18.1787 4.46351 18.8067 4.15056C19.2341 3.93763 19.5439 3.81923 19.8493 3.78131C20.5187 3.69822 21.1957 3.86923 21.7312 4.25672C22.1329 4.54734 22.4295 5.03156 23.0225 6C23.6156 6.96844 23.9121 7.45266 23.9782 7.92596C24.0663 8.55704 23.8849 9.19528 23.4739 9.7002C23.2863 9.93084 23.0226 10.1245 22.6133 10.367C22.0119 10.7233 21.6248 11.3302 21.6248 11.9999C21.6248 12.6696 22.0119 13.2767 22.6133 13.633C23.0225 13.8754 23.2862 14.0692 23.4738 14.2997C23.8848 14.8046 24.0661 15.4429 23.9781 16.074C23.912 16.5473 23.6155 17.0315 23.0223 17.9999C22.4294 18.9684 22.1328 19.4526 21.7311 19.7432C21.1955 20.1307 20.5185 20.3017 19.8492 20.2186C19.5437 20.1806 19.2339 20.0623 18.8067 19.8494C18.1785 19.5365 17.4275 19.5239 16.8124 19.8588C16.1972 20.1937 15.8332 20.8132 15.8067 21.4824C15.7887 21.9378 15.7425 22.25 15.6245 22.5185C15.3662 23.1065 14.8706 23.5738 14.2469 23.8174C13.7791 24 13.1861 24 12 24C10.8139 24 10.2209 24 9.75304 23.8174C9.12934 23.5738 8.63379 23.1065 8.37543 22.5185C8.25749 22.25 8.21134 21.9378 8.19327 21.4825C8.16673 20.8132 7.80269 20.1937 7.18751 19.8588C6.57232 19.524 5.82127 19.5365 5.19325 19.8494C4.76595 20.0623 4.45612 20.1808 4.15059 20.2187C3.48124 20.3017 2.8043 20.1307 2.26869 19.7432C1.86714 19.4528 1.57069 18.9687 0.978058 18.001L0.977427 18C0.38439 17.0316 0.0878725 16.5473 0.02179 16.074C-0.0663368 15.4429 0.115046 14.8048 0.526037 14.2998C0.713644 14.0693 0.977325 13.8755 1.38656 13.633C1.98803 13.2767 2.37506 12.6697 2.3751 12C2.37514 11.3303 1.9881 10.7232 1.38657 10.3668C0.977389 10.1244 0.713746 9.93072 0.526165 9.7002C0.115173 9.19519 -0.0662097 8.55696 0.0219044 7.92589C0.0879974 7.45259 0.384498 6.9684 0.977495 6.00001L0.977554 5.99992L0.977578 5.99988C1.5706 5.03147 1.86711 4.54725 2.26882 4.25664C2.80443 3.86915 3.48137 3.69814 4.15072 3.78122C4.45627 3.81914 4.76612 3.93756 5.19344 4.15051C5.82143 4.46345 6.57243 4.47595 7.18756 4.14112C7.80271 3.80627 8.16673 3.18682 8.19327 2.51759C8.21133 2.0622 8.25749 1.75001 8.37543 1.48156C8.63379 0.893496 9.12934 0.426276 9.75304 0.182688C10.2209 0 10.8139 0 12 0ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79085 8 7.99999 9.79086 7.99999 12C7.99999 14.2091 9.79085 16 12 16Z" />
                                </svg>
                                Настройки
                                <svg class="pointer" width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg></a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="">
                            <p class="main-title">Трофеи</p>
                            <p class='section-title rating'>Рейтинг пользователей</p>
                            <div class='rating-trophies-div'>
                                <div class='trophy-list'>
                                    <?php
                                    $list = getTrophyList();
                                    if ($list->num_rows > 0) {
                                        while ($row = $list->fetch_assoc()) {
                                            $trophy_id = $row['id'];
                                            $trophy_name = $row['name'];
                                            $trophy_desc = $row['description'];
                                            $trophy_image = $row['image'];
                                            $trophy_date = $row['get_date'];
                                            $user_first_name = $row['first_name'];
                                            $user_second_name = $row['second_name'];
                                            $user_id = $row['user_id'];
                                            $user_username = $row['username'];
                                            $user_avatar = $row['avatar'];
                                            $user_level = $row['blossom_level'];
                                            if ($trophy_id < 4) {
                                                $result = $connect->query("SELECT posts.likes AS post_likes FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = $user_id");
                                                $comment_count = $connect->query("SELECT comments.id FROM comments JOIN posts ON comments.post_id = posts.id JOIN users ON users.id = posts.user_id WHERE posts.user_id = $user_id")->num_rows;
                                                $posts_count = $result->num_rows;
                                                $likes_count = 0;
                                                if ($posts_count > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $post_likes = $row["post_likes"];
                                                        $likes_count += $post_likes;
                                                    }
                                                }
                                                $likes_count = (string)$likes_count;
                                                $posts_count = (string)$posts_count;
                                                $comment_count = (string)$comment_count;
                                                echo "<div class='current-trophy'>
                                                <div class='trophy-info'>
                                                <img class='icon' src='$trophy_image'>
                                                <div class='current-trophy-info'>
                                                <p class='name'>$trophy_name</p>
                                                <p class='desc'>$trophy_desc</p>
                                                </div>
                                                </div>
                                                <div class='user-statistic'>Это";
                                                echo "<a href='./users' class='current-static blossom-level'><img src='pics/BlossomIcon.svg'>" . $user_level . " уровень</a>:";
                                                if (($posts_count[-1] == '1') && (!isset($posts_count[-2]) || $posts_count[-2] != '1')) {
                                                    echo "<span class='current-static post'>" . $posts_count . " пост</span>,";
                                                } else if (($posts_count[-1] == '2' || $posts_count[-1] == '3' || $posts_count[-1] == '4') && (!isset($posts_count[-2]) || $posts_count[-2] != '1')) {
                                                    echo "<span class='current-static post'>" . $posts_count . " поста</span>,";
                                                } else {
                                                    echo "<span class='current-static post'>" . $posts_count . " постов</span>,";
                                                }
                                                if (($likes_count[-1] == '1') && (!isset($likes_count[-2]) || $likes_count[-2] != '1')) {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайк</span>и";
                                                } else if (($likes_count[-1] == '2' || $likes_count[-1] == '3' || $likes_count[-1] == '4') && (!isset($likes_count[-2]) || $likes_count[-2] != '1')) {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайка</span>и";
                                                } else {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайков</span>и";
                                                }
                                                if (($comment_count[-1] == '1') && (!isset($comment_count[-2]) || $comment_count[-2] != '1')) {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментарий</span>";
                                                } else if (($comment_count[-1] == '2' || $comment_count[-1] == '3' || $comment_count[-1] == '4') && (!isset($comment_count[-2]) || $comment_count[-2] != '1')) {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментария</span>";
                                                } else {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментариев</span>";
                                                }
                                                echo "</div>
                                                <div class='user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    <img src='uploads/avatar/small_$user_avatar'>
                                                    </a>
                                                    <div class='more-user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    $user_first_name $user_second_name
                                                    </a>
                                                    <span class='date'>владеет <br class='br-mobile'>с $trophy_date</span>
                                                    </div>
                                                </div>
                                                </div>";
                                            } ?>
                                    <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class=''>
                                <p class='section-title'>Основные показатели</p>
                                <div class='trophy-list'>
                                    <?php
                                    $list = getTrophyList();
                                    if ($list->num_rows > 0) {
                                        while ($row = $list->fetch_assoc()) {
                                            $trophy_id = $row['id'];
                                            $trophy_name = $row['name'];
                                            $trophy_desc = $row['description'];
                                            $trophy_stat = $row['stat_number'];
                                            $trophy_image = $row['image'];
                                            $trophy_date = $row['get_date'];
                                            $user_first_name = $row['first_name'];
                                            $user_second_name = $row['second_name'];
                                            $user_id = $row['user_id'];
                                            $user_username = $row['username'];
                                            $user_avatar = $row['avatar'];
                                            if ($trophy_id > 3) {
                                                echo "<div class='current-trophy'>
                                                <div class='trophy-info'>
                                                <img class='icon' src='$trophy_image'>
                                                <div class='current-trophy-info'>
                                                <p class='name'>$trophy_name</p>
                                                <p class='desc'>$trophy_desc</p>
                                                </div>
                                                </div>
                                                <div class='user-statistic'>
                                                <span class='current-static'>$trophy_stat</span>
                                                </div>
                                                <div class='user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    <img src='uploads/avatar/small_$user_avatar'>
                                                    </a>
                                                    <div class='more-user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    $user_first_name $user_second_name
                                                    </a>
                                                    <span class='date'>владеет <br class='br-mobile'>с $trophy_date</span>
                                                    </div>
                                                </div>
                                                </div>";
                                            } ?>
                                    <?php }
                                    }
                                    ?>
                                </div>
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
                                <li id="active">
                                    <a href="./trophy">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                                        </svg>
                                        Трофеи
                                    </a>
                                </li>
                                <li>
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
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=250"></script>
<script src="js/people.js?v=250"></script>
</body>

</html>