<?php
session_start();

require('connect.php');
require('like-or-dislike.php');
require('ratingtrophies.php');

if (isset($_SESSION['user'])) {
    $id = $_SESSION['user']['id'];
    $result_friend_1 = $connect->query("SELECT user_id_1 FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $id");
    $result_friend_2 = $connect->query("SELECT user_id_2 FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $id");
    $friends_id = array();
    if ($result_friend_1->num_rows > 0) {
        while ($row_friend = $result_friend_1->fetch_assoc()) {
            $friends_id[] = $row_friend['user_id_1'];
        }
    }
    if ($result_friend_2->num_rows > 0) {
        while ($row_friend = $result_friend_2->fetch_assoc()) {
            $friends_id[] = $row_friend['user_id_2'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=200">
    <link rel="stylesheet" href="css/wall.css?v=200">
    <title>Посты на стене в Rampus (Рампус)</title>
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
        <h1 class="title">Посты на стене в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=wall");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile">
                                <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' clip-rule='evenodd' d='M10.6852 0.137016C10.3343 0 9.88958 0 9.00001 0C8.11043 0 7.66569 0 7.31479 0.137016C6.84701 0.319707 6.47535 0.670122 6.28158 1.11117C6.19313 1.31251 6.1585 1.54665 6.14496 1.88819C6.12506 2.39011 5.85204 2.8547 5.39068 3.10584C4.92933 3.35696 4.36608 3.34759 3.89509 3.11288C3.57459 2.95317 3.34221 2.86436 3.11304 2.83592C2.61103 2.7736 2.10333 2.90186 1.70162 3.19248C1.40034 3.41044 1.17795 3.7736 0.733186 4.49991L0.733167 4.49994L0.733124 4.50001C0.288375 5.2263 0.0659982 5.58944 0.0164284 5.94442C-0.0496573 6.41772 0.08638 6.89639 0.394624 7.27515C0.53531 7.44804 0.733043 7.5933 1.03993 7.7751C1.49108 8.0424 1.78135 8.49771 1.78133 9C1.7813 9.50229 1.49103 9.95751 1.03992 10.2247C0.732995 10.4066 0.535234 10.552 0.394529 10.7248C0.0862847 11.1036 -0.0497527 11.5822 0.0163426 12.0555C0.0659044 12.4105 0.288293 12.7737 0.733071 13.5L0.73355 13.5008C1.17802 14.2266 1.40036 14.5896 1.70152 14.8074C2.10323 15.098 2.61093 15.2263 3.11295 15.164C3.3421 15.1356 3.57447 15.0467 3.89494 14.8871C4.36596 14.6524 4.92925 14.643 5.39064 14.8941C5.85203 15.1453 6.12506 15.6099 6.14496 16.1119C6.15851 16.4534 6.19313 16.6875 6.28158 16.8889C6.39517 17.1474 6.56988 17.3748 6.78926 17.5555C7.30922 16.6913 7.93605 15.8862 8.66116 15.1611C9.51192 14.3104 10.4728 13.5949 11.5102 13.0285C11.1816 12.2513 11 11.3969 11 10.5C11 7.08819 13.6286 4.29036 16.9711 4.02119C16.7064 3.60165 16.5246 3.35618 16.2985 3.19254C15.8968 2.90192 15.389 2.77366 14.887 2.83598C14.6579 2.86442 14.4256 2.95322 14.105 3.11292C13.634 3.34763 13.0707 3.35702 12.6094 3.10586C12.1479 2.85472 11.8749 2.39009 11.8551 1.88815C11.8415 1.54663 11.8069 1.3125 11.7184 1.11117C11.5247 0.670122 11.153 0.319707 10.6852 0.137016ZM14.1455 13.4996C14.109 13.4588 14.0732 13.4173 14.0382 13.3752Z' />
                                    <path d='M21.25 10.5C21.25 12.5711 19.5711 14.25 17.5 14.25C15.4289 14.25 13.75 12.5711 13.75 10.5C13.75 8.42893 15.4289 6.75 17.5 6.75C19.5711 6.75 21.25 8.42893 21.25 10.5ZM10.6057 17.1057C11.2822 16.4292 12.0479 15.8623 12.8752 15.4166C14.0826 16.5527 15.7103 17.25 17.5 17.25C19.2897 17.25 20.9174 16.5527 22.1248 15.4166C22.9521 15.8623 23.7177 16.4292 24.3943 17.1057C26.0452 18.7566 27.0429 20.9385 27.2211 23.25H17.5L7.77887 23.25C7.95711 20.9385 8.95483 18.7566 10.6057 17.1057Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>Профиль</a></li>
                        <li id="active">
                            <div class='wall-filter-div'>
                                <div class='wall-filter' id='wall-filter'>
                                    <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                        <path fill-rule='evenodd' clip-rule='evenodd' d='M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z' />
                                        <path d='M12 7H23C25.2091 7 27 8.79086 27 11V23H12C9.79086 23 8 21.2091 8 19V11C8 8.79086 9.79086 7 12 7Z' stroke-linecap='round' stroke-linejoin='round' />
                                    </svg>
                                    Стена
                                </div>
                                <div class="wall-filter__choice">
                                    <label class="wall-filter-popup-li">Все<input checked name="wall-filter" id="wall-filter-all" type="radio" value=""></label>
                                    <label class="wall-filter-popup-li <?php if (($result_friend_1->num_rows + $result_friend_2->num_rows) == 0) {
                                                                            echo "no-friends";
                                                                        } ?>">Друзья<input name="wall-filter" id="wall-filter-friends" type="radio" value=""></label>
                                </div>
                            </div>
                        </li>
                        <li><a href="./people"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>Люди</a></li>
                        <li><a href="./friends"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linejoin='round' />
                                </svg>
                                Друзья</a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="wall__new-post">
                            <form action="./add" method="post" autocomplete="off">
                                <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                <label for="textarea-post" id="textarea-post_label">О чём расскажете сегодня?</label>
                                <input type="hidden" required name="post" id="textarea-post_input" value="">
                                <input type="hidden" required name="post-source" value="source-wall">
                                <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) {
                                                                                            echo $_GET['search'];
                                                                                        } ?>">
                                <button disabled class="" type="submit" id="textarea-post_sumbit"><img src="pics/SendIcon.svg"></button>
                            </form>
                        </div>
                        <div class="wall-filter-mobile" id="wall-filter-mobile">
                            <div class="wall-filter-mobile-info" id='wall-filter-mobile-info' onclick='showPopupWallFilterMobile()'>
                                <span>
                                    Все
                                </span>
                                <svg width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                </svg>
                            </div>
                            <div class='wall-filter-popup-mobile' id='popup_wall-filter-mobile'>
                                <label class="wall-filter-popup-li-mobile">Все<input checked name="wall-filter-mobile" id="wall-filter-all-mobile" type="radio" value=""></label>
                                <div class='div-line'></div>
                                <label class="wall-filter-popup-li-mobile <?php if (($result_friend_1->num_rows + $result_friend_2->num_rows) == 0) {
                                                                                echo "no-friends";
                                                                            } ?>">Друзья<input name="wall-filter-mobile" id="wall-filter-friends-mobile" type="radio" value=""></label>
                            </div>
                        </div>
                        <div class="wall__user-posts" id="posts-filter-all">
                            <div>
                                <?php
                                if (!isset($_GET['search'])) {
                                    $search = 'all';
                                } else {
                                    $search = $_GET['search'];
                                }
                                $search_condition = $search !== 'all' ? "AND hashtags.name = '$search'" : '';
                                $sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%e %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i, users.username AS username
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                            WHERE posts.status = 0 $search_condition";

                                $result_post = $connect->query($sql_post);
                                if ($result_post->num_rows > 0) {
                                    while ($row_post = $result_post->fetch_assoc()) {
                                        $hashtag_id = $row_post["hashtag_id"];
                                        $post_text = preg_replace('/\xc2\xa0/', ' ', $row_post["post_text"]);
                                        $post_date = $row_post["post_date"];
                                        $post_likes = $row_post["post_likes"];
                                        $user_id = $row_post["user_id"];
                                        $first_name = $row_post["first_name"];
                                        $second_name = $row_post["second_name"];
                                        $username = $row_post["username"];
                                        $avatar = $row_post["avatar"];
                                        $i = $row_post['i'];
                                        $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
                                        $result_top = $connect->query($sql_top);
                                        $top_count = 0;
                                        if ($result_top->num_rows > 0) {
                                            while ($row = $result_top->fetch_assoc()) {
                                                $current_id = $row["id"];
                                                $top_count += 1;
                                                if ($user_id == $current_id) {
                                                    break;
                                                }
                                            }
                                        }
                                        echo "<div class='user-post' id='post-$i'>";
                                        echo "<div>";
                                        echo "<div class='wall__user-info'>";
                                        echo "<img class='avatar' src='uploads/avatar/thin_" . $avatar . "'>";
                                        echo "<div>";
                                        if ($user_id == $_SESSION['user']['id']) {
                                            if ($username == 'rampus') {
                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                            } else {
                                                switch ($top_count) {
                                                    case 1:
                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                        break;
                                                    case 2:
                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                        break;
                                                    case 3:
                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                        break;
                                                    default:
                                                        echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                }
                                            }
                                        } else {
                                            if ($username == 'rampus') {
                                                echo "<a href='./user/$username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                            } else {
                                                switch ($top_count) {
                                                    case 1:
                                                        echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                        break;
                                                    case 2:
                                                        echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                        break;
                                                    case 3:
                                                        echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                        break;
                                                    default:
                                                        echo "<a href='./user/$username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                }
                                            }
                                        }
                                        echo "<span>" . $post_date . "</span>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "<img onclick='showPopup($i)' src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
                                        echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                        echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
                                        echo "<a class='three-dots-popup-li open-profile' href='./user/$username'>Открыть профиль</a>";
                                        if ($user_id == $_SESSION['user']['id']) {
                                            // echo "<a class='three-dots-popup-li edit-post' href='./wall'>*************</a>";
                                            echo "<a class='three-dots-popup-li delete-post' href='deletepost?post=$i&source=wall'>Удалить</a>";
                                        }
                                        echo "</div>";
                                        echo "</div>";
                                        if ($hashtag_id != 0) {
                                            $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                                            echo "<p class='main-text'>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                        } else {
                                            echo "<p class='main-text'>" . $post_text . "</p>";
                                        }
                                        echo "<div class='post-buttons'>";
                                        $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%e %M в %k:%i') AS comment_date, users.id AS comment_user_id, users.username AS comment_username
                                        FROM comments
                                        JOIN users ON comments.user_id = users.id
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                        $result_comment = $connect->query($sql_comment);
                                        $rows_num_comment = $result_comment->num_rows;
                                        $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $_SESSION['user']['id'];
                                        $result_like = $connect->query($sql_like);
                                        if ($result_like->num_rows > 0) {
                                            echo "<button id='$i' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            if ($post_likes == 1) {
                                                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                                            } else {
                                                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            }
                                        } else {
                                            if ($post_likes == 0) {
                                                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                                            } else {
                                                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            }
                                            echo "<button id='$i' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                        }
                                        if ($rows_num_comment == 0) {
                                            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
                                            </svg>";
                                        } else {
                                            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
                                            </svg>";
                                            echo "<span class='comment-counter'>" . $rows_num_comment . "</span></button>";
                                        }
                                        echo "</div>";
                                        echo "<div class='div-line'></div>";
                                        echo "<div class='wall__comments'>";
                                        if ($rows_num_comment > 0) {
                                            echo "<div class='other-users'>";
                                            $comment_count = 2;
                                            $comment_count_current = $result_comment->num_rows;
                                            while ($row_comment = $result_comment->fetch_assoc()) {
                                                $comment_user_id = $row_comment['comment_user_id'];
                                                $comment_username = $row_comment['comment_username'];
                                                $first_name = $row_comment['first_name'];
                                                $second_name = $row_comment['second_name'];
                                                $avatar = $row_comment['avatar'];
                                                $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                                                $comment_date = $row_comment['comment_date'];
                                                $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
                                                $result_top = $connect->query($sql_top);
                                                $top_count = 0;
                                                if ($result_top->num_rows > 0) {
                                                    while ($row = $result_top->fetch_assoc()) {
                                                        $current_id = $row["id"];
                                                        $top_count += 1;
                                                        if ($comment_user_id == $current_id) {
                                                            break;
                                                        }
                                                    }
                                                }
                                                if ($comment_count_current > 2) {
                                                    if ($comment_count > 0) {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line'></div>";
                                                        }
                                                        echo "<div class='user-comment'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                        $comment_count -= 1;
                                                    } else {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line hide comment_div-line_$i'></div>";
                                                        }
                                                        echo "<div class='user-comment hide comment_user-comment_$i'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                    }
                                                } else {
                                                    if ($rows_num_comment < $result_comment->num_rows) {
                                                        echo "<div class='div-line'></div>";
                                                    }
                                                    echo "<div class='user-comment'>";
                                                    echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                    echo "<div>";
                                                    if ($comment_user_id == $_SESSION['user']['id']) {
                                                        if ($comment_username == 'rampus') {
                                                            echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                        } else {
                                                            switch ($top_count) {
                                                                case 1:
                                                                    echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                    break;
                                                                case 2:
                                                                    echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                    break;
                                                                case 3:
                                                                    echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                    break;
                                                                default:
                                                                    echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        }
                                                    } else {
                                                        if ($comment_username == 'rampus') {
                                                            echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                        } else {
                                                            switch ($top_count) {
                                                                case 1:
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                    break;
                                                                case 2:
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                    break;
                                                                case 3:
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                    break;
                                                                default:
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        }
                                                    }
                                                    echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                    echo "<span class='date'>" . $comment_date . "</span>";
                                                    echo "</div>";
                                                    echo "</div>";
                                                }
                                                $rows_num_comment -= 1;
                                            }
                                            if ($comment_count_current > 2) {
                                                echo "<p class='see-all-comments' onclick='seeAllComments($i)' id='see-all-comments_$i'>Показать все комментарии</p>";
                                            }
                                            echo "</div>";
                                        }
                                        echo "<div class='current-user'>";
                                        echo "<form action='./comment' method='post' autocomplete='off'>
                                        <div contenteditable='true' class='textarea-comment' id='textarea-comment_$i' role='textbox' onkeyup='textareaComment(event, $i)' onkeydown='textareaCommentPlaceholder(event, $i)'></div>
                                        <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_$i'>Ответить..</label>
                                        <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_$i' value=''>
                                        <input type='hidden' name='comment_id' value='$i'>
                                        <button type='submit' id='textarea-comment_submit_$i' class='' disabled><img src='pics/SendIcon.svg'></button>
                                    </form>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="wall__user-posts" id="posts-filter-friends">
                            <div>
                                <?php
                                if (!isset($_GET['search'])) {
                                    $search = 'all';
                                } else {
                                    $search = $_GET['search'];
                                }
                                $search_condition = $search !== 'all' ? "AND hashtags.name = '$search'" : '';
                                $sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%e %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i, users.username AS username
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                            WHERE posts.status = 0 $search_condition";
                                $result_post = $connect->query($sql_post);
                                if ($result_post->num_rows > 0) {
                                    while ($row_post = $result_post->fetch_assoc()) {
                                        $user_id = $row_post["user_id"];
                                        if (in_array($user_id, $friends_id)) {

                                            $hashtag_id = $row_post["hashtag_id"];
                                            $post_text = preg_replace('/\xc2\xa0/', ' ', $row_post["post_text"]);
                                            $post_date = $row_post["post_date"];
                                            $post_likes = $row_post["post_likes"];
                                            $first_name = $row_post["first_name"];
                                            $second_name = $row_post["second_name"];
                                            $username = $row_post["username"];
                                            $avatar = $row_post["avatar"];
                                            $i = $row_post['i'];
                                            $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
                                            $result_top = $connect->query($sql_top);
                                            $top_count = 0;
                                            if ($result_top->num_rows > 0) {
                                                while ($row = $result_top->fetch_assoc()) {
                                                    $current_id = $row["id"];
                                                    $top_count += 1;
                                                    if ($user_id == $current_id) {
                                                        break;
                                                    }
                                                }
                                            }
                                            echo "<div class='user-post' id='post-$i'>";
                                            echo "<div>";
                                            echo "<div class='wall__user-info'>";
                                            echo "<img class='avatar' src='uploads/avatar/thin_" . $avatar . "'>";
                                            echo "<div>";
                                            if ($user_id == $_SESSION['user']['id']) {
                                                if ($username == 'rampus') {
                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                } else {
                                                    switch ($top_count) {
                                                        case 1:
                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                            break;
                                                        case 2:
                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                            break;
                                                        case 3:
                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                            break;
                                                        default:
                                                            echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                    }
                                                }
                                            } else {
                                                if ($username == 'rampus') {
                                                    echo "<a href='./user/$username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                } else {
                                                    switch ($top_count) {
                                                        case 1:
                                                            echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                            break;
                                                        case 2:
                                                            echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                            break;
                                                        case 3:
                                                            echo "<a href='./user/$username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                            break;
                                                        default:
                                                            echo "<a href='./user/$username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                    }
                                                }
                                            }
                                            echo "<span>" . $post_date . "</span>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "<img onclick='showPopup($i)' src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
                                            echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                            echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
                                            echo "<a class='three-dots-popup-li open-profile' href='./user/$username'>Открыть профиль</a>";
                                            if ($user_id == $_SESSION['user']['id']) {
                                                // echo "<a class='three-dots-popup-li edit-post' href='./wall'>*************</a>";
                                                echo "<a class='three-dots-popup-li delete-post' href='deletepost?post=$i&source=wall'>Удалить</a>";
                                            }
                                            echo "</div>";
                                            echo "</div>";
                                            if ($hashtag_id != 0) {
                                                $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                                                echo "<p class='main-text'>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                            } else {
                                                echo "<p class='main-text'>" . $post_text . "</p>";
                                            }
                                            echo "<div class='post-buttons'>";
                                            $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%e %M в %k:%i') AS comment_date, users.id AS comment_user_id, users.username AS comment_username
                                        FROM comments
                                        JOIN users ON comments.user_id = users.id
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                            $result_comment = $connect->query($sql_comment);
                                            $rows_num_comment = $result_comment->num_rows;
                                            $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $_SESSION['user']['id'];
                                            $result_like = $connect->query($sql_like);
                                            if ($result_like->num_rows > 0) {
                                                echo "<button id='$i' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            } else {
                                                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                                echo "<button id='$i' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            }
                                            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                        <path d='M0 5C0 2.23858 2.23858 0 5 0L23 0C25.7614 0 28 2.23858 28 5L28 24L5 24C2.23858 24 0 21.7614 0 19L0 5Z' />
                                        </svg>";
                                            echo "<span class='comment-counter'>" . $rows_num_comment . "</span></button>";
                                            echo "</div>";
                                            echo "<div class='div-line'></div>";
                                            echo "<div class='wall__comments'>";
                                            if ($rows_num_comment > 0) {
                                                echo "<div class='other-users'>";
                                                $comment_count = 2;
                                                $comment_count_current = $result_comment->num_rows;
                                                while ($row_comment = $result_comment->fetch_assoc()) {
                                                    $comment_user_id = $row_comment['comment_user_id'];
                                                    $comment_username = $row_comment['comment_username'];
                                                    $first_name = $row_comment['first_name'];
                                                    $second_name = $row_comment['second_name'];
                                                    $avatar = $row_comment['avatar'];
                                                    $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                                                    $comment_date = $row_comment['comment_date'];
                                                    $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
                                                    $result_top = $connect->query($sql_top);
                                                    $top_count = 0;
                                                    if ($result_top->num_rows > 0) {
                                                        while ($row = $result_top->fetch_assoc()) {
                                                            $current_id = $row["id"];
                                                            $top_count += 1;
                                                            if ($comment_user_id == $current_id) {
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    if ($comment_count_current > 2) {
                                                        if ($comment_count > 0) {
                                                            if ($rows_num_comment < $result_comment->num_rows) {
                                                                echo "<div class='div-line'></div>";
                                                            }
                                                            echo "<div class='user-comment'>";
                                                            echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                            echo "<div>";
                                                            if ($comment_user_id == $_SESSION['user']['id']) {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    switch ($top_count) {
                                                                        case 1:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                            break;
                                                                        case 2:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                            break;
                                                                        case 3:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                            break;
                                                                        default:
                                                                            echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                    }
                                                                }
                                                            } else {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    switch ($top_count) {
                                                                        case 1:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                            break;
                                                                        case 2:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                            break;
                                                                        case 3:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                            break;
                                                                        default:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                    }
                                                                }
                                                            }
                                                            echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                            echo "<span class='date'>" . $comment_date . "</span>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                            $comment_count -= 1;
                                                        } else {
                                                            if ($rows_num_comment < $result_comment->num_rows) {
                                                                echo "<div class='div-line hide comment_div-line_$i'></div>";
                                                            }
                                                            echo "<div class='user-comment hide comment_user-comment_$i'>";
                                                            echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                            echo "<div>";
                                                            if ($comment_user_id == $_SESSION['user']['id']) {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    switch ($top_count) {
                                                                        case 1:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                            break;
                                                                        case 2:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                            break;
                                                                        case 3:
                                                                            echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                            break;
                                                                        default:
                                                                            echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                    }
                                                                }
                                                            } else {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    switch ($top_count) {
                                                                        case 1:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                            break;
                                                                        case 2:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                            break;
                                                                        case 3:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                            break;
                                                                        default:
                                                                            echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                    }
                                                                }
                                                            }
                                                            echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                            echo "<span class='date'>" . $comment_date . "</span>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                        }
                                                    } else {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line'></div>";
                                                        }
                                                        echo "<div class='user-comment'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./profile' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                switch ($top_count) {
                                                                    case 1:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomFirstIcon.svg'></a>";
                                                                        break;
                                                                    case 2:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomSecondIcon.svg'></a>";
                                                                        break;
                                                                    case 3:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names user-from-top'>" . $first_name . " " . $second_name . "<img src='pics/BlossomThirdIcon.svg'></a>";
                                                                        break;
                                                                    default:
                                                                        echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                    }
                                                    $rows_num_comment -= 1;
                                                }
                                                if ($comment_count_current > 2) {
                                                    echo "<p class='see-all-comments' onclick='seeAllComments($i)' id='see-all-comments_$i'>Показать все комментарии</p>";
                                                }
                                                echo "</div>";
                                            }
                                            echo "<div class='current-user'>";
                                            echo "<form action='./comment' method='post' autocomplete='off'>
                                        <div contenteditable='true' class='textarea-comment' id='textarea-comment_$i' role='textbox' onkeyup='textareaComment(event, $i)' onkeydown='textareaCommentPlaceholder(event, $i)'></div>
                                        <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_$i'>Ответить..</label>
                                        <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_$i' value=''>
                                        <input type='hidden' name='comment_id' value='$i'>
                                        <button type='submit' id='textarea-comment_submit_$i' class='' disabled><img src='pics/SendIcon.svg'></button>
                                        </form>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li id="active"><a href="./wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                            <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                        </svg>
                                        Стена
                                    </a></li>
                                <li><a href="./wall">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        ****</a></li>
                                <li><a href="./people">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                        </svg>
                                        Люди</a></li>
                                <li><a href="./profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <div>
                                <input type="text" name="search-hashtag" id="search-hashtag" placeholder="Поиск">
                                <input type="hidden" name="get-status" id="get-status" value=<?php if (isset($_GET['search'])) {
                                                                                                    echo $_GET['search'];
                                                                                                } else {
                                                                                                    echo null;
                                                                                                } ?>>
                                <img id="icon-search-hashtag" src="pics/SearchIcon.svg">
                            </div>
                            <ul id="success-search-hashtag">
                            </ul>
                        </div>
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=200"></script>
<script src="js/wall.js?v=200"></script>
</body>

</html>